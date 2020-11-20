<?php

namespace backend\rest_handlers;


use backend\model\User;
use SimpleXMLElement;

class UserRestHandler extends SimpleRestHandler
{
    private $user;

    public function __construct($user = null)
    {
        ($user == null) ? $this->user = new User() : $this->user = $user;
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

    public function encodeXml($responseData) {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><mobile></mobile>');
        foreach($responseData as $key=>$value) {
            $xml->addChild($key, $value);
        }
        return $xml->asXML();
    }

    public function selectEncoding($statusCode, $rawData) {
        $requestContentType = $_SERVER['HTTP_ACCEPT'];
        $this ->setHttpHeaders($requestContentType, $statusCode);

        if(strpos($requestContentType,'application/json') !== false){
            $response = $this->encodeJson($rawData);
            echo $response;
            return 'json';
        } else if(strpos($requestContentType,'text/html') !== false){
            $response = $this->encodeHtml($rawData);
            echo $response;
            return 'html';
        } else if(strpos($requestContentType,'application/xml') !== false){
            $response = $this->encodeXml($rawData);
            echo $response;
            return 'xml';
        }
    }

    function updateLastActivity()
    {
        $rawData = $this->user->updateLastActivity();

        if (!$rawData) {
            $statusCode = 404;
            $rawData = array('error' => 'Last activity not updated!');
        } else {
            $statusCode = 200;
        }

        $this->selectEncoding($statusCode, $rawData);
        return $statusCode;
    }
}