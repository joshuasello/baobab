<?php


namespace Baobab\Utils;


class Collection {

    protected $data;

    function __construct(array $data) {
        $this->data = $data;
    }

    public function data() {
        return $this->data;
    }

    public function sum() {
        $result = 0;
        foreach ($this->data as $key => $value) {
            $result += $value;
        }
        return $result;
    }

    public function count() {
        return \count($this->data);
    }

    public function mean() {
        return $this->sum() / $this->count();
    }

    public function median() {
        $count = \$this->count();
        $ind = \intdiv($count, 2);
        if ($count % 2 === 0) {
            return ($this->data[$ind] + $this->data[$ind + 1]) / 2;
        }
        return $this->data[$ind + 1];
    }

    public function segment($size) {

    }

    public function flatten() {

    }

    public function pair() {

    }

    public function append() {

    }

    public function has($key) {

    }

    public function has_strict($key) {

    }

    public function len() {

    }

    public function count_occurences() {

    }

    public function cross_join() {

    }

    public function difference() {

    }

    public function assoc_difference() {

    }

    public function keys_difference() {

    }

    public function dump() {

    }

    public function duplicates($strict=false) {

    }

    public function each() {

    }

    public function every() {

    }

    public function except() {

    }

}
