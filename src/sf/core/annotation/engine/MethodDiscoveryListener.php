<?php

namespace sf\core\annotation\engine;

use sf\core\util\Util;

Util::mark("sf.core.annotation.engine.DiscoveryListener");

interface MethodDiscoveryListener extends DiscoveryListener {
    public function discovered(&$path, &$class, &$method, &$annotationsMap, $annotationName);
}