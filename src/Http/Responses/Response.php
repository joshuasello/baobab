<?php


namespace Baobab\Http\Responses;


// TODO: Cookie methods
class Response {

    private static $current;

    private $body;
    private $headers;
    private $status=200;
    private $closed=false;
    private $cookies=[];
    private $charset="UTF-8";

    function __construct($body=null, $status=200, array $headers=[], $charset="UTF-8") {
        $this->body = $body;
        $this->status = $status;
        $this->headers = array_merge(\headers_list(), $headers);
        $this->charset = $charset;
        // default response to ship
        self::response($this);
    }

    public function status($code=null) {
        if (\is_null($code)) {
            return $this->status;
        }
        // validate code
        if (self::status_code_valid($code)) {
            $this->status = $code;
            return $this;
        }
    }

    public function body($content=null) {
        if (\is_null($content)) {
            return $this->body;
        }
        $this->body = $content;
        return $this;
    }

    public function charset($charset=null) {
        if (\is_null($charset)) {
            return $this->charset;
        }
        $this->charset = $charset;
        return $this;
    }

    public function write($content) {
        if (is_null($content)) {
            return $this;
        }
        if (is_null($this->body)) {
            $this->body = $content;
        } else {
            $this->body .= (string) $content;
        }
        return $this;
    }

    public function header($name, $value, $replace=true) {
        if ($replace and \array_key_exists($name, $this->headers)) {
            return $this;
        }
        $this->headers[$key] = $value;
        return $this;
    }

    public function with_headers(array $headers, $replace=true) {
        foreach ($headers as $name => $value) {
            $this->header($name, $value, $replace);
        }
    }

    public function has_header($name) {
        return \array_key_exists($name, $this->headers);
    }

    public function has_headers(array $names) {
        foreach ($names as $name) {
            if(!$this->has_header($name)) {
                return false;
            }
        }
        return true;
    }

    public function cookie($name, $value="", $expires=0, $path="", $domain="",
        $secure=false, $httponly=false) {
        $this->cookies[$name] =
        [
            "value"     => $value,
            "expires"   => $expires,
            "path"      => $path,
            "domain"    => $domain,
            "secure"    => $secure,
            "httponly"  => $httponly
        ];
        $this;
    }

    public function ship() {
        if (!$this->closed) {
            http_response_code($this->status);
            $this->set_headers();
            $this->set_cookies();
            echo $this->body;
            $this->closed = true;
        }
    }

    public static function response(Response $response=null) {
        if (\is_null($response)) {
            return self::$current;
        }
        self::$current = $response;
    }

    private function set_headers() {
        foreach ($this->headers as $key => $value) {
            \header(trim($key) . ": " . trim($value), true);
        }
    }

    private function set_cookies() {

    }

    private static function status_code_valid($code) {
        $str_code = (string) $code;
        if (\strlen($str_code) != 3) {
            return false;
        }
        if (!\in_array($str_code[0], ["1", "2", "3", "4", "5"])) {
            return false;
        }
        return true;
    }

}
