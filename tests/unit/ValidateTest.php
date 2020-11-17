<?php

require_once __DIR__.'/../../src/utilities/sanitize.php';
require_once __DIR__.'/../../src/backend/model/Validate.php';

use backend\model\Validate;
use PHPUnit\Framework\TestCase;

class ValidateTest extends TestCase
{
    private $_db = null,
        $_validate;

    protected function setUp(): void
    {
        $this->_db = $this->getMockBuilder('backend\model\Redirect')
            ->addMethods(['get'])
            ->addMethods(['count'])
            ->getMock();
        $this->_db->method('get')->willReturn($this->_db);

        $this->_validate = new Validate($this->_db);
    }

    public function testPassWithoutRules()
    {
        $items = array();
        $this->_validate->check($_POST, $items);
        $this->assertTrue($this->_validate->passed());
    }

    public function testFailWithRequireRule()
    {
        $items = array(
            'email' => array(
                'required' => true,
            )
        );
        $_POST['email'] = '';
        $this->_validate->check($_POST, $items);
        $this->assertContains("email is required", $this->_validate->errors());
        unset($_POST['email']);
    }

    public function testFailWithMinRule()
    {
        $items = array(
            'email' => array(
                'min' => 10,
            )
        );
        $_POST['email'] = 'qw@wp.pl';
        $this->_validate->check($_POST, $items);
        $this->assertContains("email must be a minimum of 10 characters.", $this->_validate->errors());
        unset($_POST['email']);
    }

}
