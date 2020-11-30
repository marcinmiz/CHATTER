<?php

use backend\model\Chat;
use PHPUnit\Framework\TestCase;

class ChatTest extends TestCase
{
    private $dbMock;
    protected function setUp(): void
    {
        $this->dbMock = $this->getMockBuilder('backend\model\Redirect')
            ->addMethods(['insert'])
            ->getMock();

    }

    public function testSuccessfulSendMessage()
    {
        $this->dbMock->method('insert')->willReturn(true);
        $chat = new Chat($this->dbMock);
        $message = "Hi! How are you?";
        $data = [
        'sender_id' => 1,
            'receiver_id' => 2,
            'message' => $message,
            'group' => true
        ];
        $this->assertTrue($chat->sendMessage($data));
    }

    public function testFailedSendMessage()
    {
        $this->dbMock->method('insert')->willReturn(false);
        $chat = new Chat($this->dbMock);
        $message = "Hi! How are you?";
        $data = [
            'sender_id' => 6,
            'receiver_id' => 5,
            'message' => $message,
            'group' => true
        ];
        $this->assertFalse($chat->sendMessage($data));
    }
}
