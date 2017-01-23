<?php
if (isset($_POST['register'])){
    header("Location: register.php");
    exit;
}

if(empty($_SESSION))
{
    session_start();
}

require_once('loadusers.php');

$userlist = loadusers();


if (isset($_POST['username']) && isset($_POST['password'])) {
    if ($_POST['username'] == "" || $_POST['password'] == "" ){
        $_SESSION['status'] = "Please input username and password";
    }
    else{
        if (!preg_match('/^[\w_\-]+$/', $_POST['username'])) {
            $_SESSION['status'] = "Invalid username or password";
        }
        else {
            $username = htmlspecialchars($_POST['username']);
            $password = htmlspecialchars($_POST['password']);

            if (array_key_exists($username, $userlist)) {
                if ($userlist[$username] == $password) {
                    $_SESSION['username'] = $username;
                    // $_SESSION['status'] = "logged in";   
                    header("refresh:0;url=home.php");
                    exit;
                }    
                else{
                    $_SESSION['status'] = "Invalid username or password";
                }
            }
            else {
                $_SESSION['status'] = "Invalid username or password";
            }
        }
    }    
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link type="text/css" rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="login-box">
        <div class="login-box-header">Sign in</div>
        <div class="login-error"><?php
        if (isset($_SESSION['submit'])){
            if (isset($_SESSION['status'])) {
                echo $_SESSION['status'];
            }
        }
        ?></div>
        <div id="login-panel" class="login-box-body">
            <p style="color: red; font-size: 14px; ">
                <?php


            // echo "<p>$username</p>";
            // echo "<p>$password</p>";
            // var_dump($userlist);

            //echo "test"; //debug
            //echo $_SESSION['status']."<br />"; //debug

                ?>
            </p>
            <form method="post" action="login.php">
                <div class="login-field">
                    <input id = "username" name = "username" type = "text" placeholder="Username" value = "<?php
                    if (isset($_POST['username']))
                        echo $_POST['username'];
                    ?>"/>
                </div>
                <div class="login-field">
                    <input id="password" name="password" type="password" placeholder="Password" />
                </div>
                <div class="login-field" style="padding: 0">
                    <input type="submit" class="login-btn" name="register" value="Register" style="display: block; float: right;" />
                    <input type="submit" class="login-btn" name="submit" value="Sign In" style="display: block; float: right;" />
                </div>
            </form>
        </div>
    </div>
</body>
</html>