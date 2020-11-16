<?php

require_once __DIR__.'/../../src/backend/model/Config.php';

use backend\model\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public $config;

    protected function setUp() : void
    {
        $GLOBALS['config'] = array(
            'mysql' => array(
                'host' => '127.0.0.1',
                'username' => 'root',
                'password' => '',
                'db' => 'chat'
            ),
            'remember' => array(
                'cookie_name' => 'hash',
                'cookie_expiry' => 604800
            ),
            'session' => array(
                'session_name' => 'user',
                'token_name' => 'token'
            ),
        );
        $this->config = new Config();
    }


    public function testIfPathNullNotFoundAnything()
    {
        $this->assertFalse($this->config->get(null));
    }

    public function testIfPathIncorrectNotFoundAnything()
    {
        $this->assertFalse($this->config->get('session/session_date'));
    }

    public function testIfPathCorrectFoundValue()
    {
        $this->assertEquals('root', $this->config->get('mysql/username'));
    }

}
