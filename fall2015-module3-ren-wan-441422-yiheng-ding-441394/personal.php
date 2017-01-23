<?php
if(empty($_SESSION))
{
	session_start();
}
include 'functions.php';
generateToken();
checkToken();


if(isset($_POST['delete'])){
    checkToken();
	if(isset($_POST['storyID'])){
		deleteStory($_POST['storyID']);
	}
	elseif(isset($_POST['commentID'])){
		deleteComment($_POST['commentID']);
	}
}
elseif (isset($_POST['finishEditC'])) {
	$newContent = $_POST['comment'];
	$commentID = $_POST['commentID'];
	updateComment($commentID,$newContent);
}
elseif (isset($_POST['finishEditS'])) {
	$newContent = $_POST['story'];
	$storyID = $_POST['storyID'];
	updateStory($storyID,$newContent);
}

#necessary to unset the variable,because it's confusing
if(isset($_SESSION['storyToComment'])){
	unset($_SESSION['storyToComment']);
}

$username = $_SESSION['username'];
$ownStories = getStoriesByUserName($username);
$ownComments = getCommentsByUsername($username);
?>

<!DOCTYPE html>
<html>
    <head>
        <link type="text/css" rel="stylesheet" href="css/home.css">
        <title>Look at me</title>
    </head>

    <body>
    	<header>
    		<div class="wrapper" style="margin: 0 auto">
            	<div class="toolbar">
            		<div class="left"><a href="index.php">HOME PAGE</a></div>
            		<div class="right"><a href="logout.php">Logout</a></div>
                    <div class="right"><a href="editProfile.php">Update Picture</a></div>
                    <div class="right"><a href="updatePassword.php">Change Password</a></div>
            	</div>
        	</div>
    	</header>
		<div class="wrapper">
        	<div class="block">
        		<a href="editing.php"><h3 style="float:right">Start Writing New Stories</h3></a>
                <h3>Your Stories</h3>
            	<ul>
            	<?php 
            	foreach ($ownStories as $key => $value) {?>
            		<li>
                        <a href="story.php?var=<?php echo $value[0];?>">
                            <h2>
                            <?php echo $value[1];?>
                            </h2>
                        </a>
            			<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
            				<!-- <a href="story.php?var=<?php echo $value[0];?>"> 
            				<h2> -->
            					<?php #echo $value[1];?>
            					<input type="hidden" name="storyID" value="<?php echo $value[0];?>">
            					<input type="submit" name="delete" value="Delete" style="float:right; margin-right:10px;">
            					<input type="submit" name="edit" value="Edit" style="float:right; margin-right:10px;">
                                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
            				<!-- </h2>
            				</a> -->
            				<h5>
            					<?php 
            					if (isset($_POST['edit']) && isset($_POST['storyID'])){
                                    if ($_POST['storyID']==$value[0]) {
                                        printf('<form action="');
                                        echo htmlentities($_SERVER['PHP_SELF']);
                                        printf('" method="POST"><textarea name="story" rows="4" cols="50">');
                                        echo $value[2];
                                        printf('</textarea><input type="hidden" name="storyID" value="');
                                        echo $value[0];
                                        printf('"><input type="hidden" name="token" value="');
                                        echo $_SESSION["token"];
                                        printf('" /><input type="submit" name="finishEditS" value="submit"></form>');
                                    }
                                    else{
                                        echo $value[2];
                                    }
            					}
            					else{
            						echo $value[2];
            					}
            					?>
            				</h5>
            				<p class="timeandname"><?php echo $value[5];?></p>
            			</form>
            		</li>
            	<?php }	?>
            	</ul>
            	<h3>Your Comments</h3>
            	<ul>
            	<?php 
            	foreach ($ownComments as $key => $value) {
            		$title = getStory($value[2]);?>
            		<li>
                        <a href="story.php?var=<?php echo $value[2];?>">
                            <h2>
                                <?php echo $title[0];?>
                            </h2>
                        </a>
            			<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
            				<!--<a href="story.php?var=<?php echo $value[2];?>">
            				<h2>-->
            					<?php #echo $title[0];?>
            					<input type="hidden" name="commentID" value="<?php echo $value[3];?>">
            					<input type="submit" name="delete" value="Delete" style="float:right; margin-right:10px;">
            					<input type="submit" name="edit" value="Edit" style="float:right; margin-right:10px;">
                                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
            				<!--</h2>
            				</a>-->
            				<h5>
            					<?php 
            					if (isset($_POST['edit']) && isset($_POST['commentID'])){
                                    if ($_POST['commentID']==$value[3]) {
                                        printf('<form action="');
                                        echo htmlentities($_SERVER['PHP_SELF']);
                                        printf('" method="POST"><textarea name="comment" rows="4" cols="50">');
                                        echo $value[0];
                                        printf('</textarea><input type="hidden" name="commentID" value="');
                                        echo $value[3];
                                        printf('"><input type="hidden" name="token" value="');
                                        echo $_SESSION["token"];
                                        printf('" /><input type="submit" name="finishEditC" value="submit"></form>');
                                    }
                                    else{
                                        echo $value[0];
                                    }
            					}
            					else{
            						echo $value[0];
            					}
            					?>
            				</h5>
            				<p class="timeandname"><?php echo $value[1];?></p>
            			</form>
            		</li>
            	<?php }	?>
            	</ul>
            </div>
        </div>
    </body>
</html>