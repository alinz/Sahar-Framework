<?php

namespace sf\core\annotation\engine;

use sf\core\util\Util;

Util::mark("sf.core.annotation.engine.DiscoveryListener");

interface AnnotationDiscovery {
    public function discover($paths);
    public function registerListener(DiscoveryListener &$listener);
}