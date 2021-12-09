<?php


namespace Baobab\Http\Responses;


class ResponseFactory extends Response {

    public function template($name, array $context=[], $status=200) {
        $response = new Response(render_template($name, $context), $status);
        return $response;
    }

    public function json(array $data) {
        $response = new JSONResponse($data);
        return $response;
    }

    public function download($file_path, $download_name=null, array $headers=[]) {
        $response = new FileDownloadResponse($file_path, $download_name, $headers);
        return $response;
    }

    public function file($file_path, $headers=null) {

    }

    public function streamed_download() {

    }

}
