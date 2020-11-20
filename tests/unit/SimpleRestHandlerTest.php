<?php

require_once __DIR__.'/../../src/backend/rest_handlers/SimpleRestHandler.php';

use backend\rest_handlers\SimpleRestHandler;
use PHPUnit\Framework\TestCase;

class SimpleRestHandlerTest extends TestCase
{
    private $httpVersion = "HTTP/1.1";

    public function testGetExistingHttpStatusMessage()
    {
        $status = 200;
        $rest_handler = new SimpleRestHandler();
        $this->assertEquals('OK', $rest_handler->getHttpStatusMessage($status));
    }

    public function testGetNotExistingHttpStatusMessage()
    {
        $status = 308;
        $rest_handler = new SimpleRestHandler();
        $this->assertEquals('Internal Server Error', $rest_handler->getHttpStatusMessage($status));
    }

    /**
     * @test
     * @runInSeparateProcess
     * @requires extension xdebug
     **/
    public function testContentTypeJSONStatusCode200 () {
        $statusCode = 200;
        $contentType = 'text/html';
        $rest_handler = new SimpleRestHandler();
        $rest_handler->setHttpHeaders($contentType, $statusCode);
        $url = 'http://localhost/CHATTER/src/frontend/pages/index.php';
        $headers = get_headers($url);
        self::assertEquals('Content-Type: text/html; charset=UTF-8', $headers[10]);
        self::assertEquals('HTTP/1.1 200 OK', $headers[0]);
    }
}
