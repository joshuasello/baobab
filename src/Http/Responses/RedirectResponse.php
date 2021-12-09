<?php


namespace Baobab\Http\Responses;


use Baobab\Provders\Routing\Router;


class RedirectResponse extends Response {

    function __construct($location=null, $status=302, array $headers=[]) {
        parent::__construct();
        $this->with_headers($headers);
        $this->status($status);
        if (!\is_null($location)) {
            $this->header("Location", $location);
        }
    }

    public function route($name) {
        // NOTE: The path return in match_to_path is the formatted path for that route
        $this->header("Location", Router::match_to_path($name), true);
    }

    public function ship() {
        if (!$this->has_header("Location")) {
            throw new \Exception("A location has not been set for this redirect response.", 1);
        }
        parent::ship();
    }

}
