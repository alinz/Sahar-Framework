<?php

namespace sf\test;

class Test {
    private $methodList;

    public function __construct() {
        $this->methodList = array();
    }
    public final function addTest($methodName) {
        array_push($this->methodList, $methodName);
    }
    public final function run() {
        $emptyArray = array();
        foreach ($this->methodList as $methodName) {
            $this->setup();
            call_user_func_array(array($this, $methodName), $emptyArray);
            $this->release();
        }
    }
    public function setup() { }
    public function release() { }
}