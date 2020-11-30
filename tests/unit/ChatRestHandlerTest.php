<?php

use backend\rest_handlers\ChatRestHandler;
use PHPUnit\Framework\TestCase;

class ChatRestHandlerTest extends TestCase
{

    private $chat;

    protected function setUp(): void
    {
        $this->chat = $this->getMockBuilder('backend\model\Redirect')
            ->addMethods(['sendMessage'])
            ->getMock();

    }

    /**
     * @test
     * @runInSeparateProcess
     **/
    public function testSuccessfulSendMessage()
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $this->chat->method('sendMessage')->willReturn(true);
        $restHandler = new ChatRestHandler($this->chat);
        $message = "Hi! How are you?";
        $data = [
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => $message,
            'group' => true
        ];
        $this->assertEquals(200, $restHandler->sendMessage($data));
        unset($_SERVER['HTTP_ACCEPT']);
    }

    /**
     * @test
     * @runInSeparateProcess
     **/
    public function testFailedSendMessage()
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $this->chat->method('sendMessage')->willReturn(false);
        $restHandler = new ChatRestHandler($this->chat);
        $message = "Hi! How are you?";
        $data = [
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => $message,
            'group' => true
        ];
        $this->assertEquals(404, $restHandler->sendMessage($data));
        unset($_SERVER['HTTP_ACCEPT']);
    }
}
