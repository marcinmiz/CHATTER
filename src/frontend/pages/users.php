<?php

require_once '../../core/init.php';

$user = new \backend\model\User();

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CHATTER Main page</title>
    <meta name="description" content="Web chat App">
    <meta name="keywords" content="chat, web chat">
    <meta name="author" content="Marcin Mizgała">
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <link href="../../extras/fontello/css/fontello.css" rel="stylesheet" type="text/css"/>

    <link rel="stylesheet" href="../stylesheets/main.css">

    <link rel="stylesheet" href="../stylesheets/users.css">

</head>
<body>

    <div id="header"></div>
    <div id="content" class="container">Loading</div>

    <script src="../scripts/users.js"></script>

    <?php
    $current_user_id = $_SESSION['user'];
    echo <<< END
    <script>
        localStorage.setItem("sender_id", $current_user_id);

        async function getFile(fileName, element) {
            
            fetch(fileName, {
                headers: {
                    'Accept': 'text/html'}
            })
                .then(res => res.text())
                .then((data) => {
                    document.getElementById(element).innerHTML = data;
                    if (element === 'content')
                    {
                       go();
                    }
                })
                .catch((error) => console.log(error))
        }
        getFile("extended_mainbar.html", 'header');
        getFile("users_content.html", 'content');

    </script>
END;
    ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

</body>
</html>