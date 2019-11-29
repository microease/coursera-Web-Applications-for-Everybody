<?php

session_start();

if(isset($_POST["cancel"]))
{
	header("Location: index.php");
	return;
}

$alt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123

// Check to see if we have some POST data, if we do store it in SESSION
if(isset($_POST["email"]) && isset($_POST["pass"]))
{
	$_SESSION["email"] = $_POST["email"];
	$_SESSION["pass"] = $_POST["pass"];

	header("Location: login.php");
	return;
}

// Check to see if we have some new data in $_SESSION, if we do process it
if(isset($_SESSION["email"]) & isset($_SESSION["pass"]))
{
	$username = $_SESSION["email"];
	$password = $_SESSION["pass"];

	if(strlen($username) < 1 || strlen($password) < 1)
		$_SESSION["error"] = "Email and password are required";
	else if (strpos($username, '@') === false)
		$_SESSION["error"] = "Email must have an at-sign (@)";
	else
	{
		$check = hash("md5", $alt.$password);
		if($check == $stored_hash)
		{
			// Redirect the browser to view.php
			header("Location: view.php");
			error_log("Login success ". $username);
			return;
		}
		else
		{
			$_SESSION["error"] = "Incorrect password";
			error_log("Login fail" . $username . "$check");
		}

	}

	unset($_SESSION["email"]);
	unset($_SESSION["pass"]);
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
				if(isset($_SESSION["error"]))
				{
					echo('<p style = "color:red;">' . htmlentities($_SESSION["error"]) . "</p>\n");
					unset($_SESSION["error"]);
				}
			?>

			<form method = "post">
				<label for = "nam">Email</label>
				<input type = "text" name = "email" id = "nam"><br>
				<label for = "id_1723">Password</label>
				<input type = "text" name = "pass" id = "id_1723"><br>
				<input type = "submit" value = "Log In">
				<input type = "submit" name = "cancel" value = "Cancel">
			</form>
		</div>
	</body>

</html>