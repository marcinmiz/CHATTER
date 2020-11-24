<?php

require_once __DIR__.'/../../src/backend/model/Cookie.php';
require_once __DIR__.'/../../src/backend/rest_handlers/UserRestHandler.php';

use backend\rest_handlers\UserRestHandler;
use PHPUnit\Framework\TestCase;

class UserRestHandlerTest extends TestCase
{
    private $user;

    protected function setUp() : void
    {
        $this->user = $this->getMockBuilder('backend\model\Redirect')
            ->addMethods(['updateLastActivity'])
            ->addMethods(['getStatuses'])
            ->addMethods(['findAll'])
            ->addMethods(['find'])
            ->addMethods(['data'])
            ->getMock();
    }

    public function testSuccessfulHtmlEncoding()
    {
        $responseData = array(
            'user_id' => 1,
            'user_name' => 'David',
            'surname' => 'Davidson'
        );

        $rest_handler = new UserRestHandler($this->user);

        $this->assertEquals("<table border='1'><tr><td>user_id</td><td>1</td></tr><tr><td>user_name</td><td>David</td></tr><tr><td>surname</td><td>Davidson</td></tr></table>", $rest_handler->encodeHtml($responseData));
    }

    public function testSuccessfulJSONEncoding()
    {
        $responseData = array(
            'user_id' => 2,
            'user_name' => 'Anne',
            'surname' => 'Delaware'
        );

        $jsonResponse = json_encode($responseData);

        $rest_handler = new UserRestHandler($this->user);

        $this->assertEquals($jsonResponse, $rest_handler->encodeJson($responseData));
    }

    public function testSuccessfulXMLEncoding()
    {
        $responseData = array(
            'user_id' => 3,
            'user_name' => 'Robert',
            'surname' => 'Lawrence'
        );
//Set Line separator: LF
        $jsonResponse = '<?xml version="1.0"?>
<mobile><user_id>3</user_id><user_name>Robert</user_name><surname>Lawrence</surname></mobile>
';

        $rest_handler = new UserRestHandler($this->user);

        $this->assertEquals($jsonResponse, $rest_handler->encodeXml($responseData));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSuccessfulHtmlSelection()
    {
        $statusCode = 200;
        $responseData = array(
            'user_id' => 1,
            'user_name' => 'David',
            'surname' => 'Davidson'
        );

        $rest_handler = new UserRestHandler($this->user);
        $_SERVER['HTTP_ACCEPT'] = 'text/html';
        $htmlResponse = "html";
        $this->assertEquals($htmlResponse, $rest_handler->selectEncoding($statusCode, $responseData));
        unset($_SERVER['HTTP_ACCEPT']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSuccessfulJsonSelection()
    {
        $statusCode = 404;
        $responseData = array(
            'user_id' => 2,
            'user_name' => 'Anne',
            'surname' => 'Delaware'
        );

        $rest_handler = new UserRestHandler($this->user);
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $jsonResponse = "json";
        $this->assertEquals($jsonResponse, $rest_handler->selectEncoding($statusCode, $responseData));
        unset($_SERVER['HTTP_ACCEPT']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSuccessfulXmlSelection()
    {
        $statusCode = 202;
        $responseData = array(
            'user_id' => 3,
            'user_name' => 'Robert',
            'surname' => 'Lawrence'
        );

        $rest_handler = new UserRestHandler($this->user);
        $_SERVER['HTTP_ACCEPT'] = 'application/xml';
        $jsonResponse = "xml";
        $this->assertEquals($jsonResponse, $rest_handler->selectEncoding($statusCode, $responseData));
        unset($_SERVER['HTTP_ACCEPT']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSuccessfulLastActivityUpdating () {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $this->user->method('updateLastActivity')->willReturn(true);
        $rest_handler = new UserRestHandler($this->user);
        $this->assertEquals(200, $rest_handler->updateLastActivity());
        unset($_SERVER['HTTP_ACCEPT']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testFailedLastActivityUpdating () {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $this->user->method('updateLastActivity')->willReturn(false);
        $rest_handler = new UserRestHandler($this->user);
        $this->assertEquals(404, $rest_handler->updateLastActivity());
        unset($_SERVER['HTTP_ACCEPT']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testFailedGetStatuses () {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $this->user->method('getStatuses')->willReturn(false);
        $rest_handler = new UserRestHandler($this->user);
        $this->assertEquals(404, $rest_handler->getStatuses());
        unset($_SERVER['HTTP_ACCEPT']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSuccessfulGetStatuses () {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $user = new StdClass();
        $user->user_id = 1;
        $user->last_activity = '<span class="badge badge-pill badge-success">Online</span>';
        $a = array($user);
        $this->user->method('getStatuses')->willReturn($a);
        $rest_handler = new UserRestHandler($this->user);
        $this->assertEquals(200, $rest_handler->getStatuses());
        unset($_SERVER['HTTP_ACCEPT']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSuccessfulFindAllUsers () {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        $user = new StdClass();
        $user->user_id = 27;
        $user->user_name = "Xavier";
        $user->surname = "Hernandez";

        $user2 = new StdClass();
        $user2->user_id = 58;
        $user2->user_name = "Charles";
        $user2->surname = "Dounton";

        $a = array($user, $user2);
        $this->user->method('findAll')->willReturn($a);
        $rest_handler = new UserRestHandler($this->user);
        $this->assertEquals(200, $rest_handler->getAllUsers());
        unset($_SERVER['HTTP_ACCEPT']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testFailedFindAllUsers () {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $this->user->method('getStatuses')->willReturn(false);
        $rest_handler = new UserRestHandler($this->user);
        $this->assertEquals(404, $rest_handler->getAllUsers());
        unset($_SERVER['HTTP_ACCEPT']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSuccessfulFindUser () {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        $user_id = 27;

        $user = new StdClass();
        $user->user_id = 27;
        $user->user_name = "Xavier";
        $user->surname = "Hernandez";
        $user->email = "xavier@gmail.com";

        $a = array($user);
        $this->user->method('find')->willReturn(true);
        $this->user->method('data')->willReturn($a);
        $rest_handler = new UserRestHandler($this->user);
        $this->assertEquals(200, $rest_handler->getUser($user_id));
        unset($_SERVER['HTTP_ACCEPT']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testFailedFindUser () {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        $user_id = 30;

        $this->user->method('find')->willReturn(false);
        $rest_handler = new UserRestHandler($this->user);
        $this->assertEquals(404, $rest_handler->getUser($user_id));
        unset($_SERVER['HTTP_ACCEPT']);
    }
}
