<?php

require_once __DIR__.'/../../src/backend/rest_handlers/SimpleRestHandler.php';

use backend\rest_handlers\SimpleRestHandler;
use PHPUnit\Framework\TestCase;

class SimpleRestHandlerTest extends TestCase
{

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
}
