<?php

// read only function
function loadusers() {
	$userlist = array();

	$h = fopen("users.txt", "r");

	while( !feof($h)) {
	    $line = fgets($h);
	    list($user, $pwd) = explode(" ", $line);
	    if (empty($user)) break;
	    $userlist[trim($user)] = trim($pwd);
	}

	fclose($h);

	return $userlist;
}
?>