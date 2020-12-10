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
        $this->_isLoggedIn = false;

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
        $this->_db->query("UPDATE users SET account_active=? WHERE email='{$email}' AND activation_token='{$activation_code}'", array(true));
        if ($this->_db->get('users', array('email', '=', $email))->first()->account_active) {
            Session::flash('activation', 'You have activated an account!');
            Redirect::to('index.php');
        } else {
            Session::flash('wrong_code', 'Wrong Activation Code');
        }
    }

    public function login($email = null, $password = null, $remember = false) {

            $user = $this->find($email);
            if ($user) {
                if (Hash::verify($password, $this->data()->password)) {
                    if ($this->data()->account_active != true) {
                        Session::put('new_user_email', $email);
                        Session::flash('registration', 'Your account has not been activated yet!');
                        Redirect::to('activate.php');
                        return false;

                    }
                    Session::put($this->_sessionName, $this->data()->user_id);
                    if ($remember) {
                        $hash = Hash::unique();
                        $hashCheck = $this->_db->get('users_sessions', array('user_id', '=', $this->data()->user_id));

                        if (!$hashCheck->count()) {

                            $this->_db->insert('users_sessions', array(
                                'user_id' => $this->data()->user_id,
                                'hash' => $hash
                            ));
                        } else {
                            $hash = $hashCheck->first()->hash;

                        }

                        Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
                    }
                    return true;
                }
            }

        return false;
    }

    public function exists() {
        return (!empty($this->_data)) ? true : false;
    }

    public function updateLastActivity($user_id) {
        $this->_db->query('UPDATE users SET last_activity = now() WHERE user_id = '.$user_id);
        if (!$this->_db->error()) {
            return true;
        }
        return false;
    }

    public function getStatuses($user_id) {
        $this->_db->query('SELECT user_id, last_activity FROM users WHERE user_id!=? AND account_active=1', array($user_id));
        if (!$this->_db->error()) {
            $result = $this->_db->results();
            for ($i = 0; $i < $this->_db->count(); $i++) {
                $current_timestamp = strtotime(date('Y-m-d H:i:s') . '-10 second');
                $user_last_activity = strtotime($result[$i]->last_activity);
                if ($user_last_activity > $current_timestamp)
                {
                    $result[$i]->last_activity = '<span class="badge badge-pill badge-success">Online</span>';

                }
                else
                {
                    $result[$i]->last_activity = '<span class="badge badge-pill badge-danger">Offline</span>';
                }
            }
            return $result;
        }
        return false;

    }

    public function findAll($user_id, $another_user_id, $fav) {
        $fav_users = $this->getAllFavouriteUsers($user_id, $another_user_id);
        $length = $this->_db->count();

        if ($fav_users !== false)
        {
            if ($fav)
            {
                for ($i=0; $i < $length; $i++)
                {
                    $fav_users[$i]->fav = true;
                }
                return $fav_users;
            }
            $this->_db->query('SELECT user_id, user_name, surname FROM users WHERE user_id!=? AND account_active=1', array($user_id));
            $results = $this->_db->results();
            $length2 = $this->_db->count();

            for ($i=0; $i < $length2; $i++)
            {
                for ($j=0; $j < $length; $j++)
                {
                    if ($fav_users[$j]->user_id === $results[$i]->user_id)
                    {
                        $results[$i]->fav = true;
                        break;
                    } else {
                        $results[$i]->fav = false;
                    }
                }
            }

            if (!$this->_db->error()) {
                return $this->_db->results();
            }
            return false;
        }
        return false;
    }

    public function markUserAsFavourite($liker_user_id, $popular_user_id, $icon) {
        if ($icon)
        {
            if (!$this->_db->query('INSERT INTO favourite_users VALUES(?,?)', array($liker_user_id, $popular_user_id))->error()) {
                return 1;
            }
            return 2;
        } else {
            if (!$this->_db->query('DELETE FROM favourite_users WHERE liker_user_id=? AND popular_user_id=?', array($liker_user_id, $popular_user_id))->error()) {
                return 3;
            }
            return 4;
        }

    }

    private function getAllFavouriteUsers($user_id, $another_user_id) {
        $this->_db->query('SELECT u.user_id, u.user_name, u.surname FROM favourite_users f INNER JOIN users u WHERE f.liker_user_id='.$user_id.' AND u.account_active=1 AND u.user_id = f.popular_user_id AND f.popular_user_id!='.$another_user_id, array($user_id));
        if (!$this->_db->error()) {
            return $this->_db->results();
        }
        return false;
    }
}