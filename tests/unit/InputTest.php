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

    public function testIfItemNotExistNotFound()
    {
        $this->assertEquals('', Input::get('user_name'));
    }

    public function testIfPostItemExistFound()
    {
        $_POST['user_id'] = 1;
        $this->assertEquals(1, Input::get('user_id'));
        unset($_POST['user_id']);
    }

    public function testIfGetItemExistFound()
    {
        $_GET['user_name'] = 'Garfield';
        $this->assertEquals('Garfield', Input::get('user_name'));
        unset($_GET['user_name']);
    }
}
