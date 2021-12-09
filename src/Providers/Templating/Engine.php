<?php


namespace Baobab\Providers\Templating;


class Engine {

    protected static $engine;
    protected static $templates_path;
    protected static $compiled_path;

    public static function boot($templates_path, $compiled_path) {
        self::templates_path($templates_path);
        self::compiled_path($compiled_path);
    }

    public static function compiled_path($path=null) {
        if (\is_null($path)) {
            return self::$compiled_path;
        }
        if (!\is_dir($path)) {
            \mkdir($path, 0777, true);
        }
        self::$compiled_path = $path;
    }

    public static function templates_path($path=null) {
        if (\is_null($path)) {
            return self::$templates_path;
        }
        if (!\is_dir($path)) {
            \mkdir($path, 0777, true);
        }
        self::$templates_path = $path;
    }

}
