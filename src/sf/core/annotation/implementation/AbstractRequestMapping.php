<?php

namespace sf\core\annotation\implementation;

use sf\core\util\Util;

Util::mark("sf.core.annotation.engine.AbstractAnnotation");

use sf\core\annotation\engine\AbstractAnnotation;

abstract class AbstractRequestMapping extends AbstractAnnotation {
    const Attribute_Request_Mapping_Store = "request_mapping_store";
    const Attribute_Class_Prefix_Store = "class_prefix_request_mapping_store";

    const Annotation_Value = 'value';
    const Annotation_Type = 'type';

    private static $search = array('/', '<', '{int}', '{string}');
    private static $replace = array('\/', '(?<', '\d+)', '\w+)');
    private static $start = '/^';
    private static $end = '$/';

    protected final function convertToRegExp($url) {
        $pattern = str_replace(AbstractRequestMapping::$search, AbstractRequestMapping::$replace, $url);
        $pattern = AbstractRequestMapping::$start . $pattern . AbstractRequestMapping::$end;
        return $pattern;
    }
}