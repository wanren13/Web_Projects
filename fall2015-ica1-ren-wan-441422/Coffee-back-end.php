<?php

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

// **************************************************************************



if (!isset($_POST['submit_rating'])){
	die("Not submitted");
}


$user = $_POST['user'];
$coffee = $_POST['coffee'];
$rating = int($_POST['userRating']);
$comment = $_POST['comments'];


$mysqli = connectDB();
$query = "INSERT INTO coffees (name) VALUES (?)";
$result = $mysqli->prepare($query);

if(!$result) {
	die("Insert Query Prep Failed: ".$mysqli->error);
}

$result->bind_param('s', $coffee); 
$result->execute();
$cId = $result->insert_id;
$result->close();

// echo $cId;




header("Location: Cofee-main.html");



?>