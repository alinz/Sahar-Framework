<?php

namespace sf\core\annotation\exception;

use sf\core\util\Util;

Util::mark("sf.core.exception.annotation.AnnotationException");

use sf\core\exception\annotation\AnnotationException;

class RequestMapTypeDuplicateException extends AnnotationException { }