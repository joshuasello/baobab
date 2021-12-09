<?php


namespace Baobab\Providers\Routing;


// TODO: Route middleware


class Route {

    const PARAM_TYPES = ["string", "int", "float"];
    const SUPPORTED_METHODS = ["GET", "POST"];

    private $name;
    private $path;
    private $formatted_path;
    private $action;
    private $methods=[];
    private $params=[];


    function __construct($path, $action) {
        $this->path = $path;
        $this->action = $action;
        // extracted properties
        $this->formatted_path = self::format_path($path);
        $this->extract_params($path);
    }

    public function trigger($path) {
        return \call_user_func_array(
            $this->action,
            $this->arg_vals($path)
        );
    }

    public function name($name=null) {
        if (\is_null($name)) {
            return $this->name;
        }
        $this->name = $name;
        return $this;
    }

    public function has_name($name) {
        return $this->name === $name;
    }

    public function path(array $arg_vals=[]) {
        if (!$arg_vals) {
            return $this->formatted_path;
        }

        if (\substr_count($str, "~") !== \count($arg_vals)) {
            throw new \LengthException("Parameter count does not match the amount of provided arguments");
        }

        $count = 0;
        $path = "/";
        $parts = \explode("/");

        foreach ($parts as $part) {
            if ($part === "~") {
                $path .= $arg_vals[0];
                $count++;
            } else {
                $path .= $part;
            }
            $path .= "/";
        }

        return \trim($path);
    }

    public function methods(...$methods) {
        $methods = (\is_array($methods[0])) ? $methods[0] : $methods;
        if (!self::methods_valid($methods)) {
            throw new \Exception("Provided methods are invalid", 1);
        }
        $this->methods = $methods;
        return $this;
    }

    public function matches($path, $method=null) {
        // determine whether to check the method
        if (!is_null($method) and $this->methods !== []) {
            if (!\in_array($method, $this->methods)) {
                return false;
            }
        }
/*
        if ($this->path !== $path and $this->path !== $path . "/") {
            return false;
        }
*/
        return $this->matches_formatted_path($path);
    }

    private function matches_formatted_path($path) {
        // makes sure that the path has a trailing slash if necessary
        if (!self::has_trailing_slash($path) &&
            self::has_trailing_slash($this->formatted_path)) {
            $path .= "/";
        }
        $formatted_parts = explode("/", $this->formatted_path);
        $path_parts = explode("/", $path);
        // amount of parts have to equal
        if (count($path_parts) != count($formatted_parts)) {
            return false;
        }
        // check if all non-parameter indicated parts equal
        for ($i = 0; $i < count($path_parts); $i++) {
            if (trim($formatted_parts[$i] != "~")) {
                if ($formatted_parts[$i] != $path_parts[$i]) {
                    return false;
                }
            }
        }
        return true;
    }

    private function arg_vals($path) {
        if (\is_null($this->params) or !$this->matches($path)) {
            return [];
        }
        $vals = [];
        $p_count = 0; // param counter
        // NOTE: because the paths have matched, it is assumed that their parts
        // will be of equal length
        $formatted_parts = \explode("/", $this->formatted_path);
        $path_parts = \explode("/", $path);
        for ($i = 0; $i < \count($formatted_parts); $i++) {
            if (\trim($formatted_parts[$i]) == "~") {
                // ensure the type of the provided argument value
                \array_push($vals, self::cast_arg_val(
                        // index points to the type of the current param
                        $this->params[$p_count][1],
                        $path_parts[$i]
                ));
                $p_count++;
            }
        }
        return $vals;
    }

    public static function has_trailing_slash($path) {
        return $path[strlen($path)-1] == "/";
    }

    public static function format_path($path) {
        $path = \filter_var(\ltrim(\trim($path), "/ "), FILTER_SANITIZE_URL);
        // get rid of trailing query data
        $path = \explode("?", $path)[0];

        if ($path === "") {
            return "/";
        }

        $formatted = "";
        foreach (\explode("/", $path) as $part) {
            if (self::is_param_part($part)) {
                // concatenate a parameter part indicator
                $formatted .= "/~";
            } else {
                $formatted .= "/$part";
            }
        }
        return \ltrim($formatted, "/");
    }

    private function extract_params($path) {
        if (trim($path) == "/") {
            return null;
        }

        $parts = explode("/", $path);
        foreach ($parts as $part) {
            if (self::is_param_part($part)) {
                // order important
                array_push($this->params, [
                    self::get_param_part_name($part),
                    // NOTE: string is the default type
                    self::get_param_part_type($part)
                ]);
            }
        }
    }

    private static function is_param_part($part) {
        if (!strlen($part)){
            return false;
        }
        return $part[0] == "{" and $part[strlen($part) - 1] == "}";
    }

    private static function get_param_part_name($param_part) {
        return trim(explode(":", trim($param_part, "{}"))[0]);
    }

    private static function get_param_part_type($param_part) {
        $param_parts = explode(":", $param_part);
        $type = (\count($param_parts) == 2) ?
            \trim($param_parts[1], "{ }") : "string";
        if (!\in_array($type, self::PARAM_TYPES)) {
            throw new \Exception("Provided an invalid parameter type: '$type'");
        }
        return $type;
    }

    private static function cast_arg_val($type, $arg_val) {
        if ($type === "int")
            return (int)$arg_val;
        elseif ($type === "float")
            return (float)$arg_val;
        return (string)$arg_val;
    }

    private static function methods_valid(array $methods) {
        if (!$methods) {
            return false;
        }
        // check each methods listed for the route
        foreach ($methods as $method) {
            if (!in_array(strtoupper($method), self::SUPPORTED_METHODS)) {
                    return false;
            }
        }
        return true;
    }

}
