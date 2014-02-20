<?php

namespace sf\core\annotation\implementation;

use sf\core\util\Util;

Util::mark("sf.core.Core");
Util::mark("sf.core.annotation.engine.ClassDiscoveryListener");
Util::mark("sf.core.annotation.implementation.AbstractRequestMapping");

use sf\core\Core;
use sf\core\annotation\engine\ClassDiscoveryListener;

class ClassRequestMapping extends AbstractRequestMapping implements ClassDiscoveryListener {
    private static $Annotation_Name = array("RequestMapping");
    private $core;

    public function __construct() {
        $this->core = &Core::getInstance();
        $this->core->setAttribute(AbstractRequestMapping::Attribute_Class_Prefix_Store, "");
    }

    public function discovered(&$path, &$class, &$annotationsMap, $annotationName) {
        $attributes = &$this->core->getAttribute(AbstractRequestMapping::Attribute_Class_Prefix_Store);
        $attributes[$class] = $annotationsMap[AbstractRequestMapping::Annotation_Value];
    }

    public function getAnnotationsName() {
        return ClassRequestMapping::$Annotation_Name;
    }
}