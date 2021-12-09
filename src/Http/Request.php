<?php


namespace Baobab\Http;


// TODO: Flash method. current input to session
// TODO: cookies method
// TODO: files method


class Request {

    const INPUT_TYPES=["post", "get", "json", "files"];

    protected $meta_data=[];

    protected $get_data=[];
    protected $post_data=[];
    protected $json_data=[];
    protected $files_data=[];

    function __construct() {
        $this->load_meta_data();
        $this->load_get_data();
        $this->load_post_data();
        $this->load_json_data();
        $this->load_files_data();
    }

    public function body() {
        return \file_get_contents("php://input");
    }

    /*
     * Returns the current request url, including the query string
    */
    public function full_url() {
        // NOTE: SECURITY IMPLICATIONS. The client can set HTTP_HOST and
        // REQUEST_URI to any arbitrary value it wants.

        $scheme = $this->scheme();
        $host = $this->host();
        $path = $this->path();
        return $scheme . $host . $path;
    }

    /*
     * Returns the current request url without the query string
    */
    public function url() {
        return \explode("?", $this->full_url())[0];
    }

    public function path() {
        return $this->meta("request_uri");
    }

    public function path_matches($pattern) {
        return fnmatch($path, $this->path());
    }

    public function method() {
        return $this->meta("request_method");
    }

    public function is_method($method) {
        return $this->method() === strtoupper($method);
    }

    public function content_type() {
        return $this->meta("content_type");
    }

    public function input($key=null, $default=null) {
        $all_data = \array_merge($this->get(), $this->post(), $this->json());
        if (\is_null($key)) {
            return $all_data;
        }
        $value = \data_get($key, $all_data);
        return \is_null($value) ? $default : $value;
    }

    public function replace($key, $value) {
        foreach (self::INPUT_TYPES as $type) {
            $prop_name = $type . "_data";
            $data = $this->{$prop_name};
            if (\array_key_exists($key, $data)) {
                $data[$key] = $value;
            }
            $this->{$prop_name} = $data;
        }
    }

    public function except(...$keys) {
        $result = [];
        $data = $this->input();
        $keys = (array)(\is_array[$keys[0]]) ? $keys[0] : $keys ;
        foreach ($data as $key => $value) {
            if (!\in_array($key, $keys)) {
                $result[$key] = $data[$key];
            }
        }
        return $result;
    }

    public function only(...$keys) {
        $result = [];
        $data = $this->input();
        $keys = (array)(\is_array[$keys[0]]) ? $keys[0] : $keys ;
        foreach ($keys as $key) {
            if (\array_key_exists($key, $data)) {
                $result[$key] = $data[$key];
            }
        }
        return $result;
    }

    public function has(...$keys) {
        $data = $this->input();
        $keys = (array)(\is_array[$keys[0]]) ? $keys[0] : $keys ;
        foreach ($keys as $key) {
            if (!\array_key_exists($key, $data)) {
                return false;
            }
        }
        return true;
    }

    public function filled(...$keys) {
        $data = $this->input();
        $keys = (array)(\is_array[$keys[0]]) ? $keys[0] : $keys ;
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                return false;
            }
        }
        return true;
    }

    public function get($key=null, $default=null) {
        if (\is_null($key)) {
            return $this->get_data;
        }
        return (\array_key_exists($key, $this->get_data)) ?
            $this->get_data[$key] : $default;
    }

    public function post($key=null, $default=null) {
        if (\is_null($key)) {
            return $this->post_data;
        }
        return (\array_key_exists($key, $this->post_data)) ?
            $this->post_data[$key] : $default;
    }

    public function json($key=null, $default=null) {
        if (\is_null($key)) {
            return $this->json_data;
        }
        return (\array_key_exists($key, $this->json_data)) ?
            $this->json_data[$key] : $default;
    }

    public function session() {

    }

    public function cookies() {

    }

    public function file($key) {

    }

    public function has_file($key) {

    }

    public function meta($key=null) {
        if (\is_null($key)) {
            return $this->meta_data;
        }
        return $this->meta_data[$key];
    }

    public function has_meta(...$keys) {
        $meta = $this->meta();
        foreach ($keys as $key) {
            if (!\array_key_exists($key, $meta)) {
                return false;
            }
        }
        return true;
    }

    public function scheme() {
        return stripos($this->meta("server_protocol"), "https") === 0 ?
            "https://" : "http://";
    }

    public function host() {
        return $this->meta("http_host");
    }

    public function port() {
        return $this->meta("server_port");
    }

    public function is_secure() {
        return $this->scheme() === "https://";
    }

    private function load_get_data() {
        foreach($_GET as $key => $value) {
            $this->get_data[$key] = $value;
        }
    }

    private function load_post_data() {
        foreach($_POST as $key => $value) {
            $this->post_data[$key] = \filter_input(
                INPUT_POST,
                $key,
                FILTER_SANITIZE_SPECIAL_CHARS
            );
        }
    }

    private function load_json_data() {
        if ($this->method() == "POST"){
            if ($this->server_data["http_content_type"] === "application/json") {
                // takes raw data from the request
                $json = \file_get_contents("php://input");
                $this->json_input = \json_decode($json, true);
            }
        }
    }

    private function load_files_data() {
        // Reference:
        //      https://www.php.net/manual/en/reserved.variables.files.php
        $files = $_FILES;
        $files2 = [];
        foreach ($files as $input => $infoArr) {
            $filesByInput = [];
            foreach ($infoArr as $key => $valueArr) {
                if (\is_array($valueArr)) { // file input "multiple"
                    foreach($valueArr as $i=>$value) {
                        $filesByInput[$i][$key] = $value;
                    }
                } else { // -> string, normal file input
                    $filesByInput[] = $infoArr;
                    break;
                }
            }
            $files2 = \array_merge($files2,$filesByInput);
        }
        $filtered_files = [];
        // filter out any empty files and errors
        foreach($files2 as $file) {
            if (!$file["error"]) {
                $filtered_files[] = $file;
            }
        }
        $this->files_data = $filtered_files;
    }


    private function load_meta_data() {
        foreach ($_SERVER as $key => $value) {
            // formats keys
            $this->meta_data[\strtolower($key)] = $value;
        }
    }

}
