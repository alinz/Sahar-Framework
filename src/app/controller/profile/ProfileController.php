<?php

namespace app\controller\profile;

use sf\core\util\Util;

Util::mark("sf.core.annotation.Annotation");

use sf\core\annotation\Annotation;

/**
 * @RequestMapping(value=/profile)
 */
class ProfileController extends Annotation {

    /**
     * @Resource()
     */
    protected $dao;

    /**
     * @Value(id = platform)
     */
    protected $platform;

    /**
     * @RequestMapping(value=/<id>{int}, type=GET)
     */
    public function getProfile() {

    }
}