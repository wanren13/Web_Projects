<?php
if(empty($_SESSION))
{
	session_start();
}
include 'functions.php';
generateToken();
checkToken();

if(isset($_POST['submit'])){
    #whatever a comment or story, it has content
	if (isset($_POST['content'])) {
		$content = $_POST['content'];
	}
    #here start to writing a new story
	if (isset($_POST['storyTitle'])) {
		$storyTitle = $_POST['storyTitle'];
		$user_id = getUserId($_SESSION['username']);
		addStory($storyTitle,$content,"temp",$user_id,date("Y-m-d h:i:s"));
        $currentId = getMaxStoryId();
        $link = "story.php?var=".$currentId;
        $add =  $_SERVER['HTTP_REFERER'];
        $pos =  strrpos($add,'/');
        $subadd = substr($add, 0, $pos+1);
        $final = $subadd.$link;
        updateStoryLink($currentId,$final);
		header("Location: personal.php");
	}
    #here start to writing a new story comment
	elseif (isset($_POST['storyToComment'])) {
		$storyId = $_POST['storyToComment'];
		$user_id = getUserId($_SESSION['username']);
		addComment($content,$user_id,date("Y-m-d h:i:s"),$storyId);
		header("Location: story.php?var=".$storyId);
	}
	
}
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
            		<div class="left">
            			<a href="<?php if(isset($_SESSION['username'])){echo "personal.php";}else{echo "index.php";}?>">Hello 
            			<?php if(isset($_SESSION['username'])) {
            				echo $_SESSION['username'];}?>
            			</a>
            		</div>
            		<div class="right"><a href="<?php if(isset($_SESSION['username'])){ echo "logout.php";}else{echo "login.php";}?>"><?php if(isset($_SESSION['username'])){echo "Logout";}else{echo "Login";}?></a></div>
            	</div>
        	</div>
    	</header>
    	<div class="wrapper">
        	<div class="block">
                <a href="index.php"><h3>Back to main page</h3></a>
                <a href="personal.php"><h3>Back to personal homepage</h3></a>
                <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
                	<h2 style="margin-left: 50px;">
                		Story title: 
                		<?php 
                			if(!isset($_SESSION['storyToComment'])){
                				printf ('<input type="text" name="storyTitle">');
                			}else{
                				$storyResult = getStory($_SESSION['storyToComment']);
                				printf ('<input type="hidden" name="storyToComment" value=');
                				echo $_SESSION['storyToComment'];
                				unset($_SESSION['storyToComment']);
                				printf('>');
                				echo " ".$storyResult[0];
                			}
                		?>
                		<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                		<input style="float: right;margin-right: 150px;margin-top: 10px;" type="submit" name="submit" value="Finish">
                	</h2>
                	<h5 style="margin-left: 50px;">
                		<textarea class="newStory" rows="4" cols="50" name="content">Enter story here...</textarea>
                	</h5>

                </form>
            </div>
        </div>
    </body>
</html>

