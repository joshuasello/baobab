<?php


namespace Baobab\Providers\Routing;


require_once __DIR__ . "/helpers.php";


use Baobab\Providers\Provider;
use Baobab\Http\Responses\Response;


class RoutingProvider extends Provider {

    const name="templating";

    public static function on_register($config) {
        // routing does not need any configs to be set up
        // this means that a null value will be passed in as
        // the cofig
        parent::on_register($config);
    }

    public static function on_ship($request) {
        $result = Router::trigger_route($request->path(), $request->method());
        if (is_string($result)) {
            Response::response(new Response($result));
        } elseif ($result instanceof Response) {
            // sets the headers, status code and outputs the response body
            // to the client
            Response::response($result);
        }
    }

}
