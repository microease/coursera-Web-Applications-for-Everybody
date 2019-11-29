<?php
require_once "pdo.php";

session_start();

if(!isset($_SESSION["email"]) || strlen($_SESSION["email"]) < 1)
	die("Not Logged in");

if(isset($_POST["cancel"]))
{
	// Redirect the browser to view.php
	header("Location: view.php");
	return;
}

// Check to see if we have some POST data, if we do store it in SESSION
if(isset($_POST["make"]) && isset($_POST["year"]) && isset($_POST["mileage"]))
{
	$_SESSION["make"] = $_POST["make"];
	$_SESSION["year"] = $_POST["year"];
	$_SESSION["mileage"] = $_POST["mileage"];

	header("Location: add.php");
	return;
}

$insertedRecord = false;

if(isset($_SESSION["make"])  && isset($_SESSION["year"]) && isset($_SESSION["mileage"]))
{
	$make = htmlentities($_SESSION["make"]);
	$year = htmlentities($_SESSION["year"]);
	$mileage = htmlentities($_SESSION["mileage"]);

	if(strlen($make) < 1)
		$_SESSION["failure"] = "Make is required";
	else if(!(is_numeric($year) && is_numeric($mileage)))
		$_SESSION["failure"] = "Mileage and year must be numeric";
	else
	{
		//*
		$sql = "INSERT INTO autos (make, year, mileage) values ( :mk, :yr, :mi)";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(":mk" => $make, ":yr" => $year, ":mi" => $mileage));
		$_SESSION["success"] = "Record inserted";
		unset($_SESSION["make"]);
		unset($_SESSION["year"]);
		unset($_SESSION["mileage"]);
		header("Location: view.php");
		return;
		//*/
	}
	unset($_SESSION["make"]);
	unset($_SESSION["year"]);
	unset($_SESSION["mileage"]);
}

?>

<!DOCTYPE html>

<html lang = "en">

	<head>
		<meta charset = "utf-8">
		<title>microease Automobile Tracker</title>
		<?php  require_once "bootstrap.php" ?>
	</head>

	<body>
		<div class = "container">
			<h1>Tracking Autos for <?php echo(htmlentities($_SESSION["email"])) ?></h1>
			<?php
				if(isset($_SESSION["failure"]))
				{
					echo('<p style = "color : red;">' . htmlentities($_SESSION["failure"]) . "</p>\n");
					unset($_SESSION["failure"]);
				}
			?>
			<form method = "post">
				<p>
					Make :
					<input type = "text" name = "make" size = "60">
				</p>
				<p>
					Year :
					<input type = "text" name = "year">
				</p>
				<p>
					Mileage :
					<input type = "text" name = "mileage">
				</p>
				<input type = "submit" value="Add">
				<input type = "submit" name = "cancel" value = "Cancel">
			</form>
		</div>
	</body>

</html>