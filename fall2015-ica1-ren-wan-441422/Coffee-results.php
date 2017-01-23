!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>

		<title>Coffee Result</title>
	</head>
	<body>
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

		if(!isset($_POST['submit_coffee']) {
			die('Not submited');
		}

		$mysqli = connectDB();
		$coffee = $_POST['coffee'];
		$query = "SELECT user, rating, comment FROM ratings, coffees where id = coffeeId AND name = $coffee";
		$result = $mysqli->prepare($query);

		if(!$result) {
			die("Query Prep Failed: ".$mysqli->error);
		}

		$result->execute();
		$result->bind_result($user, $rating, $comment);
		$result->fetch();
		$result->close();


		echo $user;
		echo $rating;
		echo $comment;










		?>




	</body>	
</html>