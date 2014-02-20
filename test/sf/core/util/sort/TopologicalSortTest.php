<?php

namespace sf\core\util\sort;

use sf\core\util\Util;

Util::markTest("Test");
Util::mark("sf.core.util.sort.TopologicalSort");

use sf\test\Test;

class TopologicalSortTest extends Test {
    public function __construct() {
        parent::__construct();

        $this->addTest("testValidateData");
    }

    public function testValidateData() {

        $topologicalSort = new TopologicalSort();

        $graph = array(
            "S1" => array("S2"),
            "S2" => array("S3", "S4"),
            "S3" => array(),
            "S4" => array("S4")
        );

        $sorted = $topologicalSort->sort($graph);

        $expectedArray = array("S3", "S4", "S2", "S1");

        assert($sorted == $expectedArray);
    }
}