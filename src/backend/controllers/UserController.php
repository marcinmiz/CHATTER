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

$another_user_id = 0;
if(isset($_GET["another_user_id"]))
    $another_user_id = $_GET["another_user_id"];

$popular_user_id = 0;
if(isset($_GET["popular_user_id"]))
    $popular_user_id = $_GET["popular_user_id"];

$fav = 0;
if(isset($_GET["fav"]))
    $fav = $_GET["fav"];

$icon = 0;
if(isset($_GET["icon"]))
    $icon = $_GET["icon"];

$data = file_get_contents("php://input");

$data = json_decode($data, true);

if(isset($data['action']))
    $action = $data["action"];

if(isset($data['complement']))
    $complement = $data["complement"];

/*
controls the RESTful services
URL mapping
*/

switch($action){

    case "get":
        switch($complement){
            case "all_users":
                $userRestHandler = new UserRestHandler();
                $userRestHandler->getAllUsers($user_id, $another_user_id, $fav);
                break;
            case "statuses":
                $userRestHandler = new UserRestHandler();
                $userRestHandler->getStatuses($data);
                break;
            case "user":
                $userRestHandler = new UserRestHandler();
                $userRestHandler->getUser($user_id);
                break;
            case "" :
                //404 - not found;
                echo json_encode('not found' + $action + " " + $complement);
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
                echo json_encode('not found' + $action + " " + $complement);
                break;
        }
        break;
    case "mark":
        switch($complement) {
            case "favourite_user":
                $userRestHandler = new UserRestHandler();
                $userRestHandler->markUserAsFavourite($user_id, $popular_user_id, $icon);
                break;
            case "" :
                //404 - not found;
                echo json_encode('not found' + $action + " " + $complement);
                break;
        }
        break;
    case "search":
        switch($complement) {
            case "users":
                $userRestHandler = new UserRestHandler();
                $userRestHandler->searchUsers($data);
                break;
            case "" :
                //404 - not found;
                echo json_encode('not found' + $action + " " + $complement);
                break;
        }
        break;
    case "" :
        //404 - not found;
        echo json_encode('not found' + $action);
        break;
}