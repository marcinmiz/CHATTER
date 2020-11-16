<?php

namespace backend\model;


class Hash
{
    public static function make($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verify($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function unique() {
        return self::make(uniqid());
    }
}