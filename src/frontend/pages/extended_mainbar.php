<?php
require_once '../../core/init.php';
$user = new \backend\model\User();
?>

<div class="mainbar">
    <div class="avatar-container">
        <img src="../../extras/img/avatar.png" class="avatar" alt="avatar">
        <p></p>
    </div>
    <a class="mainbar_link brand d-block mx-auto" href="index.php">CHATTER</a>
    <a class="mainbar_link" href="logout.php">Log out</a>
</div>
