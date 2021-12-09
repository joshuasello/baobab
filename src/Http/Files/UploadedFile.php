<?php


namespace Baobab\Http\Files;


class UploadedFile {

    const ERRORS = [
        UPLOAD_ERR_INI_SIZE => 'The file "%s" exceeds your upload_max_filesize ini directive (limit is %d KiB).',
        UPLOAD_ERR_FORM_SIZE => 'The file "%s" exceeds the upload limit defined in your form.',
        UPLOAD_ERR_PARTIAL => 'The file "%s" was only partially uploaded.',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        UPLOAD_ERR_CANT_WRITE => 'The file "%s" could not be written on disk.',
        UPLOAD_ERR_NO_TMP_DIR => 'File could not be uploaded: missing temporary directory.',
        UPLOAD_ERR_EXTENSION => 'File upload was stopped by a PHP extension.',
    ];

    private $original_name;
    private $mime_type;
    private $size;
    private $error;

    function __construct($path, $original_name, , $error, $size) {
        $this->original_name = \basename($file["name"]);
        $this->mime_type = $mime_type ?: 'application/octet-stream';
        $this->size = $size;
        $this->error = $error ?: UPLOAD_ERR_OK;
    }

    public function store($path) {

    }

    public function store_as($path, $file_name) {

    }

    public function client_original_name() {
        return $this->original_name;
    }

    public function client_original_extension() {
        return return \pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    public function client_mime_type() {
        return $this->mime_type;
    }

    public function client_size() {
        return $this->size;
    }

    public function error() {
        return $this->error;
    }

    public function error_message() {
        $error_code = $this->error;
        $max_filesize = $error_code === UPLOAD_ERR_INI_SIZE ?
            self::max_file_size() / 1024 : 0;
        $message = isset(self::ERRORS[$error_code]) ?
            self::ERRORS[$error_code] :
            'The file "%s" was not uploaded due to an unknown error.';
        return sprintf($message, $this->client_original_name(), $max_filesize);
    }

    public function guess_extension() {

    }

    public function is_valid() {

    }

    public function path() {

    }

    public function move($directory, $name=null) {

    }


    public static function max_file_size() {
        $ini_max = strtolower(ini_get('upload_max_filesize'));
        if ('' === $ini_max) {
            return PHP_INT_MAX;
        }
        $max = ltrim($ini_max, '+');
        if (0 === strpos($max, '0x')) {
            $max = intval($max, 16);
        } elseif (0 === strpos($max, '0')) {
            $max = intval($max, 8);
        } else {
            $max = (int) $max;
        }
        switch (substr($ini_max, -1)) {
            case 't': $max *= 1024;
            case 'g': $max *= 1024;
            case 'm': $max *= 1024;
            case 'k': $max *= 1024;
        }
        return $max;
    }

}
