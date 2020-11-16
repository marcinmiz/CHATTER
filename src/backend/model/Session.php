<?php

namespace backend\model;


class Session
{
    public static function exists($name) {
        return (isset($_SESSION[$name])) ? true : false;
    }
    public static function put($name, $value) {
        return $_SESSION[$name] = $value;
    }

    public static function get($name) {
        return $_SESSION[$name];
    }

}