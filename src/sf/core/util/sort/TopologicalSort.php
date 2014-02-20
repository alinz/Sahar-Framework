<?php

namespace sf\core\util\sort;

use sf\core\util\Util;

Util::mark("sf.core.util.sort.Sort");


/**
 * Class TopologicalSort
 * @package sf\core\util\sort
 *
 * @author Felipe Ribeiro <felipernb@gmail.com>
 * @co-author Ali Najafizadeh
 *
 * @url https://github.com/felipernb/php_algorithms/blob/master/graphs/topological_sorting.php
 */
class TopologicalSort implements Sort{

    const UNVISITED = 0;
    const VISITED   = 1;

    public function sort(array &$array) {
        $status = array();
        $stack = array();
        foreach ($array as $n => $neighbors) $status[$n] = TopologicalSort::UNVISITED;

        foreach ($array as $n => $neighbors) {
            $this->visit($array, $n, $status, $stack);
        }

        return $stack;
    }

    private function visit(array &$graph, $n, array &$status, array &$stack) {
        if($status[$n] == TopologicalSort::UNVISITED) {
            $status[$n] = TopologicalSort::VISITED;
            foreach ($graph[$n] as $neighbor) {
                $this->visit($graph, $neighbor, $status, $stack);
            }
            array_push($stack, $n);
        }
    }
}