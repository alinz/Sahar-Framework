<?php

include "src/sf/core/util/Util.php";

use sf\core\util\Util;

Util::markTest("sf.core.util.sort.TopologicalSortTest");

$topologicalSortTest = new \sf\core\util\sort\TopologicalSortTest();
$topologicalSortTest->run();