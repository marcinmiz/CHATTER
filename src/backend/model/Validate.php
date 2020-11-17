<?php

namespace backend\model;


class Validate
{
    private $_passed = false,
        $_errors = array(),
        $_db = null;

    public function __construct($db = null)
    {
        if (!$db) {
            $this->_db = DB::getInstance();
        } else {
            $this->_db = $db;
        }
    }

    private function addError($error) {
        $this->_errors[] = $error;
    }

    public function errors() {
        return $this->_errors;
    }

    public function passed() {
        return $this->_passed;
    }
}