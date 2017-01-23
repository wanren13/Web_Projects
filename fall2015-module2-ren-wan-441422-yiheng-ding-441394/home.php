<?php
if (empty($_SESSION)) {
    session_start();
    ob_clean();
}

if (!isset($_SESSION['username'])) { //if not yet logged in
    header("Location: login.php");// send to login page
    exit;
}
$username = $_SESSION['username'];

// $pwd = realpath(dirname(__FILE__));

if (isset($_FILES['uploadedfile']['name'])) {
    $filename = basename($_FILES['uploadedfile']['name']);

    // if(!preg_match("[^/?*:;{}\\]+",$filename)) {
    //         echo "Invalid filename 1";
    //         // exit;
    // } 
    // else { 

        $full_path = sprintf("../../users/%s/%s",$username,$filename);
    //    $full_path = 

        if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'],$full_path)){
        //      header("Location: upload_success.html");
        //      exit;
                echo "$filename is uploaded successfully.";
        }else{
        //      header("Location: upload_failure.html");
        //      exit;
                echo "Unable to upload $filename";
        }
//    }
}

if (isset($_POST['fileToDelete'])){
    #echo $_POST['fileToDelete'];
    unlink($_POST['fileToDelete']);
}

$userpath = "../../users/".$username;
$files = scandir($userpath);
unset($files[0]);
unset($files[1]);
$files = array_values($files);
?>
<!DOCTYPE html>
<html>
    <head>
        <link type="text/css" rel="stylesheet" href="css/home.css">
        <title><?php echo $_SESSION['username']."'s Home"; ?>
        </title>
    </head>
    <body>

    <header>
        <div class="wrapper" style="margin: 0 auto">
            <div class="toolbar">
            <?php
                echo "Hello, ".$_SESSION['username'];
            ?>
            <!-- <div style="height: 26px; width: 1px; border-right: #006092 1px solid; display: inline-block; float: left; margin-left: 10px; margin-top: 7px;"></div> -->
            <a id="logout" href="logout.php">Logout</a>         
            </div>
        </div>
    </header>

    <div class="wrapper">
    <form enctype="multipart/form-data" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
        <p>
                <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
                <label for="uploadfile_input">Choose a file to upload:</label>
                <input name="uploadedfile" type="file" id="uploadfile_input"/>
                <input type="submit" value="Upload" />
        </p>
    </form>
        
            <?php
            if (!empty($files)) {
                ?>
                <div class="block">
                <h3>File List</h3>
                <ul>            
                <?php
                foreach ($files as $fname) {?>
                        <li>
                            <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
                                <a href="viewfile.php?filename=<?php echo urlencode($fname); ?>"><?php echo $fname; ?></a>
                                <input type="hidden" name="fileToDelete" value="<?php echo "../../users/".$_SESSION['username']."/".$fname; ?>" >
                                <input type="submit" value="Delete" style="float:right; margin-right:10px;"/>
                                <?php #echo getcwd()."/users/".$_SESSION['username']."/".$fname; ?>

                            </form>
                        </li>
                <?php 
                }
            }
            else {
            ?>
                <li><?php echo "No file found"; ?></li>
            <?php
            }
            ?>
            </ul>
            </div>
        </div>
    </body>
</html>