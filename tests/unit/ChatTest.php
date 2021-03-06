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
            ->addMethods(['query'])
            ->addMethods(['error'])
            ->addMethods(['results'])
            ->getMock();
        $this->dbMock->method('query')->willReturn($this->dbMock);
    }

    public function testSuccessfulSendMessage()
    {
        $_SESSION['user'] = 1;
        $this->dbMock->method('insert')->willReturn(true);
        $a = [];
        $a[0] = new StdClass();
        $a[0]->user_name = "Max";
        $a[0]->surname = "Maxim";
        $a[0]->receiver_id = 2;
        $a[0]->message_text = "Hey";
        $a[0]->sending_date = "2020-11-30 15:33:44";
        $this->dbMock->method('results')->willReturn($a);
        $chat = new Chat($this->dbMock);
        $message = "Hi! How are you?";
        $data = [
        'sender_id' => 1,
            'receiver_id' => 2,
            'message' => $message,
            'group' => true
        ];
        $this->assertEqualsCanonicalizing($a, $chat->sendMessage($data));
        unset($_SESSION['user']);
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

    public function testSuccessfulGetAllPrivateMessages() {
        $this->dbMock->method('error')->willReturn(false);
        $messages = array(
            'user_name' => 'Cindy',
            'surname' => 'Dindi',
            'receiver_id' => 15,
            'message_text' => 'Hey!',
            'sending_date' => '2020-11-30 15:33:44'
        );
        $this->dbMock->method('results')->willReturn($messages);

        $data = [
            'action' => 'get',
            'complement' => 'all_messages',
            'current_user_id' => 16,
            'another_user_id' => 15,
            'group' => false
        ];
        $chat = new Chat($this->dbMock);
        $this->assertEqualsCanonicalizing($messages, $chat->getAllMessages($data));
    }

    public function testFailedGetAllPrivateMessages() {
        $this->dbMock->method('error')->willReturn(true);

        $data = [
            'action' => 'get',
            'complement' => 'all_messages',
            'current_user_id' => 16,
            'another_user_id' => 15,
            'group' => false
        ];
        $chat = new Chat($this->dbMock);
        $this->assertEqualsCanonicalizing(false, $chat->getAllMessages($data));
    }

    public function testSuccessfulGetAllGroupMessages() {
        $this->dbMock->method('error')->willReturn(false);
        $messages = array(
            'user_name' => 'Cindy',
            'surname' => 'Dindi',
            'receiver_id' => 15,
            'message_text' => 'Hey!',
            'sending_date' => '2020-11-30 15:33:44'
        );
        $this->dbMock->method('results')->willReturn($messages);

        $data = [
            'action' => 'get',
            'complement' => 'all_messages',
            'current_user_id' => 16,
            'another_user_id' => 15,
            'group' => true
        ];
        $chat = new Chat($this->dbMock);
        $this->assertEqualsCanonicalizing($messages, $chat->getAllMessages($data));
    }

    public function testFailedGetAllGroupMessages() {
        $this->dbMock->method('error')->willReturn(true);

        $data = [
            'action' => 'get',
            'complement' => 'all_messages',
            'current_user_id' => 16,
            'another_user_id' => 15,
            'group' => true
        ];
        $chat = new Chat($this->dbMock);
        $this->assertEqualsCanonicalizing(false, $chat->getAllMessages($data));
    }

    public function testSuccessfulGetAllNewPrivateMessages() {
        $this->dbMock->method('error')->willReturn(false);
        $messages = array(
            'user_name' => 'Cindy',
            'surname' => 'Dindi',
            'receiver_id' => 15,
            'message_text' => 'Hey!',
            'sending_date' => '2020-11-30 15:33:44'
        );
        $this->dbMock->method('results')->willReturn($messages);

        $data = [
            'action' => 'get',
            'complement' => 'all_new_messages',
            'current_user_id' => 16,
            'another_user_id' => 15,
            'group' => false
        ];
        $chat = new Chat($this->dbMock);
        $this->assertEqualsCanonicalizing($messages, $chat->getAllMessages($data));
    }

    public function testFailedGetAllNewGroupMessages() {
        $this->dbMock->method('error')->willReturn(true);

        $data = [
            'action' => 'get',
            'complement' => 'all_new_messages',
            'current_user_id' => 16,
            'another_user_id' => 15,
            'group' => true
        ];
        $chat = new Chat($this->dbMock);
        $this->assertEqualsCanonicalizing(false, $chat->getAllMessages($data));
    }
}
