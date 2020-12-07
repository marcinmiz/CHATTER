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

    function updateLastActivity($user_id)
    {
        $rawData = $this->user->updateLastActivity($user_id);

        if (!$rawData) {
            $statusCode = 404;
            $rawData = array('error' => 'Last activity not updated!');
        } else {
            $statusCode = 200;
            $rawData = array('success' => 'Last activity updated!');
        }

        $this->selectEncoding($statusCode, $rawData);
        return $statusCode;
    }

    function getStatuses($user_id) {

        $rawData = $this->user->getStatuses($user_id);

        if($rawData == false) {
            $statusCode = 404;
            $rawData = array('error' => 'No statuses found!');
        } else {
            $statusCode = 200;
        }

        $this->selectEncoding($statusCode, $rawData);
        return $statusCode;
    }

    function getAllUsers($user_id)
    {
        $rawData = $this->user->findAll($user_id);

        if ($rawData == false) {
            $statusCode = 404;
            $rawData = array('error' => 'No users found!');
        } else {
            $statusCode = 200;
        }

        $this->selectEncoding($statusCode, $rawData);
        return $statusCode;
    }

    function getUser($user_id)
    {
        $rawData = $this->user->find($user_id);

        if ($rawData == false) {
            $statusCode = 404;
            $rawData = array('error' => 'No users found!');
        } else {
            $statusCode = 200;
            $data = $this->user->data();
            $user_data = [
              'user_id' => $data->user_id,
              'user_name' => $data->user_name,
              'surname' => $data->surname,
              'email' => $data->email
            ];
            $rawData = $user_data;
        }

        $this->selectEncoding($statusCode, $rawData);
        return $statusCode;
    }

    function markUserAsFavourite($liker_user_id, $popular_user_id)
    {
        $rawData = $this->user->markUserAsFavourite($liker_user_id, $popular_user_id);

        if ($rawData == false) {
            $statusCode = 404;
            $rawData = array('error' => 'User has not been added to favourite users!');
        } else {
            $statusCode = 200;
            $rawData = array('success' => 'User has been added to favourite users!');
        }

        $this->selectEncoding($statusCode, $rawData);
        return $statusCode;
    }

    function getAllFavouriteUsers($user_id)
    {
        $rawData = $this->user->getAllFavouriteUsers($user_id);

        if ($rawData == false) {
            $statusCode = 404;
            $rawData = array('error' => 'Error occurred during adding to favourite users!');
        } else {
            $statusCode = 200;
        }

        $this->selectEncoding($statusCode, $rawData);
        return $statusCode;
    }
}