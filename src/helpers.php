<?php
/**
 * Global Helper Functions File
 *
 * This file contains the declarations for helper functions that
 * encapsulate services offered by the baobab framework. The aim
 * of this file is, if a user should so choose, provide any
 * functionality for constructing a working baobab project.
 *
 * P.S. Alot of inspiration for some of the functions came from the
 * laravel project.
 *
 * @package Baobab
 * @author  Joshua Sello <joshuasello@gmail.com>
 * @since   1.0.0
 */

use Baobab\App;
use Baobab\Http\Responses\Response;
use Baobab\Http\Responses\ResponseFactory;
use Baobab\Http\Responses\RedirectResponse;


function app($config=null, $root_path=null) {
    return (new App($config, $root_path));
}


function request() {
    return App::$request;
}



function config($name) {
    return App::config($name);
}


function response($body=null, $code=200, $headers=[]) {
    if (\is_null($body)) {
        return (new ResponseFactory());
    }
    return (new Response($body, $code, $headers));
}


function redirect($location=null, $status=302, $headers=[]) {
    if (\is_null($location)) {

    }
    return (new RedirectResponse($location, $status, $headers));
}


function back() {

}


function base_path() {

}


function lang() {

}
