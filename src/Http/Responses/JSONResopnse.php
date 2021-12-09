<?php


namespace Baobab\Http\Responses;


class JSONResponse extends Response {

    function __construct(array $json) {
        parent::__construct(
            \json_encode($json),
            200,
            ["Content-Type" => "application/json"],
            "UTF-8"
        );
    }

    public function with_callback($callback_name) {
        $this->body($callback_name . "(" . $this->body() . ");");
    }

}
