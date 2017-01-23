<?php
if(empty($_SESSION))
{
	session_start();
}

if( !(isset($_SESSION['token']) and $_SESSION['token']==$_POST['token']) ){
	echo json_encode(array(
		"success"=>false,
		"message"=>"Request forgery detected"));
	exit;
}

include 'function.php';
header("Content-Type: application/json");

if(!isset($_SESSION['username'])){
	exit;
}

$content = $_POST['content'];
$timestamp = $_POST['timestamp'];
$username = $_SESSION['username'];
$tag_id = $_POST['tag_id'];

$user_id = getUserId($username);

if(!addEvent($user_id,$content,$timestamp,$tag_id)) {
	echo json_encode(
		array(
			"success"=>false,
			"message"=>"Cann't add event"
		)
	);
}

exit;
?>