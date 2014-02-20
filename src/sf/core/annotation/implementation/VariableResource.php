<?php

namespace sf\core\annotation\implementation;

use sf\core\util\Util;

Util::mark("sf.core.annotation.implementation.AbstractResource");
Util::mark("sf.core.annotation.engine.FieldDiscoveryListener");
Util::mark("sf.core.exception.ParameterMissingException");

use sf\core\Core;
use sf\core\annotation\engine\FieldDiscoveryListener;
use sf\core\exception\ParameterMissingException;
use sf\core\exception\ResourceNotFoundException;

class VariableResource extends AbstractResource implements FieldDiscoveryListener {
    const Resource_Name = "Resource";
    const Value_Name = "Value";
    const Id_name = "id";

    const Property_Field_Name = "field";
    const Property_Rice_Id_Name = "id";
    const Property_Value_Name = "value";

    private static $Annotation_Name = array(VariableResource::Resource_Name, VariableResource::Value_Name);
    private $core;

    public function __construct() {
        $this->core = &Core::getInstance();
        $this->core->setAttribute(AbstractResource::Attribute_Resource_Store, array());
        $this->core->setAttribute(AbstractResource::Attribute_Value_Store, array());
    }

    public function getAnnotationsName() {
        return VariableResource::$Annotation_Name;
    }

    public function discovered(&$path, &$class, &$field, &$annotationsMap, $annotationName) {
        if ($annotationName == VariableResource::Resource_Name) {
            $this->updateResource($path, $class, $field, $annotationsMap);
        } else if ($annotationName == VariableResource::Value_Name) {
            $this->updateValue($path, $class, $field, $annotationsMap);
        } else {
            //it shouldn't happen. since this method will be called from internal use.
        }
    }

    private function updateResource(&$path, &$class, &$field, &$annotationsMap) {
        if (isset($annotationsMap[VariableResource::Id_name])) {
            $id = &$annotationsMap[VariableResource::Id_name];
        } else {
            $id = &$field;
        }

        $attribute = &$this->core->getAttribute(AbstractResource::Attribute_Resource_Store);

        if (!isset($attribute[$class])) {
            $attribute[$class] = array();
        }

        $propertyList = &$attribute[$class];

        array_push($propertyList, array(
            VariableResource::Property_Field_Name => $field,
            VariableResource::Property_Rice_Id_Name => $id
        ));
    }

    private function updateValue(&$path, &$class, &$field, &$annotationsMap) {
        if (isset($annotationsMap[VariableResource::Id_name])) {
            $id = &$annotationsMap[VariableResource::Id_name];
        } else {
            $id = &$field;
        }

        //checking if id exists in Properties. if not it will throw an exception
        if (!$this->core->isPropertyExist($id)) {
            throw new ResourceNotFoundException("$id is not found in property list. class name: $class.$field");
        }

        $attribute = &$this->core->getAttribute(AbstractResource::Attribute_Value_Store);

        if (!isset($attribute[$class])) {
            $attribute[$class] = array();
        }

        $propertyList = &$attribute[$class];

        array_push($propertyList, array(
            VariableResource::Property_Field_Name => $field,
            VariableResource::Property_Rice_Id_Name => $id
        ));
    }
}