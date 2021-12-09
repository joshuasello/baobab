<?php


namespace Baobab\Providers;


class Provider {

    protected static $registered=false;
    protected static $config;

    public static function on_register($config) {
        self::$config = $config;
        self::$registered = true;
    }

    public static function on_ship($request) {
        if (!self::$registered) {
            throw new \Exception("Provider not registered");
        }
    }

}
