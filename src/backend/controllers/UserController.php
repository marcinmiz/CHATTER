<?php

require_once '../../core/init.php';
require_once "../rest_handlers/UserRestHandler.php";

use backend\rest_handlers\UserRestHandler;

$action = "";
if(isset($_GET["action"]))
    $view = $_GET["action"];

$complement = "";
if(isset($_GET["$complement"]))
    $view = $_GET["$complement"];
/*
controls the RESTful services
URL mapping
*/
switch($action){

    case "get":
        switch($complement){
            case "all_users":
                $userRestHandler = new UserRestHandler();
                $userRestHandler->getAllUsers();
                break;
            case "statuses":
                $userRestHandler = new UserRestHandler();
                $userRestHandler->getStatuses();
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
                $userRestHandler->updateLastActivity();
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