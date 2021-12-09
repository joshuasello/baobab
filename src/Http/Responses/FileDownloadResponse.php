<?php


namespace Baobab\Http\Responses;


class FileDownloadResponse extends Response {

    private $local_file_path;
    private $delete_after_download=false;

    function __construct($local_file_path, $download_name=null, array $headers=[]) {
        if (!\file_exists($local_file_path) and \is_file($local_file_path)) {
            throw new \Exception("File '$local_file_path' to download does not exist.", 1);
        }
        parent::__construct();

        $download_name = (\is_null($download_name)) ?
            basename($local_file_path) : $download_name;

        $this->with_headers($headers);
        $this->with_headers([
            "Content-Description" => "File Transfer",
            "Content-Type" => "application/octet-stream",
            "Content-Disposition" => "attachment; filename='$download_name'",
            "Expires" => "0",
            "Cache-Control" => "must-revalidate",
            "Pragma" => "public",
            "Content-Length" => filesize($local_file_path)
        ], true);

        $this->local_file_path = $local_file_path;
    }

    public function delete_after() {
        $this->delete_after_download = true;
    }

    public function ship() {
        parent::ship();
        readfile($this->local_file_path);
        if ($this->delete_after_download) {
            \unlink($this->local_file_path);
        }
    }

}
