<?php

require_once __DIR__.'/../../src/backend/model/Input.php';

use backend\model\Input;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{

    public function testIfRequestTypeWrong()
    {
        $this->assertFalse(Input::exists('delete'));
    }

    public function testIfRequestTypePostEmpty()
    {
        $this->assertFalse(Input::exists('post'));
    }

    public function testIfRequestTypePostNotEmpty()
    {
        $_POST['email'] = 'Carl.Munich@yahoo.com';
        $this->assertTrue(Input::exists('post'));
        unset($_POST['email']);
    }

    public function testIfRequestTypeGetEmpty()
    {
        $this->assertFalse(Input::exists('get'));
    }

    public function testIfRequestTypeGetNotEmpty()
    {
        $_GET['surname'] = 'Henderson';
        $this->assertTrue(Input::exists('get'));
        unset($_GET['surname']);
    }
}
