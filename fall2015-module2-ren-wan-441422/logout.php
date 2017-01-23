<?php
if (empty($_SESSION)) // if the session not yet started
 {
 	session_start();
 }  
	unset($_SESSION['username']);
	session_destroy();

	header("Location: login.php");
	exit;
?>