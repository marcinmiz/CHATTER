<?php

namespace backend\rest_handlers;


use backend\model\Chat;
use SimpleXMLElement;

class ChatRestHandler extends SimpleRestHandler
{
    private $chat;

    public function __construct($chat = null)
    {
        ($chat == null) ? $this->chat = new Chat() : $this->chat = $chat;
    }

    public function encodeHtml($responseData) {

        $htmlResponse = "<table border='1'>";
        foreach($responseData as $key=>$value) {
            $htmlResponse .= "<tr><td>". $key. "</td><td>". $value. "</td></tr>";
        }
        $htmlResponse .= "</table>";
        return $htmlResponse;
    }

    public function encodeJson($responseData) {
        $jsonResponse = json_encode($responseData);
        return $jsonResponse;
    }

}