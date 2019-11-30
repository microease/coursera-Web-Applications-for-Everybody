<?php

require_once "pdo.php";

session_start();

if(!isset($_SESSION["email"]) || strlen($_SESSION["email"]) < 1)
	die("ACCESS DENIED");

if (isset($_POST["cancel"])) 
{
	// Redirect to index.php
	header("Location: index.php");
	return;
}

// Check to see if we have some POST data, if we do store it in SESSION
if(isset($_POST["make"]) && isset($_POST["model"]) && isset($_POST["year"]) && isset($_POST["mileage"]))
{
	$_SESSION["make"] = $_POST["make"];
	$_SESSION["model"] = $_POST["model"];
	$_SESSION["year"] = $_POST["year"];
	$_SESSION["mileage"] = $_POST["mileage"];

	header("Location: add.php");
	return;
}

if (isset($_SESSION["make"]) && isset($_SESSION["model"]) && isset($_SESSION["year"]) && isset($_SESSION["mileage"])) 
{
	$make = $_SESSION["make"];
	$model = $_SESSION["model"];
	$year = $_SESSION["year"];
	$mileage = $_SESSION["mileage"];
	unset($_SESSION["make"]);
	unset($_SESSION["model"]);
	unset($_SESSION["year"]);
	unset($_SESSION["mileage"]);

	if (strlen($make) < 1 || strlen($model) < 1 || strlen($year) < 1 || strlen($mileage) < 1) 
		$_SESSION["failure"] = "All field are required";
	else if(!is_numeric($year))
		$_SESSION["failure"] = "Year must be numeric";
	else if(!is_numeric($mileage))
		$_SESSION["failure"] = "Mileage must be numeric";
	else
	{
		$sql = "INSERT INTO autos (make, model, year, mileage) VALUES (:mk, :md, :yr, :mi)";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(":mk" => $make, ":md" => $model, ":yr" => $year, ":mi" => $mileage));
		$_SESSION["success"] = "Record added";
		header("Location: index.php");
		return;
	}
}

?>

<!DOCTYPE html>

<html lang = "en">

	<head>
		<meta charset = "utf-8">
		<title>microease - Automobile Tracker</title>
		<?php require_once "bootstrap.php" ?>
	</head>

	<body>
		<div class = "container">

			<h1>Tracking Automobiles for <?php echo(htmlentities($_SESSION["email"])); ?></h1>

			<?php
				if (isset($_SESSION["failure"])) 
				{
					echo('<p style = "color:red;">' . $_SESSION["failure"] . "</p>\n");
					unset($_SESSION["failure"]);
				}
			?>

			<form method = "post">
				<p>
					Make : 
					<input type = "text" name="make" size = "40">
				</p>
				<p>
					Model : 
					<input type = "text" name="model" size = "40">
				</p>
				<p>
					Year : 
					<input type = "text" name="year" size = "10">
				</p>
				<p>
					Mileage : 
					<input type = "text" name="mileage" size = "10">
				</p>
				<input type = "submit" value = "Add">
				<input type = "submit" name="cancel" value = "Cancel">
			</form>
		</div>
	</body>

</html>