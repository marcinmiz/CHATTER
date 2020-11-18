<?php
require_once '../../core/init.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CHATTER register</title>
    <meta name="description" content="Web chat App">
    <meta name="keywords" content="chat, web chat">
    <meta name="author" content="Marcin Mizgała">
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&amp;subset=latin-ext" rel="stylesheet">

    <link rel="stylesheet" href="../stylesheets/main.css">

    <link rel="stylesheet" href="../stylesheets/login.css">

    <!--[if lt IE 9]-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <!--[endif]-->

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

                <h3>Register</h3>

                <form action="" method="post">

                    <div class="field">

                        <input class="initial col-sm-10 col-md-6 col-lg-4 col-xl-3" type="text" name="user_name" id="user_name" placeholder="Name" value="<?php echo escape(\backend\model\Input::get('user_name'));?>" autocomplete="off">

                    </div>

                    <div class="field">

                        <input class="initial col-sm-10 col-md-6 col-lg-4 col-xl-3" type="text" name="surname" id="surname" placeholder="Surname" value="<?php echo escape(\backend\model\Input::get('surname'));?>" autocomplete="off">

                    </div>

                    <div class="field">

                        <input class="initial col-sm-10 col-md-6 col-lg-4 col-xl-3" type="email" name="email" id="email" placeholder="E-mail" value="<?php echo escape(\backend\model\Input::get('email'));?>" autocomplete="off">

                    </div>

                    <div class="field">

                        <input class="initial col-sm-10 col-md-6 col-lg-4 col-xl-3" type="password" name="password" id="password" placeholder="Choose a password">

                    </div>

                    <div class="field">

                        <input class="initial col-sm-10 col-md-6 col-lg-4 col-xl-3" type="password" name="password_again" id="password_again" placeholder="Enter your password again">

                    </div>
                    <?php
                    if (\backend\model\Input::exists()) {
                        if (\backend\model\Token::check(\backend\model\Input::get('token')))
                        {
                            $validate = new \backend\model\Validate();
                            $validation = $validate->check($_POST, array(
                                'user_name' => array(
                                    'required' => true,
                                    'min' => 2,
                                    'max' => 40,
                                ),
                                'surname' => array(
                                    'required' => true,
                                    'min' => 2,
                                    'max' => 40
                                ),
                                'email' => array(
                                    'required' => true,
                                    'min' => 2,
                                    'max' => 40,
                                    'unique' => 'users'
                                ),
                                'password' => array(
                                    'required' => true,
                                    'min' => 6
                                ),
                                'password_again' => array(
                                    'required' => true,
                                    'matches' => 'password'
                                )
                            ));

                            if ($validation->passed())
                            {
echo <<< END
<script>
    document.getElementById('notification').innerText = "Sending ...";
</script>
END;

                                $user = new \backend\model\User();

                                try{
                                    $user->create(array(
                                        'user_name' => \backend\model\Input::get('user_name'),
                                        'surname' => \backend\model\Input::get('surname'),
                                        'email' => \backend\model\Input::get('email'),
                                        'password' => \backend\model\Hash::make(\backend\model\Input::get('password')),
                                        'activation_token' => bin2hex(random_bytes(4)),
                                        'account_active' => false,
                                        'joined' => date('Y-m-d H:i:s'),
                                        'last_activity' => date('Y-m-d H:i:s'),
                                    ));
                                    $email = \backend\model\Input::get('email');
                                    $user = new \backend\model\User($email);
                                    \backend\model\Session::put('new_user_email', $email);

                                    if($user->send_mail($user->data()->email, $user->data()->activation_token)) {
                                        echo <<< END
<script>
    document.getElementById('notification').innerText = "Email successfully sent to $email...";
</script>
END;
                                        \backend\model\Session::flash('registration', 'You have been registered but an account has to be activated!');
                                        \backend\model\Redirect::to('activate.php');

                                    } else {
                                        echo <<< END
<script>
    document.getElementById('notification').innerText = "Email sending failed...";
</script>
END;
                                    }
                                } catch (Exception $e) {
                                    die($e->getMessage());
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
                    ?>
                    <input type="hidden" name="token" value="<?php echo \backend\model\Token::generate(); ?>">

                    <div>

                        <input class="my-2 mx-auto login_button col-sm-10 col-md-6 col-lg-4 col-xl-3" type="submit" value="Register">

                    </div>

                </form>

                <div>

                    <button class="my-2 mx-auto register_button col-sm-10 col-md-6 col-lg-4 col-xl-3" onclick="location='index.php';">
                        Back to Login
                    </button>

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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

</body>
</html>