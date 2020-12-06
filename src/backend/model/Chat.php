<?php

namespace backend\model;


class Chat
{
    private $_db;

    public function __construct($_db = null)
    {
        ($_db == null) ? $this->_db = DB::getInstance() : $this->_db = $_db;
    }

    public function sendMessage($data) {

        if ($data['group'])
        {
            $table = 'group_messages';

        } else {
            $table = 'private_messages';
        }

        if($this->_db->insert($table,
            [
                'sender_id' => $data['sender_id'],
                'receiver_id' => $data['receiver_id'],
                'message_text' => $data['message'],
                'sending_date' => date('Y-m-d H:i:s'),
                'new' => true
            ]))
        {
            $error = $this->_db->error();
            $sql = "SELECT u.user_name, u.surname, m.receiver_id, m.message_text, m.sending_date FROM users u INNER JOIN ". $table ." m on u.user_id = m.sender_id WHERE sender_id = ? ORDER BY sending_date DESC LIMIT 1";
            $this->_db->query($sql, [Session::get('user')]);
            if (!$error && !$this->_db->error())
            {
                return $this->_db->results();
            }
        }

        return false;
    }

    public function getAllMessages($data) {

        if ($data['group'] === true)
        {
            $table = "group_messages";

        } else {

            $table = "private_messages";
        }

        $sql = "UPDATE ". $table ." SET new = ? WHERE (sender_id = ? OR receiver_id = ?) AND (sender_id = ? OR receiver_id = ?) AND new = true";
        $params = [false, $data['current_user_id'], $data['current_user_id'], $data['another_user_id'], $data['another_user_id']];

        $this->_db->query($sql, $params);

        $sql = "SELECT u.user_name, u.surname, m.receiver_id, m.message_text, m.sending_date FROM users u INNER JOIN ". $table ." m on u.user_id = m.sender_id WHERE (sender_id = ? OR receiver_id = ?) AND (sender_id = ? OR receiver_id = ?) ORDER BY sending_date ASC";
        $params = [$data['current_user_id'], $data['current_user_id'], $data['another_user_id'], $data['another_user_id']];

        $result = $this->_db->query($sql, $params);
        if(!$result->error())
        {
            return $result->results();
        }

        return false;
    }

    public function getAllNewMessages($data) {

        if ($data['group'] === true)
        {
            $table = "group_messages";

        } else {

            $table = "private_messages";
        }

        $sql = "SELECT u.user_name, u.surname, m.receiver_id, m.message_text, m.sending_date FROM users u INNER JOIN ". $table ." m on u.user_id = m.sender_id WHERE receiver_id = ? AND sender_id = ? AND m.new = true ORDER BY sending_date ASC";
        $params = [$data['current_user_id'], $data['another_user_id']];

        $result = $this->_db->query($sql, $params);
        $error = $result->error();
        $reply = $result->results();

        $sql = "UPDATE ". $table ." SET new = ? WHERE receiver_id = ? AND sender_id = ? AND new = true";
        $params = [false, $data['current_user_id'], $data['another_user_id']];

        $this->_db->query($sql, $params);

        if(!$error)
        {
            return $reply;
        }

        return false;
    }
}