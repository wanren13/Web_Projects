<?php 
if(empty($_SESSION))
{
	session_start();
    generateToken();
}
include 'functions.php';

checkToken();

if (isset($_POST['register'])){
    if ($_POST['username'] != ''){
        if($_POST['password'] != ''&& $_POST['password2'] != ''){
        	if (!preg_match('/^[\w_\-]+$/', $_POST['username'])) {
                $_SESSION['error'] = "Invalid username";
            }
            else{
            	if($_POST['password'] == $_POST['password2']){
            		$username = htmlspecialchars($_POST['username']);
                    $password_tmp = htmlspecialchars($_POST['password']);
                    $password = crypt($password_tmp,"");

                    $result = addUser($username,$password);
                    if($result == 0){
                    	$_SESSION['error'] = "Query failed";
                    }
                    elseif ($result == -1) {
                    	$_SESSION['error'] = "User already exists";
                    }
                    else{
                    	$_SESSION['username'] = $username;
                    	$_SESSION['password'] = $password;
                    	header("Location: index.php");
                    }
            	}
            	else{
            		$_SESSION['error'] = "Passwords didn't match";
            	}
            }
        }
        else{
        	$_SESSION['error'] = "Please input password";
        }
    }
    else{
    	$_SESSION['error'] = "Please input username";
    }
    unset($_POST['username']);
    unset($_POST['password']);
    unset($_POST['password2']);
}

?>

<!DOCTYPE html>
<html>
    <head><link type="text/css" rel="stylesheet" href="css/register.css">
    <title>Register</title>
    </head>
    <body id="dummybodyid">
        <div class="reg-box">
            <div class="reg-box-header">Register</div>
            <div class="reg-box-error"><?php
                if (isset($_SESSION['error'])) {
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                }
        ?></div>
            <div class="reg-box-body">
                <form name="LoginForm" method="post" action="register.php">
                    <input type="text" id="username" name="username" placeholder="User Name" /><br />
                    <input type="password" id="password" name="password" placeholder="Password" /><br />
                    <input type="password" id="password2" name="password2" placeholder="Confirm Password" /><br />                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                    <input type="submit" name="register" value="Register" class="reg-btn"/>
                </form>
            </div>
        </div>        
    </body>
</html>