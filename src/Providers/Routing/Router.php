<?php
/**
 * Router Class File
 *
 * This file stores the the Routes class definition.
 *
 * @package Baobab
 * @author  Joshua Sello <joshuasello@gmail.com>
 * @since   1.0.0
 */


namespace Baobab\Providers\Routing;


use Baobab\Http\Responses\Response;
use Baobab\Http\Responses\Status;


// TODO: Optional parameters for route paths
// TODO: Regular expression constraints on route parameters
// TODO: Default request handler
class Router {

    private static $routes=[];
    private static $error_handlers = [];
    private static $current_route;

    /**
     * Adds a new route.
     *
     * @param array     $methods    Methods that this route responds to
     * @param string    $uri        URI for this route
     * @param callable  $handler    Function to be executed
    */
    public static function route($path, $action) {
        \array_push(self::$routes, new Route($path, $action));
        return self::$routes[\count(self::$routes)-1];
    }

    /**
     * Sets a new handler for an error.
     *
     * @param int       $code       Error code
     * @param callable  $action    Function to be executed
    */
    public static function error_handler($code, $action) {
        self::$error_handlers[$code] = $action;
    }

    /**
     * Calls a handler with a given code
     *
     * @param int       $code       Error code
     * @param string    $message    Message
    */
    public static function trigger_error($code, $message=null) {
        if (array_key_exists($code, self::$error_handlers)) {
            $error_content = call_user_func_array(
                self::$error_handlers[$code],
                [$message]
            );
            return new Response($error_content, $code);
        }

        // default request hander
        return new Response("Error handling request", $code);
    }

    public static function trigger_route($path, $method) {
        // format path and method
        $path = Route::format_path($path);
        $method = \strtoupper($method);

        self::$current_route = self::match_to_route($path, $method);

        if (\is_null(self::$current_route)) {
            // 404
            return self::trigger_error(Status::NOT_FOUND);
        }

        if (Route::has_trailing_slash(self::$current_route->path()) and
            !Route::has_trailing_slash($path)) {
            // redirect
            header("Location: /$path/");
        }
        return self::$current_route->trigger($path);
    }

    public static function template($path, $name, array $context=[]) {
        return self::route($path, function() {
            return \render_template($name, $context);
        });
    }

    public static function redirect($from, $to, $status=302) {
        return self::route($from, function() {
            return \redirect($to)->status($status);
        });
    }

    public static function match_to_path($name) {
        foreach (self::$routes as $route) {
            if ($route->has_name($name)) {
                // returns formatted route path
                return $route->path();
            }
        }
    }

    private static function match_to_route($path, $method) {
        foreach (self::$routes as $route) {
            if ($route->matches($path, $method)) {
                return $route;
            }
        }
    }

}
