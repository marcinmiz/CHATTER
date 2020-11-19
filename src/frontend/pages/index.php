<?php
    require_once '../../core/init.php'
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CHATTER login</title>
    <meta name="description" content="Web chat App">
    <meta name="keywords" content="chat, web chat">
    <meta name="author" content="Marcin Mizgała">
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&amp;subset=latin-ext" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <link rel="stylesheet" href="../stylesheets/main.css">

    <link rel="stylesheet" href="../stylesheets/login.css">

    <!--[if lt IE 9]-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <!--[endif]-->

    <style>
        #notification {
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

                <h3>Log in</h3>
                <form action="" method="post">

                    <input class="d-block mx-auto my-2 initial col-sm-10 col-md-6 col-lg-4 col-xl-3" type="email"
                           placeholder="E-mail address" name="email"
                           value="<?php echo escape(\backend\model\Input::get('email')); ?>" autocomplete="off">

                    <input class="d-block mx-auto my-2 initial col-sm-10 col-md-6 col-lg-4 col-xl-3" type="password"
                           placeholder="password" name="password" autocomplete="off">

                    <input type="submit" class="d-block mx-auto my-2 login_button col-sm-10 col-md-6 col-lg-4 col-xl-3"
                           value="Log in">

                    <label for="remember">

                        <input type="checkbox" name="remember" id="remember"> Remember me

                    </label>

<?php
    if (\backend\model\Input::exists())
    {
        if (\backend\model\Token::check(\backend\model\Input::get('token'))) {
            $validate = new \backend\model\Validate();
            $validation = $validate->check($_POST, array(
                'email' => array('required' => true),
                'password' => array('required' => true)
            ));

            if ($validation->passed()) {
                $user = new \backend\model\User();

                $remember = (\backend\model\Input::get('remember') === 'on') ? true : false;
                $login = $user->login(\backend\model\Input::get('email'), \backend\model\Input::get('password'), $remember);

                if ($login) {
                    \backend\model\Redirect::to('users.php');
                }

            } else {
                foreach ($validation->errors() as $error) {
echo <<< END
<script>
    document.getElementById('notification').append("$error");
    var br = document.createElement("br");
    document.getElementById('notification').appendChild(br);
</script>
END;
            }
        }
    }
}
if (\backend\model\Session::exists('activation')) {
    $activation_message = \backend\model\Session::flash('activation');
    echo <<< END
<script>
    document.getElementById('notification').append("$activation_message");
    var br = document.createElement("br");
    document.getElementById('notification').appendChild(br);
</script>
END;
}
?>
                    <input type="hidden" name="token" value="<?php echo \backend\model\Token::generate(); ?>">

                </form>

                <a class="prompt mt-2" href="#">Forgot password?</a>

                <p class="prompt mt-2">You don't have an account?</p>

                <button class="my-2 mx-auto register_button col-sm-10 col-md-6 col-lg-4 col-xl-3" onclick="location='register.php';">Register</button>

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
                if (req.status == 200) {
                    basicMainbarResolve(req.response);}
                else {basicMainbarReject("File not Found");}
            };
            req.send();
        });
        document.getElementById('mainbar').innerHTML  = await basicMainbarPromise;
    }
    getFile();

</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

</body>
</html>