<?php
function getProfile($user_id)
{   
	ob_start();

	//Attempt to Connect
	$b64Src = 'img/no_portrait_yet.png';

	$mysqli = connectDB();
	$query = "SELECT portrait, portrait_name FROM users where user_id = $user_id";
	// echo $query;
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->store_result();
	$result->bind_result($portrait, $portrait_name);	

	if($result->num_rows) {
		$result->fetch();
		$result->close();	

		// $image = $row['IMG'];
		// $filename = $row['IMG_NAME'];
		if(!empty($portrait_name)) {
			$tmp = explode('.', $portrait_name);
			$ext = end($tmp);	

			$mime = "image/".$ext;
			$b64Src = "data:".$mime.";base64," . base64_encode($portrait);
		}
	}

	return $b64Src;		
}



function generateToken(){
	if (!isset($_SESSION['token'])) {
		$_SESSION['token'] = substr(md5(rand()),0,10);
	}
}

function checkToken(){
	if (isset($_POST['token'])) {
		if ($_SESSION['token'] !== $_POST['token']) {
        	die("request forgery detected!");
    	}
	}
}

function connectDB(){
	$db_host="52.26.60.242";
	$db_user="wustl_inst";
	$db_psw="wustl_pass";
	$db_name="news";
	$mysqli = new mysqli($db_host, $db_user, $db_psw, $db_name);
	if ($mysqli->connect_error) {
		echo "Connection Error: ".$mysqli->connect_error;
		exit;
	}
	return $mysqli;
}



// Functions return a boolean value

function addUser($user, $pwd) {
	$mysqli = connectDB();
	$query = "SELECT username FROM users where username = '$user'";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Select Query Prep Failed 1: ".$mysqli->error);
		return 0;
	}

	$result->execute();
	$final = store_result();
	$num = $final->num_rows;
	if($num) {
		return -1;
	}
	else {
		$result->close();
		$query = "INSERT INTO users (username, password) VALUES (?,?)";
		$result = $mysqli->prepare($query);
		
		if(!$result) {
			die("Insert Query Prep Failed 2: ".$mysqli->error);
			return 0;
		}
 
		$result->bind_param('ss', $user, $pwd); 
		$result->execute();		
	}
	$result->close();
	return 1;
}


function updatePortait($user_id, $portrait, $portrait_name) {
	$mysqli = connectDB();
	$query = "UPDATE users set portrait = '$portrait',  portrait_name = '$portrait_name' where user_id = $user_id";
	if(!$mysqli->query($query)) {
		die("Update Query Prep Failed: ".$mysqli->error);
	}
}


function login($user, $pwd) {
	$mysqli = connectDB();
	$query = "SELECT username, password FROM users where username = '$user'";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->bind_result($username,$password);
	$result->fetch();
	$result->close();
	return ($username == $user && $password == $pwd);
}

function getUser($user_id){
	$mysqli = connectDB();
	$query = "SELECT username FROM users where user_id = $user_id";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->bind_result($username);
	$result->fetch();
	$result->close();
	return $username;
}

function getUserId($username){
	$mysqli = connectDB();
	$query = "SELECT user_id FROM users where username = '$username'";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->bind_result($user_id);
	$result->fetch();
	$result->close();
	return $user_id;
}

function getSaltedPwdByName($username){
	$mysqli = connectDB();
	$query = "SELECT password FROM users where username = '$username'";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->bind_result($password);
	$result->fetch();
	$result->close();
	return $password;
}


function getUserByStoryId($story_id){
	$mysqli = connectDB();
	$query = "SELECT username FROM users LEFT JOIN stories ON users.user_id = stories.user_id where story_id = $story_id";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->bind_result($username);
	$result->fetch();
	$result->close();
	return $username;
}

function getStory($story_id) {
	$mysqli = connectDB();
	$query = "SELECT title ,content ,link ,user_id, post_time FROM stories WHERE story_id = $story_id";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->bind_result($title,$content,$link,$user_id,$post_time);
	$result->fetch();
	$story = array();
	array_push($story,$title,$content,$link,$user_id,$post_time);
	$result->close();

	return $story;
}

function getAllStories(){
	$mysqli = connectDB();
	$query = "SELECT story_id ,title ,content ,link ,user_id, post_time FROM stories ORDER BY post_time DESC";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->bind_result($story_id,$title,$content,$link,$user_id,$post_time);
	$stories = array();
	while($result->fetch()){
		$temp = array();
		array_push($temp, $story_id,$title,$content,$link,$user_id,$post_time);
		array_push($stories, $temp);
	}
	$result->close();
	return $stories;
}

