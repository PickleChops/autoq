<?php

/**
 * Fetch value from array or return default
 * @param $key
 * @param $arr
 * @param null $default
 * @return mixed|null
 */
function array_get($key, $arr, $default = null) {
    return array_key_exists($key, $arr) ? $arr[$key] : $default;
}

/**
 * Dump and die
 * @param $var
 */
function dd($var) {
    var_dump($var);
    die();
}