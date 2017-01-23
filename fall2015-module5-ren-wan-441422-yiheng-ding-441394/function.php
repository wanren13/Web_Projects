<?php
function connectDB(){
	$db_host="52.26.184.7";
	$db_user="wa";
	$db_psw="123";
	$db_name="calendar";
	$mysqli = new mysqli($db_host, $db_user, $db_psw, $db_name);
	if ($mysqli->connect_error) {
		echo "Connection Error: ".$mysqli->connect_error;
		exit;
	}
	return $mysqli;}

//reset auto increment
function resetUsersAutoIncrement(){
	$mysqli = connectDB();
	$query = "ALTER TABLE users AUTO_INCREMENT = 1";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->close();}
function resetEventsAutoIncrement(){
	$mysqli = connectDB();
	$query = "ALTER TABLE events AUTO_INCREMENT = 1";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->close();}

//regular expression check
function usernameCheck($user){
	if (!preg_match('/^[\w_\-]+$/', $user)) {
        return "Invalid username";
    }
    $user = htmlspecialchars($user);
    return $user;}
function passwordCheck($pwd){
	if (!preg_match('/^[\w\d~!@#$%^&*_\-]+$/', $pwd)){
    	return "Invalid password";
    }
    $pwd = htmlspecialchars($pwd);
    return $pwd;}
function textCheck($text){
	if (!preg_match('/^[\w\d\s,.~!@_()\-]+$/', $text)){
    	return "Invalid Text";
    }
    $text = htmlspecialchars($text);
    return $text;}
function timestampCheck($timestamp){
	if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $timestamp)){
    	return "Invalid timestamp";
    }
    $timestamp = htmlspecialchars($timestamp);
    return $timestamp;}

//User part
function addUser($user, $pwd) {
    $user =  usernameCheck($user);
    if ($user == "Invalid username"){
    	return $user;
    }
    $pwd = passwordCheck($pwd);
    if  ($pwd == "Invalid password"){
    	return $pwd;
    } 
    $pwd = crypt($pwd,"");

	$mysqli = connectDB();
	$query = "SELECT username FROM users where username = '$user'";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Select Query Prep Failed 1: ".$mysqli->error);
		return "Query Fail";
	}

	$result->execute();
	if($result->fetch()) {
		return "User Already Exists";
	}
	else {
		$result->close();
		$query = "INSERT INTO users (username, password) VALUES (?,?)";
		$result = $mysqli->prepare($query);
		
		if(!$result) {
			die("Insert Query Prep Failed 2: ".$mysqli->error);
			return "Query Fail";
		}
 
		$result->bind_param('ss', $user, $pwd); 
		$result->execute();		
	}
	$result->close();
	return "success";}
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
	return $user_id;}
function login($user, $pwd) {
	$user =  usernameCheck($user);
    if ($user == "Invalid username"){
    	return $user;
    }
    $pwd = passwordCheck($pwd);
    if  ($pwd == "Invalid password"){
    	return $pwd;
    }

	$mysqli = connectDB();

	$query = "SELECT username, password FROM users where username = '$user'";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
		return "Query Fail";
	}

	$result->execute();
	$result->bind_result($username,$password);
	if($result->fetch()){
		$pwd = crypt($pwd,$password);
	}
	else{
		return "Incorrect username or password";
	}
	$result->close();
	if ($username == $user && $password == $pwd){
		return "success";
	}
	else{
		return "Incorrect username or password";
	}}

#echo addUser("dingding","auisdhia1@*#!_~");
#echo login("dingdi ng","auisdhia1@*#!_");

//event part
function addEvent($user_id,$content,$timestamp,$tag_id){
	$content = textCheck($content);
	if($content == "Invalid Text" ){return "Invalid Content";}
	$timestamp = timestampCheck($timestamp);
	if($timestamp == "Invalid timestamp"){return $timestamp;}

	$mysqli = connectDB();
	$query = "INSERT INTO events (user_id, content, timestamp, tag_id) VALUES (?,?,?,?)";
	$result = $mysqli->prepare($query);
	
	if(!$result) {
		die("Insert Query Prep Failed: ".$mysqli->error);
		return "Query Fail";
	}

	$result->bind_param('dssd', $user_id,$content,$timestamp,$tag_id); 
	$result->execute();

	echo json_encode(
		array(
			"success"=>true,
			"id"=> $mysqli->insert_id
		)
	);

	$result->close();
	return "success";
}


function addSharedEvent($event_id, $owner_id, $self_id){
	$mysqli = connectDB();
	$query = "INSERT INTO shared_events (event_id, owner_id, friend_id) VALUES (?,?,?)";
	$result = $mysqli->prepare($query);
	
	if(!$result) {
		die("Insert Query Prep Failed: ".$mysqli->error);
		return "Query Fail";
	}

	$result->bind_param('ddd', $event_id, $owner_id, $self_id); 
	$result->execute();

	// echo json_encode(
	// 	array(
	// 		"success"=>true,
	// 		"id"=> $mysqli->insert_id
	// 	)
	// );

	$result->close();
	return "success";
}



function getEvents($user_id){
	$mysqli = connectDB();
	$query = "SELECT event_id ,content ,timestamp, tag_id FROM events  WHERE user_id=$user_id ORDER BY timestamp DESC";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->bind_result($event_id,$content,$timestamp,$tag);
	$events = array();
	while($result->fetch()){
		$temp = array();
		array_push($temp, $event_id,$content,$timestamp,$tag);
		array_push($events, $temp);
	}
	$result->close();
	return $events;
}


function getTags(){
	$mysqli = connectDB();
	$query = "SELECT tag_id, tag_name FROM tags";
	$result = $mysqli->prepare($query);

	if(!$result) {
		die("Query Prep Failed: ".$mysqli->error);
	}

	$result->execute();
	$result->bind_result($tag_id,$tag_name);
	$tags = array();
	while($result->fetch()){
		$temp = array();
		array_push($temp, $tag_id, $tag_name);
		array_push($tags, $temp);
	}
	$result->close();
	return $tags;
}


function deleteEvent($event_id){
	$mysqli = connectDB();
	$query = "DELETE FROM events WHERE event_id = $event_id";
	if(!$mysqli->query($query)) {
		die("Delete Query Prep Failed: ".$mysqli->error);
	}

	return "success";
}

#echo date("Y-m-d")."<br>".date("h:i:s");
#echo addEvent(1,"asuidhasuhi ,.~@()"," ",date("Y-m-d h:i:s"));
// addEvent(1,"asuidhasuhi ,.~@()","1991-10-03 09:12:11");
// addEvent(1,"where to find kaiyi","1992-10-03 09:12:11");
// addEvent(1,"I love kaiyi ","2012-10-03 10:33:11");
// addEvent(1,"oh my god","1998-10-03 03:12:11");

// $events = getEvents(1);
// foreach ($events as $event){
// 	echo $event[2]."<br>";
// 	$date = strtotime($event[2]);
// 	echo $date."<br>" ;
// 	$date = date("Y-m-d",$date);
// 	echo $date."<br>" ;
// 	echo "---------------<br>";
// 	echo $event[0]."----".$event[1]."----".$date."<br>";
// }

?>

