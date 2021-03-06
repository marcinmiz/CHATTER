<?php

require_once __DIR__.'/../../src/backend/model/Redirect.php';

use backend\model\Redirect;
use PHPUnit\Framework\TestCase;

class RedirectTest extends TestCase
{

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testDoRedirection()
    {
        Redirect::to("register.php");
        $this->assertContains(
            'Location: register.php', xdebug_get_headers()
        );
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testDoRedirectionPageNotFound()
    {
        Redirect::to(404);

        $this->assertContains(
            'Location: 404', xdebug_get_headers()
        );
    }

}
