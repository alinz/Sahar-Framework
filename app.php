<?php
include "src/sf/core/util/Util.php";
include "src/sf/core/Core.php";

use sf\core\Core;

$core = Core::getInstance("sf.json", "local.json");
$core->start();