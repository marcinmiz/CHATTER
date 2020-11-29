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

        if($this->_db->insert('private_messages',
            [
                'sender_id' => $data['sender_id'],
                'receiver_id' => $data['receiver_id'],
                'message_text' => $data['message'],
                'sent_date' => date('Y-m-d H:i:s'),
                'new' => true
            ]))
        {
            return true;
        }

        return false;
    }
}