<?php

namespace sf\core\system;

use sf\core\util\Util;

Util::mark("sf.core.exception.ResourceNotFoundException");
Util::mark("sf.core.exception.PropertyNotFoundException");
Util::mark("sf.core.exception.io.FileNotFoundException");

use sf\core\exception\ResourceNotFoundException;
use sf\core\exception\PropertyNotFoundException;
use sf\core\exception\io\FileNotFoundException;

class Configuration {
    const Pattern_Start = '/\$\{';
    const Pattern_End   = '\}/';

    const Field_Properties_Name         = "properties";
    const Field_Annotations_Name        = "annotations";
    const Field_Auto_Scan_Paths_Name    = "auto_scan_paths";
    const Field_Resources_Name          = "resources";
    const Field_Start_Name              = "start";

    private $properties;
    private $annotations;
    private $autoScanPaths;
    private $resources;
    private $start;

    public function __construct($default, $local) {
        $this->properties = array();
        $this->autoScanPaths = array();
        $this->annotations = array();
        $this->resources = array();
        $this->start = "";

        //First we parse the Files
        $this->parseFile($default, true);
        $this->parseFile($local, false);

        //apply properties
        $this->applyProperties($this->properties);
        $this->applyProperties($this->annotations);
        $this->applyProperties($this->autoScanPaths);
        $this->applyProperties($this->resources);

        $this->convertResourcesToMap();
    }

    public function getStart() {
        return $this->start;
    }

    public function &getResource($id) {
        if (!isset($this->resources[$id])) {
            throw new ResourceNotFoundException($id);
        }

        return $this->resources[$id];
    }

    public function &getProperties($key) {
        if (!isset($this->properties[$key])) {
            throw new PropertyNotFoundException($key);
        }

        return $this->properties[$key];
    }

    public function isPropertyExist($key) {
        return isset($this->properties[$key]);
    }

    public function isResourceExist($id) {
        return isset($this->resources[$id]);
    }

    public function addNewResource($id, $class, $args, $singleton) {
        $this->resources[$id] = array(
            "class" => $class,
            "args" => $args,
            "singleton" => $singleton
        );
    }

    public function &getAutoScanPaths() {
        return $this->autoScanPaths;
    }

    public function &getAnnotations() {
        return $this->annotations;
    }

    ///This is parseFile Section [Start]
    private function parseFile($fileName, $required) {
        if (!file_exists($fileName)) {
            if ($required) {
                throw new FileNotFoundException();
            }
            return;
        }

        //load the content of the file
        $content = file_get_contents($fileName);
        $map = json_decode($content, true);

        if (isset($map[Configuration::Field_Properties_Name])) {
            Configuration::mergeMap($map[Configuration::Field_Properties_Name], $this->properties);
        }

        if (isset($map[Configuration::Field_Annotations_Name])) {
            Configuration::mergeMapWithId($map[Configuration::Field_Annotations_Name], $this->annotations);
        }

        if (isset($map[Configuration::Field_Auto_Scan_Paths_Name])) {
            Configuration::mergeArray($map[Configuration::Field_Auto_Scan_Paths_Name], $this->autoScanPaths);
        }

        if (isset($map[Configuration::Field_Resources_Name])) {
            Configuration::mergeMapWithId($map[Configuration::Field_Resources_Name], $this->resources);
        }

        if (isset($map[Configuration::Field_Start_Name])) {
            $this->start = $map[Configuration::Field_Start_Name];
        }
    }

    private static function mergeMap(&$from, &$to) {
        foreach ($from as $key => $value) {
            $to[$key] = $value;
        }
    }

    private static function mergeArray(&$from, &$to) {
        $to = array_merge($from, $to);
    }

    private static function mergeMapWithId(&$from, &$to) {
        $fieldName = "id";
        foreach ($from as &$map) {
            $index = Configuration::findMapWithId($to, $fieldName, $map[$fieldName]);
            if ($index != -1) {
                $to[$index] = $map;
            } else {
                array_push($to, $map);
            }
        }
    }

    private static function findMapWithId(&$mapArray, $field, $value) {
        $index = 0;
        foreach ($mapArray as &$map) {
            if (isset($map[$field]) && $map[$field] == $value) return $index;
            $index++;
        }
        return -1;
    }
    ///This is parseFile Section [End]

    ///Apply Properties [Started].
    private function applyProperties(&$targetObject) {
        $json = json_encode($targetObject);
        foreach ($this->properties as $key => &$value) {
            $pattern = Configuration::Pattern_Start . $key . Configuration::Pattern_End;
            $json = preg_replace($pattern, $value, $json);
        }
        $targetObject = json_decode($json, true);
    }
    ///Apply Properties [End].

    private function convertResourcesToMap() {
        $result = array();
        foreach ($this->resources as $resource) {
            $id = $resource['id'];
            $result[$id] = $resource;
        }
        $this->resources = $result;
    }
}