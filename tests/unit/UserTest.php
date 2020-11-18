<?php

require_once __DIR__.'/../../src/backend/model/Cookie.php';
require_once __DIR__.'/../../src/backend/model/User.php';

use backend\model\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public $user, $dbMock, $mockPeople;

    protected function setUp(): void
    {
        $this->dbMock = $this->getMockBuilder('backend\model\Redirect')
            ->addMethods(['get'])
            ->addMethods(['count'])
            ->addMethods(['first'])
            ->addMethods(['delete'])
            ->addMethods(['insert'])
            ->addMethods(['query'])
            ->addMethods(['error'])
            ->getMock();

        $this->mockPeople = new \stdClass();
        $this->mockPeople->users_data = [];
        $this->mockPeople->users_data[0] = new StdClass();
        $this->mockPeople->users_data[0]->user_id = 1;
        $this->mockPeople->users_data[0]->user_name = 'Bob';
        $this->mockPeople->users_data[0]->surname = 'Marley';
        $this->mockPeople->users_data[0]->email = 'marley@gmail.com';
        $this->mockPeople->users_data[0]->password = '$2y$10$CNSDDKDnqIxuKl2Pj496TO8iniFtcKRPd3OaxyRjzi6jlOpH3ValS';
        $this->mockPeople->users_data[0]->activation_token = 'eufih3';
        $this->mockPeople->users_data[0]->account_active = 'true';
        $this->mockPeople->users_data[0]->joined = '2020-10-27 18:26:33';
        $this->mockPeople->users_data[0]->last_activity = '2020-11-11 17:02:42';

        $this->mockPeople->users_data[2] = new StdClass();
        $this->mockPeople->users_data[2]->user_id = 3;
        $this->mockPeople->users_data[2]->user_name = 'Max';
        $this->mockPeople->users_data[2]->surname = 'Maximowicz';
        $this->mockPeople->users_data[2]->email = 'max@yahoo.com';
        $this->mockPeople->users_data[2]->password = '$2y$10$fYXx3G9L5TygIvGfozvYD.ibv1vbXAvNTZEIxjDOWvyEG3NVld5YK';
        $this->mockPeople->users_data[2]->activation_token = '5ehd5dg9';
        $this->mockPeople->users_data[2]->account_active = 'true';
        $this->mockPeople->users_data[2]->joined = '2020-10-30 18:34:49';
        $this->mockPeople->users_data[2]->last_activity = '2020-11-12 16:20:00';

        $this->dbMock->method('get')->willReturn($this->dbMock);
        $this->dbMock->method('delete')->willReturn($this->dbMock);
        $this->dbMock->method('query')->willReturn($this->dbMock);
        $this->dbMock->method('insert')->willReturn(false);
        $this->user = new User(null, $this->dbMock);
    }

    public function testCurrentLoggedUserFound()
    {
        $_SESSION['user'] = 1;
        $this->dbMock->method('count')->willReturn(1);
        $this->dbMock->method('first')->willReturn($this->mockPeople->users_data[0]);
        $this->user = new User(null, $this->dbMock);
        self::assertTrue(isset($_SESSION['user']));
        self::assertTrue($this->user->isLoggedIn());
        unset($_SESSION['user']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testCurrentLoggedUserNotFound()
    {
        $_SESSION['user'] = 2;
        $this->dbMock->method('count')->willReturn(0);
        $this->user = new User(null, $this->dbMock);
        self::assertFalse(isset($_SESSION['user']));
        self::assertFalse($this->user->isLoggedIn());
        unset($_SESSION['user']);
    }

    public function testNullUserNotFound()
    {
        $this->assertFalse($this->user->find(null));
    }

    public function testUserNotFoundById()
    {
        $this->dbMock->method('count')->willReturn(0);
        $this->assertFalse($this->user->find(5));
    }

    public function testUserFoundById()
    {
        $this->dbMock->method('count')->willReturn(1);
        $this->dbMock->method('first')->willReturn($this->mockPeople->users_data[0]);
        $this->assertTrue($this->user->find(1));
    }

    public function testUserFoundByEmail()
    {
        $this->dbMock->method('count')->willReturn(1);
        $this->dbMock->method('first')->willReturn($this->mockPeople->users_data[2]);
        $this->assertTrue($this->user->find('max@yahoo.com'));
    }

    public function testIfThrowExceptionCreatingUser () {
        $this->expectException(Exception::class);
        $this->user->create(array('Nicolas', 'Miko', 'nicolas.miko@gmail.com'));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testCorrectActivationCode () {
        $this->dbMock->method('count')->willReturn(1);
        $this->dbMock->method('first')->willReturn($this->mockPeople->users_data[0]);
        $this->dbMock->method('error')->willReturn(false);
        $user2 = new User(1, $this->dbMock);
        $user2->activate('wgeff24r');
        $this->assertEquals('You have activated an account!', \backend\model\Session::flash('activation'));
        $this->assertContains(
            'Location: index.php', xdebug_get_headers()
        );
    }


    public function testIncorrectActivationCode () {
        $this->dbMock->method('count')->willReturn(1);
        $this->dbMock->method('first')->willReturn($this->mockPeople->users_data[0]);
        $this->dbMock->method('error')->willReturn(true);
        $user2 = new User(1, $this->dbMock);
        $user2->activate('r372rdiwo');
        $this->assertEquals('Wrong Activation Code', \backend\model\Session::flash('wrong_code'));
    }
}
