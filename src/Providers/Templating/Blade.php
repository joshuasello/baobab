<?php

namespace Baobab\Providers\Templating;


require_once __DIR__ . "/../../../vendor/autoload.php";


use \eftec\bladeone\BladeOne;


class Blade extends Engine {

    public static function boot($templates_path, $compiled_path) {
        parent::boot($templates_path, $compiled_path);
        self::$engine = new BladeOne(
            self::$templates_path,
            self::$compiled_path,
            BladeOne::MODE_AUTO
        );
    }

    public static function render($name, array $context=[]) {
        if (is_null(self::$engine)) {
            throw new \Exception("Templating engine not setup.", 1);
        }
        return self::$engine->run($name, $context);
    }

    public static function add_global($name, $value) {
        self::$engine->share($name, $value);
    }

    public static function add_directive($name, $func) {
        self::$engine->directive($name, $func);
    }

    public static function add_runtime_directive($name, $func) {
        self::$engine->directiveRT($name, $func);
    }
}
