<?php


namespace Baobab\Providers\Templating;


require_once __DIR__ . "/helpers.php";


use Baobab\Providers\Provider;


class TemplatingProvider extends Provider {

    const name="templating";

    public static function on_register($config) {
        parent::on_register($config);
        $engine_cls = self::$config["engine"];
        if (\is_null($engine_cls)) {
            return null;
        }
        if (!class_exists($engine_cls)) {
            throw new \Exception("Engine class '$engine_cls' does not exist.", 1);
        }
        // set template engine and configs
        \call_user_func_array(
            "$engine_cls::boot",
            [self::$config["templates_path"], self::$config["compiled_path"]]
        );
    }

}
