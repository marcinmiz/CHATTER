<?php

namespace backend\model;


class User
{
    private $_db,
        $_data,
        $_sessionName,
        $_cookieName,
        $_isLoggedIn = false;

    public function __construct($user = null, $db = null)
    {
        if (!$db) {
            $this->_db = DB::getInstance();
        } else {
            $this->_db = $db;
        }
        $this->_sessionName = Config::get('session/session_name');
        $this->_cookieName = Config::get('remember/cookie_name');
        if (!$user) {
            if (Session::exists($this->_sessionName)) {
                $user = Session::get($this->_sessionName);
                if ($this->find($user)) {
                    $this->_isLoggedIn = true;
                } else {
                    $this->logout();
                }
            }
        } else {
            $this->find($user);
        }
    }

    public function find($user = null) {
        if ($user) {
            $field = (is_numeric($user)) ? 'user_id' : 'email';
            $data = $this->_db->get('users', array($field, '=', $user));

            if ($this->_db->count()) {
                $this->_data = $data->first();
                return true;
            }
        }
        return false;
    }

    public function logout() {

        $this->_db->delete('users_sessions', array('user_id', '=', Session::get($this->_sessionName)));

        Session::delete($this->_sessionName);
        Cookie::delete($this->_cookieName);
    }

    public function data() {
        return $this->_data;
    }

    public function isLoggedIn()
    {
        return $this->_isLoggedIn;
    }

}