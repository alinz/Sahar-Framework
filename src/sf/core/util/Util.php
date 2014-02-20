<?php

namespace sf\core\util;

class Util {
    public static function toNamespacePath($path) {
        return str_replace(".", "\\", $path);
    }

    public static function toRealPath($path) {
        return str_replace(".", "/", $path);
    }

    public static  function toVirtualPath($path) {
        return str_replace("\\", ".", $path);
    }

    public static function mark($filePath) {
        $filePath = "src/" . Util::toRealPath($filePath) . ".php";
        include_once($filePath);
    }

    public static function markTest($filePath) {
        $filePath = "test/" . Util::toRealPath($filePath) . ".php";
        include_once($filePath);
    }
}

