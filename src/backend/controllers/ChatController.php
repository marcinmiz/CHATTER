<?php

require_once '../../core/init.php';
require_once "../rest_handlers/ChatRestHandler.php";

use backend\rest_handlers\ChatRestHandler;

    $data = file_get_contents("php://input");

    $data = json_decode($data, true);

    $action = $data['action'];
    $complement = $data['complement'];

/*
controls the RESTful services
URL mapping
*/

switch($action){

    case "send":
        switch($complement){
            case "message":
                $chatRestHandler = new ChatRestHandler();
                $chatRestHandler->sendMessage($data);
                break;
            case "" :
                //404 - not found;
                echo json_encode("Not found " + $action + " " + $complement);
                break;
        }
        break;
    case "" :
        //404 - not found;
        echo json_encode("Not found $action");
        break;
}