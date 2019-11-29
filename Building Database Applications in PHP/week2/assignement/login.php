<?php

if(isset($_POST["cancel"]))
{
	// Redirect the browser to index.php
	header("Location: index.php");
	return;
}

$alt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123

$failure = false; // If we have no POST data

// Check to see if we have some POST data, if we do process it
if(isset($_POST["who"]) && isset($_POST["pass"]))
{
	$username = $_POST["who"];
	$password = $_POST["pass"];

	if(strlen($username) < 1 || strlen($password) < 1)
		$failure = "Email and password are required";
	elseif (strpos($username, '@') === false) 
		$failure = "Email must have an at-sign (@)";
	else
	{
		$check = hash("md5", $alt.$password);
		if ($check == $stored_hash) 
		{
			// Redirect the browser to autos.php
			header("Location: autos.php?name=".urldecode($username));
			error_log("Login success " . $username);
			return;
		}
		else
		{
			$failure = "Incorrect password";
			error_log("Login fail " . $username . "$check");
		}
	}
}

?>

<!DOCTYPE html>

<html lang = "en">

	<head>
		<meta charset = "utf-8">
		<title>microease - Login Page</title>
		<?php require_once "bootstrap.php" ?>
	</head>

	<body>
		<div class = "container">
			<h1>Please Log In</h1>
			<?php
				if($failure !== false)
					echo ('<p style = "color : red;">' . htmlentities($failure) . "</p>\n");
			?>

			<form method = "post">
				<label for = "nam">User Name</label>
				<input type = "text" name = "who" id = "nam"><br>
				<label for = "id_1723">Password</label>
				<input type = "text" name = "pass" id = "id_1723"><br>
				<input type = "submit" value = "Log In">
				<input type = "submit" name = "cancel" value = "Cancel">
			</form>
		</div>
	</body>

</html>