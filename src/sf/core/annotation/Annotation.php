<?php

namespace sf\core\annotation;

abstract class Annotation {
    final public function __call($name, $arguments) {
        preg_match("/^set([A-Z].+)/", $name, $args);
        if (count($args) === 2) {
            $variable = lcfirst($args[1]);
            $this->$variable = $arguments[0];
        }
    }
}