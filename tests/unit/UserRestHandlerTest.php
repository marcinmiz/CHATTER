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

    public function testSuccessfulJSONEncoding()
    {
        $responseData = array(
            'user_id' => 2,
            'user_name' => 'Anne',
            'surname' => 'Delaware'
        );

        $jsonResponse = json_encode($responseData);

        $rest_handler = new UserRestHandler();

        $this->assertEquals($jsonResponse, $rest_handler->encodeJson($responseData));
    }

    public function testSuccessfulXMLEncoding()
    {
        $responseData = array(
            'user_id' => 3,
            'user_name' => 'Robert',
            'surname' => 'Lawrence'
        );
//Set Line separator: LF
        $jsonResponse = '<?xml version="1.0"?>
<mobile><user_id>3</user_id><user_name>Robert</user_name><surname>Lawrence</surname></mobile>
';

        $rest_handler = new UserRestHandler();

        $this->assertEquals($jsonResponse, $rest_handler->encodeXml($responseData));
    }
}
