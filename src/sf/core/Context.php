<?php

namespace sf\core;

interface Context {
    public function &getResourceObject($id);
    public function &getPropertyValue($name);
    public function &getAttribute($key);
    public function isResourceExist($id);
    public function setAttribute($key, $value);
    public function start();
}