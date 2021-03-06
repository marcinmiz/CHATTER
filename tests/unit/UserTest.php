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
            ->addMethods(['results'])
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
        $this->mockPeople->users_data[0]->account_active = true;
        $this->mockPeople->users_data[0]->joined = '2020-10-27 18:26:33';
        $this->mockPeople->users_data[0]->last_activity = '2020-11-11 17:02:42';

        $this->mockPeople->users_data[1] = new StdClass();
        $this->mockPeople->users_data[1]->user_id = 2;
        $this->mockPeople->users_data[1]->user_name = 'Max';
        $this->mockPeople->users_data[1]->surname = 'Maximowicz';
        $this->mockPeople->users_data[1]->email = 'max@yahoo.com';
        $this->mockPeople->users_data[1]->password = '$2y$10$awYyCZUv19shfZ25S.ieounGNzitgz1IhFvxfjtHnykv.5aGgIpHa';
        $this->mockPeople->users_data[1]->activation_token = '5ehd5dg9';
        $this->mockPeople->users_data[1]->account_active = false;
        $this->mockPeople->users_data[1]->joined = '2020-10-30 18:34:49';
        $this->mockPeople->users_data[1]->last_activity = '2020-11-12 16:20:00';

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
        $this->dbMock->method('first')->willReturn($this->mockPeople->users_data[1]);
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
        $this->mockPeople->users_data[0]->account_active = true;
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
        $this->mockPeople->users_data[0]->account_active = false;
        $this->dbMock->method('count')->willReturn(1);
        $this->dbMock->method('first')->willReturn($this->mockPeople->users_data[0]);
        $this->dbMock->method('error')->willReturn(true);
        $user2 = new User(1, $this->dbMock);
        $user2->activate('r372rdiwo');
        $this->assertEquals('Wrong Activation Code', \backend\model\Session::flash('wrong_code'));
    }

    public function testLoginWithIncorrectEmail() {
        $this->dbMock->method('count')->willReturn(0);
        $email = 'sylvester@onet.pl';
        $password = 'ndowqfnwef';
        $remember = 'false';
        $this->assertFalse(false, $this->user->login($email, $password, $remember));
    }

    public function testLoginWithIncorrectPassword() {
        $this->dbMock->method('count')->willReturn(1);
        $this->dbMock->method('first')->willReturn($this->mockPeople->users_data[1]);
        $email = 'max@yahoo.com';
        $password = 'ndowqfnwef';
        $remember = 'false';
        $this->assertFalse(false, $this->user->login($email, $password, $remember));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testLoginInactiveAccount() {
        $this->dbMock->method('count')->willReturn(1);
        $this->dbMock->method('first')->willReturn($this->mockPeople->users_data[1]);
        $email = 'max@yahoo.com';
        $password = 'qwerty';
        $remember = false;
        $this->assertFalse(false, $this->user->login($email, $password, $remember));
        $this->assertEquals('Your account has not been activated yet!', \backend\model\Session::flash('registration'));
        $this->assertContains(
            'Location: activate.php', xdebug_get_headers()
        );
    }

    public function testLoginActiveAccountWithoutRememberMe() {
        $this->mockPeople->users_data[1]->account_active = true;
        $this->dbMock->method('count')->willReturn(1);
        $this->dbMock->method('first')->willReturn($this->mockPeople->users_data[1]);
        $email = 'max@yahoo.com';
        $password = 'qwerty';
        $remember = false;
        $this->assertTrue(true, $this->user->login($email, $password, $remember));
        $this->assertTrue(true, \backend\model\Session::exists('user'));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSuccessfulUpdateLastActivity() {
        $user_id = 60;

        $this->dbMock->method('error')->willReturn(false);
        $this->dbMock->method('count')->willReturn(1);
        $this->dbMock->method('first')->willReturn($this->mockPeople->users_data[1]);
        $user = new User(3, $this->dbMock);
        $this->assertTrue($user->updateLastActivity($user_id));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testFailedUpdateLastActivity() {
        $user_id = 30;

        $this->dbMock->method('error')->willReturn(true);
        $this->dbMock->method('count')->willReturn(1);
        $this->dbMock->method('first')->willReturn($this->mockPeople->users_data[1]);
        $user = new User(3, $this->dbMock);
        $this->assertFalse($user->updateLastActivity($user_id));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testGetStatusesQueryExecutionProblem() {
        $this->dbMock->method('error')->willReturn(true);

        $data = array();
        $data['action'] = 'get';
        $data['complement'] = 'statuses';
        $data['current_user_id'] = 30;
        $data['ids'] = [24,57,43,66];

        $user = new User(null, $this->dbMock);
        $_SESSION['user'] = 1;
        $this->assertFalse($user->getStatuses($data));
        unset($_SESSION['user']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testGetStatusesUserLastActivityLessThan10seconds() {
        $this->dbMock->method('error')->willReturn(false);

        $data = array();
        $data['action'] = 'get';
        $data['complement'] = 'statuses';
        $data['current_user_id'] = 60;
        $data['ids'] = [1,2];

        $user1Time = new StdClass();
        $user1Time->user_id = 1;
        $user1Time->last_activity = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '-8 second'));

        $user2Time = new StdClass();
        $user2Time->user_id = 2;
        $user2Time->last_activity = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '-9 second'));

        $a1 = array($user1Time, $user2Time);
        $this->dbMock->method('results')->willReturn($a1);
        $this->dbMock->method('count')->willReturn(2);
        $user = new User(null, $this->dbMock);
        $_SESSION['user'] = 1;

        $user1Badge = new StdClass();
        $user1Badge->user_id = 1;
        $user1Badge->last_activity = '<span class="badge badge-pill badge-success">Online</span>';

        $user2Badge = new StdClass();
        $user2Badge->user_id = 2;
        $user2Badge->last_activity = '<span class="badge badge-pill badge-success">Online</span>';

        $a2 = array($user1Badge, $user2Badge);
        $this->assertEqualsCanonicalizing($a2, $user->getStatuses($data));
        unset($_SESSION['user']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testGetStatusesUserLastActivityEqualTo10seconds() {
        $this->dbMock->method('error')->willReturn(false);

        $data = array();
        $data['action'] = 'get';
        $data['complement'] = 'statuses';
        $data['current_user_id'] = 60;
        $data['ids'] = [1,2];

        $user1Time = new StdClass();
        $user1Time->user_id = 1;
        $user1Time->last_activity = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '-10 second'));

        $user2Time = new StdClass();
        $user2Time->user_id = 2;
        $user2Time->last_activity = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '-10 second'));

        $a1 = array($user1Time, $user2Time);
        $this->dbMock->method('results')->willReturn($a1);
        $this->dbMock->method('count')->willReturn(2);
        $user = new User(null, $this->dbMock);
        $_SESSION['user'] = 1;

        $user1Badge = new StdClass();
        $user1Badge->user_id = 1;
        $user1Badge->last_activity = '<span class="badge badge-pill badge-danger">Offline</span>';

        $user2Badge = new StdClass();
        $user2Badge->user_id = 2;
        $user2Badge->last_activity = '<span class="badge badge-pill badge-danger">Offline</span>';

        $a2 = array($user1Badge, $user2Badge);
        $this->assertEqualsCanonicalizing($a2, $user->getStatuses($data));
        unset($_SESSION['user']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testGetStatusesUserLastActivityMoreThan10seconds() {
        $this->dbMock->method('error')->willReturn(false);

        $data = array();
        $data['action'] = 'get';
        $data['complement'] = 'statuses';
        $data['current_user_id'] = 60;
        $data['ids'] = [1,2];

        $user1Time = new StdClass();
        $user1Time->user_id = 1;
        $user1Time->last_activity = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '-11 second'));

        $user2Time = new StdClass();
        $user2Time->user_id = 2;
        $user2Time->last_activity = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '-12 second'));

        $a1 = array($user1Time, $user2Time);
        $this->dbMock->method('results')->willReturn($a1);
        $this->dbMock->method('count')->willReturn(2);
        $user = new User(null, $this->dbMock);
        $_SESSION['user'] = 1;

        $user1Badge = new StdClass();
        $user1Badge->user_id = 1;
        $user1Badge->last_activity = '<span class="badge badge-pill badge-danger">Offline</span>';

        $user2Badge = new StdClass();
        $user2Badge->user_id = 2;
        $user2Badge->last_activity = '<span class="badge badge-pill badge-danger">Offline</span>';

        $a2 = array($user1Badge, $user2Badge);
        $this->assertEqualsCanonicalizing($a2, $user->getStatuses($data));
        unset($_SESSION['user']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testFindAllUsersNobodyFound() {
        $this->dbMock->method('error')->willReturn(false);

        $user_id = 30;

        $this->dbMock->method('count')->willReturn(0);
        $user = new User(null, $this->dbMock);
        $_SESSION['user'] = 1;

        $this->assertNull($user->findAll($user_id, 0, 0));
        unset($_SESSION['user']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testFindAllUsersQueryExecutionError() {
        $this->dbMock->method('error')->willReturn(true);

        $user_id = 30;

        $this->dbMock->method('count')->willReturn(2);
        $user = new User(null, $this->dbMock);
        $_SESSION['user'] = 1;

        $this->assertFalse($user->findAll($user_id, 0, 0));
        unset($_SESSION['user']);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testFindAll2Users() {
        $this->dbMock->method('error')->willReturn(false);

        $user_id = 60;

        $user1Time = new StdClass();
        $user1Time->user_id = 1;
        $user1Time->user_name = 'Fred';
        $user1Time->surname = 'Sukkothai';

        $user2Time = new StdClass();
        $user2Time->user_id = 2;
        $user2Time->user_name = 'Walter';
        $user2Time->surname = 'Erwin';

        $a1 = array($user1Time, $user2Time);

        $this->dbMock->method('results')->willReturn($a1);
        $this->dbMock->method('count')->willReturn(2);
        $user = new User(null, $this->dbMock);
        $_SESSION['user'] = 3;

        $this->assertEqualsCanonicalizing($a1, $user->findAll($user_id, 0, 0));
        unset($_SESSION['user']);
    }

    /**
     * @test
     * @runInSeparateProcess
     **/
    public function testSuccessfulMarkUserAsFavourite() {
        $this->dbMock->method('error')->willReturn(false);
        $user = new User(null, $this->dbMock);
        $this->assertEquals(1, $user->markUserAsFavourite(1,2,1));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testFailedMarkUserAsFavourite() {
        $this->dbMock->method('error')->willReturn(true);
        $user = new User(null, $this->dbMock);
        $this->assertEquals(2, $user->markUserAsFavourite(1,2,1));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSuccessfulUnmarkUserAsFavourite() {
        $this->dbMock->method('error')->willReturn(false);
        $user = new User(null, $this->dbMock);
        $this->assertEquals(3, $user->markUserAsFavourite(1,2,0));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testFailedUnmarkUserAsFavourite() {
        $this->dbMock->method('error')->willReturn(true);
        $user = new User(null, $this->dbMock);
        $this->assertEquals(4, $user->markUserAsFavourite(1,2,0));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSuccessfulGet2FavouriteUsers() {
        $this->dbMock->method('error')->willReturn(false);
        $a = [];
        $a[0] = new StdClass();
        $a[0]->user_id = 1;
        $a[0]->user_name = "Peter";
        $a[0]->surname = "Parker";
        $a[1] = new StdClass();
        $a[1]->user_id = 2;
        $a[1]->user_name = "George";
        $a[1]->surname = "Smith";
        $this->dbMock->method('results')->willReturn($a);
        $this->dbMock->method('count')->willReturn(2);
        $user = new User(null, $this->dbMock);
        $this->assertEqualsCanonicalizing($a,  $user->findAll(3,4, 1));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSuccessfulGetNoneFavouriteUsers() {
        $this->dbMock->method('error')->willReturn(false);
        $this->dbMock->method('results')->willReturn([]);
        $this->dbMock->method('count')->willReturn(0);
        $user = new User(null, $this->dbMock);
        $this->assertEqualsCanonicalizing([],  $user->findAll(3,4, 1));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testFailedGetFavouriteUsers() {
        $this->dbMock->method('error')->willReturn(true);
        $this->dbMock->method('count')->willReturn(1);
        $user = new User(null, $this->dbMock);
        $this->assertFalse($user->findAll(3,4, 1));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSearchUsers2Found() {
        $this->dbMock->method('error')->willReturn(false);
        $a[0] = new StdClass();
        $a[0]->user_id = 1;
        $a[0]->user_name = "Peter";
        $a[0]->surname = "Parker";
        $a[1] = new StdClass();
        $a[1]->user_id = 2;
        $a[1]->user_name = "Peter";
        $a[1]->surname = "Smith";
        $this->dbMock->method('results')->willReturn($a);
        $user = new User(null, $this->dbMock);
        $data = [
            'action' => 'search',
            'complement' => 'users',
            'current_user_id' => 12,
            'another_user_id' => 0,
            'key' => 'Peter',
            'online' => false,
            'favourite' => false
        ];
        $this->assertEquals($a, $user->searchUsers($data));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testFailedSearchUsersDBError() {
        $this->dbMock->method('error')->willReturn(true);
        $user = new User(null, $this->dbMock);
        $data = [
            'action' => 'search',
            'complement' => 'users',
            'current_user_id' => 12,
            'another_user_id' => 25,
            'key' => 'Johnson',
            'online' => false,
            'favourite' => false
        ];
        $this->assertEquals(false, $user->searchUsers($data));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testFailedSearchUsers3wordsKey() {
        $this->dbMock->method('error')->willReturn(false);
        $user = new User(null, $this->dbMock);
        $data = [
            'action' => 'search',
            'complement' => 'users',
            'current_user_id' => 12,
            'another_user_id' => 25,
            'key' => 'Bill Duncan Johnson'
        ];
        $this->assertEquals(false, $user->searchUsers($data));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSearchUsersAllUsers2FavouriteFound() {
        $this->dbMock->method('error')->willReturn(false);
        $a[0] = new StdClass();
        $a[0]->user_id = 1;
        $a[0]->user_name = "Silvia";
        $a[0]->surname = "Johnson";
        $a[1] = new StdClass();
        $a[1]->user_id = 2;
        $a[1]->user_name = "Mike";
        $a[1]->surname = "Johnas";
        $this->dbMock->method('results')->willReturn($a);
        $user = new User(null, $this->dbMock);
        $data = [
            'action' => 'search',
            'complement' => 'users',
            'current_user_id' => 12,
            'another_user_id' => 0,
            'key' => 'Jo',
            'online' => false,
            'favourite' => true
        ];
        $this->assertEquals($a, $user->searchUsers($data));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testSearchUsersAllFavUsersEmptyKey3FavouriteFound() {
        $this->dbMock->method('error')->willReturn(false);
        $a[0] = new StdClass();
        $a[0]->user_id = 1;
        $a[0]->user_name = "Silvia";
        $a[0]->surname = "Johnson";
        $a[1] = new StdClass();
        $a[1]->user_id = 2;
        $a[1]->user_name = "Mike";
        $a[1]->surname = "Johnas";
        $this->dbMock->method('results')->willReturn($a);
        $user = new User(null, $this->dbMock);
        $data = [
            'action' => 'search',
            'complement' => 'users',
            'current_user_id' => 34,
            'another_user_id' => 78,
            'key' => '',
            'online' => false,
        ];
        $this->assertEquals($a, $user->searchUsers($data));
    }
}
