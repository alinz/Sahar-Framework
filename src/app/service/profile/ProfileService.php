<?php

namespace app\service\profile;

/**
 * @Component()
 */
class ProfileService {
    private $message;
    public function __construct() {
        $this->message = "hello";
    }
}