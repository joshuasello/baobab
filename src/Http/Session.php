<?php


namespace Baobab\Http;


class Session {

    private $id;

    function __construct(){
        \session_start();
    }

    public function put($key, $value) {

    }

    public function push($location, $value) {

    }

    public function get($key, $default=null) {

    }

    public function has(...$keys) {

    }

    public function exists(...$keys) {

    }

    public function all() {

    }

}
