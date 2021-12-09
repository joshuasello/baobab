<?php


use Baobab\App;


function render_template($name, array $context=[]) {
    $engine_cls = App::config("templating")["engine"];
    return call_user_func_array("$engine_cls::render", [$name, $context]);
}
