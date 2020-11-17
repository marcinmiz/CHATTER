<?php

require_once __DIR__.'/../../src/core/init.php';
require_once __DIR__.'/../../src/backend/model/Token.php';

use backend\model\Token;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{

    public function testTokenExistAndProper()
    {
        $_SESSION['token'] = 'h72feh2hjf3894hfw4ef4e';
        self::assertTrue(Token::check('h72feh2hjf3894hfw4ef4e'));
    }

    public function testTokenNotExist()
    {
        self::assertFalse(Token::check('h72feh2hwf3443t34gf4e'));
    }

    public function testTokenNotProper()
    {
        $_SESSION['token'] = '392ueux209eixm594i232xim';
        self::assertFalse(Token::check('h32d24fwgq3fgef4e'));
    }
}
