<?php 
if(empty($_SESSION))
{
	session_start();
}
include 'functions.php';
generateToken();
checkToken();

$username = htmlspecialchars($_SESSION['username']);


if (isset($_POST['change'])){
    if ($_POST['password0'] != '' && $_POST['password1'] != ''&& $_POST['password2'] != '') {
        $saltedPwd = getSaltedPwdByName($username);
        $password = crypt($_POST['password0'], $saltedPwd);

        if(login($username,$password)){

            if($_POST['password1'] == $_POST['password2']){

                $password_tmp = htmlspecialchars($_POST['password1']);
                $password = crypt($password_tmp,"");
                updatePassword(getUserId($username), $password);
                header("Location: personal.php");
            }
            else{
                $_SESSION['error'] = "Password unmatched";

            }            
        }
        else {
            $_SESSION['error'] = "Wrong password";
        }
    }
    else{
        $_SESSION['error'] = "Please input passwords";
    }
    unset($_POST['password0']);
    unset($_POST['password1']);
    unset($_POST['password2']);
}

?>

<!DOCTYPE html>
<html>
    <head><link type="text/css" rel="stylesheet" href="css/register.css">
    <title>Change Your Password</title>
    </head>
    <body id="dummybodyid">
        <div class="reg-box">
            <div class="reg-box-header">Change Password</div>
            <div class="reg-box-error"><?php
                if (isset($_SESSION['error'])) {
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                }
        ?></div>
            <div class="reg-box-body">
                <form name="LoginForm" method="POST" action="updatePassword.php">
                    <input type="password" id="password0" name="password0" placeholder="Old password" /><br />
                    <input type="password" id="password1" name="password1" placeholder="New Password" /><br />
                    <input type="password" id="password2" name="password2" placeholder="Confirm New Password" /><br />                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                    <input type="submit" name="change" value="Change" class="reg-btn"/>
                </form>
            </div>
        </div>        
    </body>
</html>