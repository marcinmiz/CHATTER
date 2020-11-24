<?php

require_once '../../core/init.php';
require_once "../rest_handlers/UserRestHandler.php";

use backend\rest_handlers\UserRestHandler;

$action = "";
if(isset($_GET["action"]))
    $action = $_GET["action"];

$complement = "";
if(isset($_GET["complement"]))
    $complement = $_GET["complement"];

$user_id = 0;
if(isset($_GET["user_id"]))
    $user_id = $_GET["user_id"];
/*
controls the RESTful services
URL mapping
*/

switch($action){

    case "get":
        switch($complement){
            case "all_users":
                $userRestHandler = new UserRestHandler();
                $userRestHandler->getAllUsers($user_id);
                break;
            case "statuses":
                $userRestHandler = new UserRestHandler();
                $userRestHandler->getStatuses($user_id);
                break;
            case "user":
                $userRestHandler = new UserRestHandler();
                $userRestHandler->getUser($user_id);
                break;
            case "" :
                //404 - not found;
                break;
        }
        break;
    case "update":
        switch($complement) {
            case "last_activity":
                $userRestHandler = new UserRestHandler();
                $userRestHandler->updateLastActivity($user_id);
                break;
            case "" :
                //404 - not found;
                break;
        }
        break;
    case "" :
        //404 - not found;
        break;
}