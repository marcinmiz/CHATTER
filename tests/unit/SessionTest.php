<?php

require_once __DIR__.'/../../src/backend/model/Session.php';
use backend\model\Session;

class SessionTest extends PHPUnit\Framework\TestCase
{

    public function testIfCreateNonExistingSessionVariable()
    {
        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }
        $session = new Session();
        $session->flash('user_id', 3);
        $this->assertEquals(3,  $_SESSION['user_id']);
        unset($_SESSION['user_id']);
    }

    public function testIfReturnExistingSessionVariable()
    {
        $_SESSION['user_name'] = 'Alex';
        $session = new Session();
        $this->assertEquals('Alex', $session->flash('user_name', 'Alex'));
    }
}
