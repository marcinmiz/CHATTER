<?php

require_once '../../core/init.php';

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CHATTER activate</title>
    <meta name="description" content="Web chat App">
    <meta name="keywords" content="chat, web chat">
    <meta name="author" content="Marcin Mizgała">
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <link rel="stylesheet" href="../stylesheets/main.css">

    <link rel="stylesheet" href="../stylesheets/login.css">

    <style>
        #notification
        {
            text-align: center;
            margin-bottom: 25px;
        }
    </style>

</head>
<body>

<header id="mainbar"></header>

<main>

    <article>

        <div class="container">

            <div class="form_container">

                <div id="notification"></div>

                <div class="card bg bg-dark mx-auto my-1 col-sm-10 col-md-6">

                    <div class="card-header">
                        Activate your Account
                    </div>

                    <div class="card-block p-4">

                        <form action="" method="post">

                            <input class="d-block initial mx-auto mt-1 mb-3 col-sm-12" type="text" id="activation_code" name="activation_code" placeholder="Enter activation code"  autocomplete="off">

                            <input type="submit" class="d-block login_button mx-auto my-3 col-sm-12" value="Activate">
<?php

if (\backend\model\Session::exists('email_sent')) {
    $email_sent_message = \backend\model\Session::flash('email_sent');
    echo <<< END
<script>
    document.getElementById('notification').append("$email_sent_message");
    var br = document.createElement("br");
    document.getElementById('notification').appendChild(br);
</script>
END;
}

if (\backend\model\Session::exists('registration')) {
                                $registration_message = \backend\model\Session::flash('registration');
                                echo <<< END
<script>
    document.getElementById('notification').append("$registration_message");
    var br = document.createElement("br");
    document.getElementById('notification').appendChild(br);
</script>
END;
                            }

if (\backend\model\Input::exists('post')) {
    if (\backend\model\Token::check(\backend\model\Input::get('token'))) {
        $user = new \backend\model\User(\backend\model\Session::get('new_user_email'));
        $user->activate(\backend\model\Input::get('activation_code'));
    }
}

?>
                            <input type="hidden" name="token" value="<?php echo \backend\model\Token::generate(); ?>">

                        </form>

                    </div>

                    <div class="card-footer text-muted text-left">
                        <p>We have sent you a mail, kindly activate your account</p>
                        <p>You can copy and paste the code in above field.</p>
                    </div>

                </div>

            </div>

        </div>

    </article>

</main>

<script>

    async function getFile() {
        let basicMainbarPromise = new Promise(function(basicMainbarResolve, basicMainbarReject) {
            let req = new XMLHttpRequest();
            req.open('GET', "basic_mainbar.html");
            req.onload = function() {
                if (req.status == 200) {basicMainbarResolve(req.response);}
                else {basicMainbarReject("File not Found");}
            };
            req.send();
        });
        document.getElementById('mainbar').innerHTML  = await basicMainbarPromise;
    }
    getFile();

</script>

</body>
</html>
