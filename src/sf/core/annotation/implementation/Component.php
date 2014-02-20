<?php

namespace sf\core\annotation\implementation;

use sf\core\util\Util;

Util::mark("sf.core.annotation.engine.ClassDiscoveryListener");

use sf\core\Core;
use sf\core\annotation\engine\ClassDiscoveryListener;

class Component implements ClassDiscoveryListener {
    private static $Annotation_Name = array("Component");
    private $core;

    public function __construct() {
        $this->core = &Core::getInstance();
    }

    public function discovered(&$path, &$class, &$annotationsMap, $annotationName) {
        $classPath = Util::toVirtualPath($class);
        $temp = explode(".", $classPath);
        $id = lcfirst($temp[count($temp) - 1]);
        $singleton = true;

        if ($annotationsMap) {
            if (isset($annotationsMap["id"])) {
                $id = $annotationsMap["id"];
            }

            if (isset($annotationsMap["singleton"])) {
                $value = $annotationsMap["singleton"];
                $value = strtolower($value);
                $singleton = $value == "true";
            }
        }

        $this->core->addNewResourceSource($id, $classPath, array(), $singleton);
    }

    public function getAnnotationsName() {
        return Component::$Annotation_Name;
    }
}