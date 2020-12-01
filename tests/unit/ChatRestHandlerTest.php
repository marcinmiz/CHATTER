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
            ->addMethods(['getAllMessages'])
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
    /**
     * @test
     * @runInSeparateProcess
     **/
    public function testSuccessfulGetAllMessages () {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        $data = [
            'action' => 'get',
            'complement' => 'all_messages',
            'current_user_id' => 16,
            'another_user_id' => 15,
            'group' => false
        ];

        $messages = array(
            'user_name' => 'Cindy',
            'surname' => 'Dindi',
            'receiver_id' => 15,
            'message_text' => 'Hey!',
            'sending_date' => '2020-11-30 15:33:44'
        );

        $this->chat->method('getAllMessages')->willReturn($messages);
        $rest_handler = new ChatRestHandler($this->chat);
        $this->assertEquals(200, $rest_handler->getAllMessages($data));
        unset($_SERVER['HTTP_ACCEPT']);
    }

    /**
     * @test
     * @runInSeparateProcess
     **/
    public function testFailedGetAllMessages () {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        $data = [
            'action' => 'get',
            'complement' => 'all_messages',
            'current_user_id' => 16,
            'another_user_id' => 15,
            'group' => false
        ];

        $this->chat->method('getAllMessages')->willReturn(false);
        $rest_handler = new ChatRestHandler($this->chat);
        $this->assertEquals(404, $rest_handler->getAllMessages($data));
        unset($_SERVER['HTTP_ACCEPT']);
    }
}
