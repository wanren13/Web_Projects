<!DOCTYPE html>
<html>
	<head>
		<style type="text/css">
		html {
	    	display: table;
	    	margin: auto;
		}
		td.num {
			width: 50px;
		}
		td.op {
			width: 150px;
			text-align: center;
		}
		/*
		td.msg {
		}
		*/
		legend {
			font-size: 24pt;
			font-weight: bold;
			text-align: center;
		}
		</style>
		<title>PHP Calculator</title>
	</head>
	<body>
		<?php
			// var_dump($_GET['num1']);

			$denominator_is_zero = false;
			$operator = " ";
			// $n1_is_numeric = true;
			// $n2_is_numeric = true;

			if (isset($_GET['num1'])) {
				$n1_is_set = true;
				if ($_GET['num1'] != '') {
					if (is_numeric($_GET['num1'])) {
			    		$n1 = (float)$_GET['num1'];
			    		// echo "<p>n1 is $n1</p>";			    		
			    		$n1_is_numeric = true;
			    	}
			    	else {
			    		$n1_is_numeric = false;
			    	}
			    	$n1_is_empty = false;
			    }
			    else {
			    	$n1_is_empty = true;
			    	$n1_is_numeric = false;
			    }
			}
			else {
				$n1_is_set = false;
				$n1_is_empty = true;
		    	$n1_is_numeric = false;	
			//	echo "<p>n1 is not set</p>";

			}
			
			if (isset($_GET['num2'])) {
				$n2_is_set = true;
				if ($_GET['num2'] != '') {
					if (is_numeric($_GET['num2'])) {
						$n2 = (float)$_GET['num2'];
						// echo "<p>n2 is $n2</p>";						
			    		$n2_is_numeric = true;
					}
					else {
						$n2_is_numeric = false;
					}					
					$n2_is_empty = false;
				}
				else {
					$n2_is_empty = true;
					$n2_is_numeric = false;
				}
				// var_dump($n2);
			}      
			else {
				$n2_is_set = false;
				$n2_is_empty = true;
				$n2_is_numeric = false;
			//	echo "<p>n2 is not set</p>";
			}

			if (/*isset($_GET['num1']) && isset($_GET['num2']) && */ $n1_is_numeric && $n2_is_numeric && isset($_GET['options']) && $_GET['options'] != '') {
				$operator = $_GET['options'];
				switch ($_GET['options']) {
					case "+":
						$result = $n1 + $n2;
						break;
					case "-":
						$result = $n1 - $n2;
						break;
					case "*":
						$result = $n1 * $n2;
						break;
					case "/":
						if ($n2 != 0) {
							$result = $n1 / $n2; 
						}
						else {
							$denominator_is_zero = true;
						}
						break;
				}
				// var_dump($result);
			}

			// echo "n1 empty"; var_dump($n1_is_empty);
			// echo "n1 numeric"; var_dump($n1_is_numeric);
			// echo "n2 empty"; var_dump($n2_is_empty);
			// echo "n2 numeric"; var_dump($n2_is_numeric);
		?>
		<!-- <form action ="calculator.php" method = "GET"> -->
		<form action ="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method = "GET">
		<fieldset style = "width:600px">
		<legend>Calculator</legend>
		<table>
			<tr>				
				<th>First Number</th>
				<th></th>
				<th>Second Number</th>
				<th></th>
			</tr>
			<tr>
				<td class = "num">
					<input type = "text" name = "num1" value = 
					<?php
					if (!$n1_is_empty && ($n2_is_empty || !$n2_is_numeric) && $n1_is_numeric) {
						echo $n1;
						//echo "<p>";
						//var_dump($n1);
						//echo "</p>";
					}
					else {
						echo "\"\"";
					}
					?>>
				</td>
				<td class = "op">
					<input type = "radio" name = "options" value = "+" checked>+
					<input type = "radio" name = "options" value = "-">-
					<input type = "radio" name = "options" value = "*">*
					<input type = "radio" name = "options" value = "/">/
				</td>
				<td class = "num">
					<input type = "text" name= "num2" value = 
					<?php
					if (($n1_is_empty || !$n1_is_numeric) && !$n2_is_empty && $n2_is_numeric) {
						echo $n2;
					}
					else {
						echo "\"\"";
					}
					?>>
				</td>
				<td><input type="submit" value="Calculate"/></td>
			</tr>					
			<tr>
				<td class = "msg" colspan = "4">
				<?php
					if (isset($result)) {
					    echo "<b>Result: ".$n1." ".$operator." ".$n2." = ".$result."</b>";
					}
					else {
						if ($n1_is_set && $n2_is_set) {
							echo "<p><b>Error Message: </b></p>";
							if ($denominator_is_zero) {
								echo "<p>Denominator cannot be 0.</p>";
							} 
							if ($n1_is_empty) {
								echo "<p>Please input the first number.</p>";
							}
							else {
								if (!$n1_is_numeric) {
									echo "<p>The input for the first number is not a number.</p>";
								}
							}
							if ($n2_is_empty) {
								echo "<p>Please input the second number.</p>";
							}
							else {
								if (!$n2_is_numeric) {
									echo "<p>The input for the second number is not a number.</p>";
								}
							}
						}
					}
				?>
				</td>
			</tr>
		</table>
		</fieldset>
		</form>	
		<p><a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-html401" alt="Valid HTML 4.01 Strict" height="31" width="88"></a></p>
	</body>	
</html>