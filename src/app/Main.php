<?php

namespace app;

use sf\core\util\Util;

Util::mark("sf.core.Context");
Util::mark("sf.core.ResourceListener");
Util::mark("sf.core.annotation.Annotation");
Util::mark("sf.core.StartApplication");

use sf\core\annotation\Annotation;
use sf\core\Context;
use sf\core\ResourceListener;
use sf\core\StartApplication;

class Main extends Annotation implements ResourceListener, StartApplication {
    private $arg1;
    private $arg2;

    /**
     * @Value()
     */
    protected $platform;

    /**
     * @Resource()
     */
    protected $dao;

    /**
     * @Resource()
     */
    protected $profileService;

    public function __construct($arg1, $arg2) {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }

    public function resourceDidLoad(Context &$context) {
        echo "resourceDidLoad is called.";
    }

    public function start() {

    }
}