<?php

namespace sf\core\annotation\implementation;

use sf\core\util\Util;

Util::mark("sf.core.annotation.engine.MethodDiscoveryListener");
Util::mark("sf.core.annotation.implementation.AbstractRequestMapping");
Util::mark("sf.core.annotation.exception.RequestMapTypeDuplicateException");

use sf\core\Core;
use sf\core\annotation\engine\MethodDiscoveryListener;
use sf\core\annotation\exception\RequestMapTypeDuplicateException;

class MethodRequestMapping extends AbstractRequestMapping implements MethodDiscoveryListener {
    private static $Annotation_Name = array("RequestMapping");
    private $core;

    public function __construct() {
        $this->core = &Core::getInstance();
        $this->core->setAttribute(AbstractRequestMapping::Attribute_Request_Mapping_Store, array());
    }

    public function discovered(&$path, &$class, &$method, &$annotationsMap, $annotationName) {
        $attribute = &$this->core->getAttribute(AbstractRequestMapping::Attribute_Class_Prefix_Store);
        $prefix_path = isset($attribute[$class])? $attribute[$class] : "";

        $url = $prefix_path . $annotationsMap[AbstractRequestMapping::Annotation_Value];
        $url = $this->convertToRegExp($url);

        $type = isset($annotationsMap[AbstractRequestMapping::Annotation_Type])? $annotationsMap[AbstractRequestMapping::Annotation_Type] : "GET";

        $this->addNewPath($url, $type, $path, $class, $method);
    }

    public function getAnnotationsName() {
        return MethodRequestMapping::$Annotation_Name;
    }

    private function addNewPath(&$url, &$type, &$path, &$class, &$method) {
        $requestMappingStore = &$this->core->getAttribute(AbstractRequestMapping::Attribute_Request_Mapping_Store);

        if (!isset($requestMappingStore[$url])) {
            $requestMappingStore[$url] = array();
        }

        $map = &$requestMappingStore[$url];

        if (isset($map[$type])) {
            throw new RequestMapTypeDuplicateException($type);
        }

        $map[$type] = array(
            "class" => $class,
            "method" => $method,
            "path" => $path
        );
    }
}