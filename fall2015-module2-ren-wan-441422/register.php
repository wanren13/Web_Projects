<?php
if(empty($_SESSION))
{
    session_start();
    #printf("%s/user.txt\n",getcwd());
}

require_once('loadusers.php');

?>
<?php
    if (isset($_POST['register'])){
        if ($_POST['username'] != ''){
            if($_POST['password'] != ''&& $_POST['password2'] != ''){
                    // printf("<p><strong>%s</strong></p>\n",htmlentities($_POST['username']));
                    // printf("<p><strong>%s</strong></p>\n",htmlentities($_POST['password']));
                    // printf("<p><strong>%s</strong></p>\n",htmlentities($_POST['password2'])); 

                $userlist = loadusers();

                // $pattern = '/^[a-zA-Z0-9_-]+$/';
                $pattern = '/^[\w_\-]+$/';

                if (!preg_match($pattern, $_POST['username'])) {
                    $_SESSION['error'] = "Invalid username";

                 //   printf("<p><strong>Invalid username</strong></p>");
                }
                else{
                    if (array_key_exists($_POST['username'], $userlist)){
                        $_SESSION['error'] = "Username exists";
                    //    printf("<p><strong>username exists</strong></p>");
                    }
                    else{
                        if($_POST['password'] == $_POST['password2']){
                            #$filepath = getcwd();
                            $w = fopen("users.txt","a+");
                            $username = htmlspecialchars($_POST['username']);
                            $password = htmlspecialchars($_POST['password']);
                            $newUser = "\n$username $password";
                            fwrite($w,$newUser);
                            fclose($w);

                            // printf("<p><strong>register successes</strong></p>");

                            $newDir = "users/$username";
                            mkdir($newDir);

                            $_SESSION['username'] = $username;
                            $_SESSION['password'] = $password;
                            header("Location: home.php");
                        }
                        else{
                            $_SESSION['error'] = "Passwords didn't match";
                         //   printf("<p><strong>passwords didn't match</p></strong>");
                        }
                    }
                }
            }
            else{
                $_SESSION['error'] = "Please input password";
                //printf("<p><strong>please re-input password</strong></p>\n");
            }        
        }
        else{
            $_SESSION['error'] = "Please input username";
            // printf("<p><strong>please input username</strong></p>");
        }
        unset($_POST['username']);
        unset($_POST['password']);
        unset($_POST['password2']);
    }
?>
<!DOCTYPE html>
<html>
    <head><link type="text/css" rel="stylesheet" href="css/register.css"></head>
    <title>Register</title>
    <body id="dummybodyid">
        <div class="reg-box">
            <div class="reg-box-header">Register</div>
            <div class="reg-box-error"><?php
                if (isset($_SESSION['error'])) {
                //    echo "Error set "; 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
//                    unset($_POST['register']);
                }
                // else
                    // echo "Error unset";
        ?></div>
            <div class="reg-box-body">
                <form name="LoginForm" method="post" action="register.php">
                    <input type="text" id="username" name="username" placeholder="User Name" /><br />
                    <input type="password" id="password" name="password" placeholder="Password" /><br />
                    <input type="password" id="password2" name="password2" placeholder="Confirm Password" /><br />                    
                    <input type="submit" name="register" value="Register" class="reg-btn"/>
                </form>
            </div>
        </div>        
    </body>
</html>