function getStoriesByUserName($username){
	$mysqli = connectDB();
	$query = "SELECT story_id ,title ,content ,link ,stories.user_id, post_time FROM stories LEFT JOIN users ON stories.user_id = users.user_id WHERE users.username = '$username' ORDER BY post_time DESC";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->bind_result($story_id,$title,$content,$link,$user_id,$post_time);
	$stories = array();
	while($result->fetch()){
		$temp = array();
		array_push($temp, $story_id,$title,$content,$link,$user_id,$post_time,$username);
		array_push($stories, $temp);
	}
	$result->close();
	return $stories;
}

function getComments($story_id) {
	$mysqli = connectDB();
	$query = "SELECT content, user_id ,comment_time FROM comments where story_id = $story_id";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->bind_result($content,$user_id,$comment_time);
	$comments = array();

	while ($result->fetch()) {
		$c = array();
		array_push($c, $content, $user_id, $comment_time);
		array_push($comments, $c);
	}
	$result->close();

	return $comments;
}

function getCommentsByUsername($username){
	$mysqli = connectDB();
	$query = "SELECT content,comment_time,story_id,comment_id FROM comments LEFT JOIN users ON comments.user_id = users.user_id where username = '$username' ORDER BY comment_time DESC";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->bind_result($content,$comment_time,$story_id,$comment_id);
	$comments = array();

	while ($result->fetch()) {
		$c = array();
		array_push($c, $content, $comment_time, $story_id,$comment_id);
		array_push($comments, $c);
	}
	$result->close();

	return $comments;
}

function addStory($title, $content, $link, $user_id, $post_time) {
	$mysqli = connectDB();
	$query = "INSERT INTO stories (title, content, link, user_id, post_time) VALUES (?,?,?,?,?)";
	$result = $mysqli->prepare($query);
	
	if(!$result) {
		die("Insert Query Prep Failed: ".$mysqli->error);
	}

	$result->bind_param('sssds', $title, $content, $link, $user_id, $post_time); 
	$result->execute();		
	$result->close();
}

function getMaxStoryId(){
	$mysqli = connectDB();
	$query = "SELECT MAX(story_id) FROM stories";
	$result = $mysqli->prepare($query);
	
	if(!$result) {
		die("Insert Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();	
	$result->bind_result($story_id); 
	$result->fetch();
	$result->close();
	return $story_id;
}

function addComment($content, $user_id, $comment_time, $story_id) {
	$mysqli = connectDB();
	$query = "INSERT INTO comments (content, user_id, comment_time, story_id) VALUES (?,?,?,?)";
	$result = $mysqli->prepare($query);
	
	if(!$result) {
		die("Insert Query Prep Failed: ".$mysqli->error);
	}

	$order =array("fuck");
	$replace = "****";
	$content = str_replace($order, $replace, $content);

	$result->bind_param('sdsd', $content, $user_id, $comment_time , $story_id); 
	$result->execute();		
	$result->close();
}


function updatePassword($user_id, $pwd) {
	$mysqli = connectDB();
	$query = "UPDATE users SET password = '$pwd' WHERE user_id = $user_id";
	if(!$mysqli->query($query)) {
		die("Update Query Prep Failed: ".$mysqli->error);
	}
}

function updateStory($story_id, $content) {
	$mysqli = connectDB();
	$order =array("'");
	$replace = "''";
	$content_tmp = str_replace($order, $replace, $content);
	$query = "UPDATE stories SET content = '$content_tmp' WHERE story_id = $story_id";
	if(!$mysqli->query($query)) {
		die("Update Query Prep Failed: ".$mysqli->error);
	}
}

function updateStoryLink($story_id,$link){
	$mysqli = connectDB();
	$query = "UPDATE stories SET link = '$link' WHERE story_id = $story_id";
	if(!$mysqli->query($query)) {
		die("Update Query Prep Failed: ".$mysqli->error);
	}
}



function updateComment($comment_id, $content) {
	$mysqli = connectDB();
	$order =array("'");
	$replace = "''";
	$content_tmp = str_replace($order, $replace, $content);
	$query = "UPDATE comments SET content = '$content_tmp' WHERE comment_id = $comment_id";
	if(!$mysqli->query($query)) {
		die("Update Query Prep Failed: ".$mysqli->error);
	}
}



function deleteStory($story_id) {
	$mysqli = connectDB();
	$query = "DELETE FROM stories WHERE story_id = $story_id";
	if(!$mysqli->query($query)) {
		die("Delete Query Prep Failed: ".$mysqli->error);
	}
}



function deleteComment($comment_id) {
	$mysqli = connectDB();
	$query = "DELETE FROM comments WHERE comment_id = $comment_id";
	if(!$mysqli->query($query)) {
		die("Delete Query Prep Failed: ".$mysqli->error);
	}
}

#addStory("big news","dingding and kaiyi","link yo",2,date("Y-m-d h:i:s"));
#addComment("so amazing!!!",2,date("Y-m-d h:i:s"),10);
?>