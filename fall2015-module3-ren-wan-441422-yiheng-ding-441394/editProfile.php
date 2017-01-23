<?php 
if(empty($_SESSION))
{
	session_start();
}
include 'functions.php';
generateToken();
checkToken();

if (isset($_POST['Upload'])) {
    $file = $_FILES['image']['tmp_name'];
    if(!isset($file))
    {
        echo "Please select any image.";
    }
    else
    {
        $image = addslashes(file_get_contents($file));
        $image_name = addslashes($_FILES['image']['name']);
        $image_size = getimagesize($file);
        
        if($image_size == false)
        {
            echo "This is not an image.";
        }
        else
        {
            $user_id = getUserId($_SESSION['username']);
            updatePortait($user_id, $image, $image_name);
            header("Location: personal.php");
            //$lastID = mysql_insert_id();
            // echo "Image uploaded.<p/>Your Image is: <p/><img src=getImage.php?id=$lastID width=100 height=100>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head><link type="text/css" rel="stylesheet" href="css/register.css"></head>
    <title>Edit Profile</title>
    <body id="dummybodyid">
        <div class="reg-box">
            <div class="reg-box-header">Update Portrait</div>
            <div class="reg-box-error"><?php
                if (isset($_SESSION['error'])) {
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                }
        ?></div>
            <div class="reg-box-body">
<!--                 <form name="LoginForm" method="post" action="register.php">
                    <input type="text" id="username" name="username" placeholder="User Name" /><br />
                    <input type="password" id="password" name="password" placeholder="Password" /><br />
                    <input type="password" id="password2" name="password2" placeholder="Confirm Password" /><br />                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                    <input type="submit" name="register" value="Register" class="reg-btn"/>
                </form> -->

                <form enctype="multipart/form-data" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
                    File: <input type="file" id="username" name="image"/>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                    <input type="submit" name="Upload" value="Upload" class="reg-btn"/>
                    </form>
                </form>
            </div>
        </div>        
    </body>
</html>