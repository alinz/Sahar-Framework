<?php

namespace sf\core;

use sf\core\annotation\exception\ResourceIdDuplicatedException;
use sf\core\util\Util;

Util::mark("sf.core.Context");
Util::mark("sf.core.system.Configuration");
Util::mark("sf.core.annotation.engine.AutoAnno");
Util::mark("sf.core.annotation.implementation.ClassRequestMapping");
Util::mark("sf.core.annotation.implementation.MethodRequestMapping");
Util::mark("sf.core.annotation.implementation.VariableResource");
Util::mark("sf.core.exception.StartApplicationNotFoundException");

use sf\core\annotation\engine\DiscoveryListener;
use sf\core\annotation\implementation\AbstractResource;
use sf\core\system\Configuration;
use sf\core\annotation\engine\AutoAnno;
use sf\core\annotation\implementation\VariableResource;
use sf\core\exception\StartApplicationNotFoundException;

class Core implements Context {

    private $configuration;
    private $annotation;
    private $attributes;

    public function __construct($defaultJsonConfiguration, $localJsonConfiguration) {
        $this->attributes = array();
        $this->configuration = new Configuration($defaultJsonConfiguration, $localJsonConfiguration);

        $this->annotation = new AutoAnno();
    }

    public function start() {
        $this->loadAnnotations();

        $this->annotation->discover($this->configuration->getAutoScanPaths());
        $startObject = &$this->getResourceObject($this->configuration->getStart());
        if ($startObject instanceof StartApplication) {
            $startObject->start();
        } else {
            throw new StartApplicationNotFoundException();
        }
    }

    private function loadAnnotations() {
        $annotations = &$this->configuration->getAnnotations();
        foreach ($annotations as &$annotation) {
            if ($annotation["enable"] === true) {
                $obj = &$this->createInstance($annotation["class"], array());
                if ($obj instanceof DiscoveryListener) {
                    $this->annotation->registerListener($obj);
                }
            }
        }
    }

    private function &createInstance($path, $args) {
        Util::mark($path);
        $classPath = Util::toNamespacePath($path);
        $reflect  = new \ReflectionClass($classPath);
        $object = $reflect->newInstanceArgs($args);
        return $object;
    }

    public function &getResourceObject($id) {
        $resource = &$this->configuration->getResource($id);
        if (isset($resource["object"])) {
            return $resource["object"];
        }

        $argsObject = array();

        $args = &$resource["args"];
        foreach ($args as &$arg) {
            $obj = null;
            if ($arg["type"] === "ref") {
                $obj = &$this->getResourceObject($arg["value"]);
            } else {
                $obj = &$arg["value"];
            }
            array_push($argsObject, $obj);
        }

        //create object.
        $clazz = $resource["class"];
        $object = $this->createInstance($clazz, $argsObject);
        $classPath = Util::toNamespacePath($clazz);

        //Apply and update Resource and Value annotation
        $attributeResources = &$this->getAttribute(AbstractResource::Attribute_Resource_Store);
        if (isset($attributeResources[$classPath])) {
            $resources = &$attributeResources[$classPath];

            foreach ($resources as &$resourceItem) {
                $object->{"set".ucfirst($resourceItem[VariableResource::Property_Rice_Id_Name])}($this->getResourceObject($resourceItem[VariableResource::Property_Field_Name]));
            }
        }

        $attributeValues = &$this->getAttribute(AbstractResource::Attribute_Value_Store);
        if (isset($attributeValues[$classPath])) {
            $values = &$attributeValues[$classPath];

            foreach ($values as &$value) {
                $object->{"set".ucfirst($value[VariableResource::Property_Rice_Id_Name])}($this->getPropertyValue($value[VariableResource::Property_Field_Name]));
            }
        }

        if ($object instanceof ResourceListener) {
            $object->resourceDidLoad($this);
        }

        if ($resource["singleton"] === true) {
            $resource["object"] = $object;
        }

        return $object;
    }

    public function &getPropertyValue($name) {
        return $this->configuration->getProperties($name);
    }

    public function isResourceExist($id) {
        return $this->configuration->isResourceExist($id);
    }

    public function addNewResourceSource($id, $class, $args, $singleton) {
        if ($this->isResourceExist($id)) {
            throw new ResourceIdDuplicatedException("Component $class is registered with duplicated id.");
        }

        $this->configuration->addNewResource($id, $class, $args, $singleton);
    }

    public function isPropertyExist($key) {
        return $this->configuration->isPropertyExist($key);
    }

    public function &getAttribute($key) {
        $result = null;
        if (isset($this->attributes[$key])) {
            $result = &$this->attributes[$key];
        }
        return $result;
    }

    public function setAttribute($key, $value) {
        $this->attributes[$key] = $value;
    }

    private static $instance = null;
    public static function &getInstance($defaultJson = "sf.json", $localJson = "local.json") {
        if (Core::$instance == null) {
            Core::$instance = new Core($defaultJson, $localJson);
        }
        return Core::$instance;
    }

    public function __destruct() { }
}