<?php


namespace Baobab\Middleware;


class AssetPreprocessor extends Middleware {

    public function handle($request) {
        $config = \config("asset_preprocessor");

    }

    private function compile_sass($src_dir, $dst_path) {

    }
}
