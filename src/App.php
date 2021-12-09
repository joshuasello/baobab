<?php
/**
 * App Class File
 *
 * This file stores the the App class definition.
 *
 * @package Baobab
 * @author  Joshua Sello <joshuasello@gmail.com>
 * @since   1.0.0
 */


namespace Baobab;


// include globals functions
require_once __DIR__ . "/functions.php";
require_once __DIR__ . "/helpers.php";


use Baobab\Http\Request;
use Baobab\Http\Exceptions\HttpException;
use Baobab\Http\Responses\Response;
use Baobab\Routing\Router;


/**
 * App Class
 *
 * An App instance is an interactor object that handles a given request and
 * despatches it to a given route.
 */
class App {

    public static $request;

    private static $config;

    private $root;
    private $providers=[];
    private $middleware=[];

    function __construct($config=null, $root_path=null) {
        self::$config=(array) (require_once(__DIR__ . "/preset.php"));
        self::load_config($config);

        self::$request = new Request();
        $this->set_root($root_path);
        $this->load_providers();
        $this->load_middleware();
    }

    function __destruct() {
        // 1. Run middleware operations
        try {
            self::$request = $this->run_middleware();
        } catch (HttpException $http_e) {
            // run error handler route

        }

        // 2. Run on ship operations for providers
        $this->ship_providers();
        // 3. ship response to client
        Response::response()->ship();
    }

    public function register_extension($type, $cls) {
        $type = \strtolower($type);

        if (!\class_exists($cls)) {
            \trigger_error(
                "Could not add $type. Class '$cls' not found.",
                E_USER_WARNING
            );
            return;
        }

        if ($type === "middleware") {
            \array_push($this->middleware, $cls);
        } elseif ($type === "provider") {
            \array_push($this->providers, $cls);
        } else {
            throw new \UnexpectedValueException("Invalid extension type '$type' given.");
        }
    }

    public function register_provider($cls) {
        $this->register_extension("provider", $cls);
        // trigger on register method on provider class
        $name = \constant("$cls::name");
        $config = (\array_key_exists($name, self::$config)) ?
            self::$config[$name] : null;
        \call_user_func_array("$cls::on_register", [$config]);
    }

    public function register_middleware($cls) {
        $this->register_extension("middleware", $cls);
    }

    public static function config($name) {
        return self::$config[$name];
    }

    private function run_middleware() {
        return self::process_request($this->middleware, self::$request);
    }

    private static function process_request(array $middleware, $request) {
        $cls = \array_shift($middleware);
        $obj = new $cls();
        $handled_request = $obj->handle($request);
        if (\count($middleware) === 0) {
            return $handled_request;
        }
        return self::process_request($middleware, $handled_request);
    }

    private function ship_providers() {
        foreach ($this->providers as $cls) {
            \call_user_func_array(
                "$cls::on_ship",
                [self::$request]
            );
        }
    }

    private function load_extension($path, $type) {
        $register = (array)(require_once($path));
        if ($type === "middleware") {
            foreach ($register as $cls) {
                $this->register_middleware($cls);
            }
        } elseif ($type === "provider") {
            foreach ($register as $cls) {
                $this->register_provider($cls);
            }
        } else {
            throw new \UnexpectedValueException("Invalid extension type '$type' given.");
        }
    }

    private function load_providers() {
        $this->load_extension(__DIR__ . "/Providers/register.php", "provider");
    }

    private function load_middleware() {
        $this->load_extension(__DIR__ . "/Middleware/register.php", "middleware");
    }

    private function set_root($root_path) {
        if (is_null($root_path)) {
            $this->root_path = getcwd();
        } elseif (!is_dir($root_path)) {
            throw new \Exception("Error finding app root directory", 1);
        } else {
            $this->root = $root_path;
        }
    }

    private static function load_config($config) {
        if (\is_null($config) || !$config) {
            return false;
        } elseif (\is_string($config)) {
            // assume config is a file path string
            self::load_config_from_file($config);
        } elseif (\is_assoc($config)) {
            self::set_config_from_array($config);
        } else {
            throw new \InvalidArgumentException(
                "Provided config is neither a valid file path or an associative array."
            );
        }
    }

    private static function set_config_from_array(array $config_data){
        if (!$config_data || \is_null($config_data)) {
            return null;
        }
        self::$config = self::merge_valid_fields(self::$config, $config_data);
    }

    private static function load_config_from_file($file_path){
        if (!file_exists($file_path)) {
            trigger_error(
                "Config file not found, reverting to default config.",
                E_USER_WARNING
            );
            return null;
        }
        $parts = \explode(".", $file_path);
        $extention = $parts[count($parts)-1];
        $data = null;
        if ($extention === "php") {
            $data = (include $file_path);
        } elseif ($extention === "json") {
            $data = json_decode(file_get_contents($file_path), true);
        }
        self::set_config_from_array($data);
    }

    private static function merge_valid_fields(array $array_1, array $array_2) {
        foreach ($array_2 as $key => $value) {
            // check that field is valid
            if (!array_key_exists($key, $array_1)) {
                continue;
            }
            if (is_array($array_1[$key]) && is_array($array_2[$key])) {
                $array_1[$key] = self::merge_valid_fields(
                    $array_1[$key],
                    $array_2[$key]
                );
                continue;
            }
            $array_1[$key] = $value;
        }
        return $array_1;
    }

}
