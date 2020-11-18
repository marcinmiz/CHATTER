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

    public function create($fields = array()) {
        if (!$this->_db->insert('users', $fields)) {
            throw new \Exception('There was a problem creating an account');
        }
    }

    public function send_mail($to_email, $activation_token) {

        $subject = "Account Activation Code";

        $headers = "From: Web chat App \r\n";
        $headers .= "Reply-To: noreply@gmail.com \r\n";
        $headers .= "MIME-Version: 1.0 \r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8 \r\n";
        $message = '<html><body>';
        $message .= '<h1>Your Activation Code</h1>';
        $message .= '<h3>' . $activation_token . '</h3>';
        $message .= '</body></html>';

        if (mail($to_email, $subject, $message, $headers)) {
            return true;
        } else {
            return false;
        }
    }

    public function activate($activation_code) {
        $email = $this->data()->email;
        if (!$this->_db->query("UPDATE users SET account_active=? WHERE email='{$email}' AND activation_token='{$activation_code}'", array(true))->error()) {
            Session::flash('activation', 'You have activated an account!');
            Redirect::to('index.php');
        } else {
            Session::flash('wrong_code', 'Wrong Activation Code');
        }
    }


}