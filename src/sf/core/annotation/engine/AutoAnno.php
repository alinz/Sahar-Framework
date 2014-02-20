<?php
namespace sf\core\annotation\engine;

use sf\core\util\Util;

Util::mark("sf.core.annotation.engine.AnnotationDiscovery");

class AutoAnno implements AnnotationDiscovery {
    const Pattern_Start = '/@';
    const Pattern_End   = '\(([\w\=\d\/\;,\ \t\{\}<>]*)\)/';

    protected $listeners;

    public function __construct() {
        $this->listeners = array();
    }

    public function discover($paths) {
        if (is_array($paths)) {
            foreach($paths as &$path) {
                $this->discovering($path);
            }
        } else if (is_string($paths)) {
            $this->discovering($paths);
        }
    }

    public function registerListener(DiscoveryListener &$listener) {
        if ($listener instanceof DiscoveryListener) {
            array_push($this->listeners, $listener);
        }
    }

    protected function discovering(&$path) {
        $lists = array_diff(scandir($path), array('..', '.'));

        foreach ($lists as &$item) {
            $file = $path . $item;
            if (is_dir($file)) {
                $file .= "/";
                $this->discovering($file);
            } else if ($this->isPhp($file)) {
                $this->readAnnotations($file);
            }
        }
    }

    private function isPhp(&$path) {
        return preg_match("/.*\\.php$/", $path);
    }

    private function readAnnotations(&$filename) {
        $tokens = $this->getTokens($filename);
        $max = count($tokens);
        $comment = false;
        $i = 0;
        $class = "";
        $namespace = "";

        while($i < $max) {
            $token = $tokens[$i];
            if (is_array($token)) {
                list($code, $value) = $token;
                switch($code) {
                    case T_NAMESPACE:
                        $namespace = $this->getNamespace($tokens, $i, $max) . "\\";
                        break;

                    case 366:
                    case T_DOC_COMMENT:
                        $comment = $value;
                        break;

                    case T_CLASS:
                    case T_INTERFACE:
                        $class = $namespace.$this->getString($tokens, $i, $max);
                        if ($comment !== false) {
                            $this->checkClassAnnotation($filename, $class, $comment);
                            $comment = false;
                        }
                        break;

                    case T_VARIABLE:
                        if ($comment !== false) {
                            $field = substr($token[1], 1);
                            $this->checkFieldAnnotation($filename, $class, $field, $comment);
                            $comment = false;
                        }
                        break;

                    case T_FUNCTION:
                        if ($comment !== false) {
                            $function = $this->getString($tokens, $i, $max);
                            $this->checkMethodAnnotation($filename, $class, $function, $comment);
                            $comment = false;
                        }
                        break;

                    // ignore
                    case T_PUBLIC:
                    case T_FINAL:
                    case T_PROTECTED:
                    case T_WHITESPACE:
                    case T_ABSTRACT:
                    case T_PRIVATE:
                    case T_VAR:
                        break;

                    default:
                        $comment = false;
                        break;
                }
            } else {
                $comment = false;
            }
            $i++;
        }
    }

    private function checkClassAnnotation(&$path, &$class, &$annotationStr) {
        foreach($this->listeners as &$listener) {
            if ($listener instanceof ClassDiscoveryListener) {
                $annotationsName = $listener->getAnnotationsName();
                foreach($annotationsName as &$annotationName) {
                    $pattern = self::makePattern($annotationName);
                    preg_match($pattern, $annotationStr, $args);
                    if (count($args) === 2) {
                        $params = $this->extractParams($args[1]);
                        $listener->discovered($path, $class, $params, $annotationName);
                    }
                }
            }
        }
    }

    private function checkMethodAnnotation(&$path, &$class, &$method, &$annotationStr) {
        foreach($this->listeners as &$listener) {
            if ($listener instanceof MethodDiscoveryListener) {
                $annotationsName = $listener->getAnnotationsName();
                foreach($annotationsName as &$annotationName) {
                    $pattern = self::makePattern($annotationName);
                    preg_match($pattern, $annotationStr, $args);
                    if (count($args) === 2) {
                        $params = $this->extractParams($args[1]);
                        $listener->discovered($path, $class, $method, $params, $annotationName);
                    }
                }
            }
        }
    }

    private function checkFieldAnnotation(&$path, &$class, &$field, &$annotationStr) {
        foreach($this->listeners as &$listener) {
            if ($listener instanceof FieldDiscoveryListener) {
                $annotationsName = $listener->getAnnotationsName();
                foreach($annotationsName as &$annotationName) {
                    $pattern = self::makePattern($annotationName);
                    preg_match($pattern, $annotationStr, $args);
                    if (count($args) === 2) {
                        $params = $this->extractParams($args[1]);
                        $listener->discovered($path, $class, $field, $params, $annotationName);
                    }
                }
            }
        }
    }

    private function extractParams(&$arg) {
        if ($arg === '') {
            return False;
        }

        $map = array();

        $params = explode(',', $arg);
        foreach($params as $param) {
            list($key, $value) = explode('=', $param);
            $map[trim($key)] = trim($value);
        }

        return $map;
    }

    private function getNamespace(&$tokens, &$i, $max) {
        $namespace = "";
        do {
            $i++;
            $token = $tokens[$i];
            if (is_array($token)) {
                if ($token[0] == T_STRING || $token[0] == 384) {
                    $namespace .= $token[1];
                }
            } else {
                return $namespace;
            }
        } while($i <= $max);
        return false;
    }

    private function getString($tokens, &$i, $max) {
        do {
            $token = $tokens[$i];
            $i++;
            if (is_array($token)) {
                if ($token[0] == T_STRING) {
                    return $token[1];
                }
            }
        } while($i <= $max);
        return false;
    }

    private function getTokens($file) {
        return token_get_all(file_get_contents($file));
    }

    private static function makePattern(&$str) {
        return self::Pattern_Start . $str . self::Pattern_End;
    }
}