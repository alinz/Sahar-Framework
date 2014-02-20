<?php

namespace sf\core;

use sf\core\util\Util;

Util::mark("sf.core.Context");
Util::mark("sf.core.Listener");

interface ResourceListener extends Listener {
    public function resourceDidLoad(Context &$context);
}