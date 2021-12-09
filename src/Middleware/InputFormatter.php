<?php


namespace Baobab\Middleware;


class InputFormatter extends Middleware {

    public function handle($request) {
        $input = $request->input();
        foreach ($input as $key => $value) {
            if (\is_string($value)) {
                $value = \trim($value);
                $value = ($value === "") ? null : $value;
                $value = \htmlspecialchars($value);
            }
            $request->replace($key, $value);
        }

        return $request;
    }

}
