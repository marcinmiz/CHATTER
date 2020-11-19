<?php

require_once '../../core/init.php';

$user = new \backend\model\User();
$user->logout();

\backend\model\Redirect::to('index.php');

