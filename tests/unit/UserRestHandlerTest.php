<?php

require_once __DIR__.'/../../src/backend/rest_handlers/UserRestHandler.php';

use backend\rest_handlers\UserRestHandler;
use PHPUnit\Framework\TestCase;

class UserRestHandlerTest extends TestCase
{

    public function testSuccessfulHtmlEncoding()
    {
        $responseData = array(
            'user_id' => 1,
            'user_name' => 'David',
            'surname' => 'Davidson'
        );

        $rest_handler = new UserRestHandler();

        $this->assertEquals("<table border='1'><tr><td>user_id</td><td>1</td></tr><tr><td>user_name</td><td>David</td></tr><tr><td>surname</td><td>Davidson</td></tr></table>", $rest_handler->encodeHtml($responseData));
    }
}
