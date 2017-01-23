<?php

if (empty($_SESSION)) {
	session_start();
}

if (!isset($_SESSION['username'])) { //if not yet logged in
	header("Location: login.php");// send to login page
	exit;
}

$userpath = "../../users/".$_SESSION['username'];

$full_path = $userpath."/".$_GET['filename'];

$finfo = new finfo(FILEINFO_MIME_TYPE);

$mime = $finfo->file($full_path);

// Finally, set the Content-Type header to the MIME type of the file, and display the file.
header("Content-Type: ".$mime);
ob_clean();
readfile($full_path);
?>