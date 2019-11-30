<?php

require_once "pdo.php";

session_start();

if(isset($_POST["cancel"]))
{
	// Redirect to index.php
	header("Location: index.php");
	return;
}

if(isset($_POST["make"]) && isset($_POST["model"]) && isset($_POST["year"]) && isset($_POST["mileage"]) && isset($_POST["autos_id"]))
{
	$_SESSION["make"] = $_POST["make"];
	$_SESSION["model"] = $_POST["model"];
	$_SESSION["year"] = $_POST["year"];
	$_SESSION["mileage"] = $_POST["mileage"];
	$_SESSION["autos_id"] = $_POST["autos_id"];

	header("Location: edit.php?autos_id=" . $_POST["autos_id"]);
	return;
}

if(isset($_SESSION["make"]) && isset($_SESSION["model"]) && isset($_SESSION["year"]) && isset($_SESSION["mileage"]) 
	&& isset($_SESSION["autos_id"]))
{
	$make = $_SESSION["make"];
	$model = $_SESSION["model"];
	$year = $_SESSION["year"];
	$mileage = $_SESSION["mileage"];
	$autos_id = $_SESSION["autos_id"];

	if(strlen($make) < 1 || strlen($model) < 1 || strlen($year) < 1 || strlen($mileage) < 1)
		$_SESSION["failure"] = "All field are required";
	else if(!is_numeric($year))
		$_SESSION["failure"] = "Year must be numeric";
	else if(!is_numeric($mileage))
		$_SESSION["failure"] = "Mileage must be numeric";
	else
	{
		$sql = "UPDATE autos SET make = :make, model = :model, year = :year, mileage = :mileage WHERE autos_id = :autos_id";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(":make" => $make, ":model" => $model, ":year" => $year, 
			":mileage" => $mileage, ":autos_id" => $autos_id));
		$_SESSION["success"] = "Record edited";
		unset($_SESSION["make"]);
		unset($_SESSION["model"]);
		unset($_SESSION["year"]);
		unset($_SESSION["mileage"]);
		unset($_SESSION["autos_id"]);
		header("Location: index.php");
		return;
	}
	unset($_SESSION["make"]);
	unset($_SESSION["model"]);
	unset($_SESSION["year"]);
	unset($_SESSION["mileage"]);
	unset($_SESSION["autos_id"]);
}

// Guardian: Make sure that autos_id is present
if(!isset($_GET["autos_id"]))
{
	$_SESSION["error"] = "Missing id";
	header("Location: index.php");
	return;
}

$stmt = $pdo->prepare("SELECT * FROM autos WHERE autos_id = :xyz");
$stmt->execute(array(":xyz" => $_GET["autos_id"]));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row === false)
{
	$_SESSION["error"] = "Bad value for id";
	header("Location: index.php");
	return;
}

$mk = htmlentities($row["make"]);
$md = htmlentities($row["model"]);
$mi = htmlentities($row["mileage"]);
$yr = htmlentities($row["year"]);
$autos_id = $row["autos_id"];

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
			<h1>Editing Automobile</h1>

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
					<input type = "text" name="make" size = "40" value="<?php echo($mk) ?>">
				</p>
				<p>
					Model : 
					<input type = "text" name="model" size = "40" value="<?php echo($md) ?>">
				</p>
				<p>
					Year : 
					<input type = "text" name="year" size = "10" value="<?php echo($yr) ?>">
				</p>
				<p>
					Mileage : 
					<input type = "text" name="mileage" size = "10" value="<?php echo($mi) ?>">
				</p>
				<input type="hidden" name="autos_id" value="<?php echo($autos_id) ?>">
				<input type = "submit" value = "Save">
				<input type = "submit" name="cancel" value = "Cancel">
			</form>
		</div>
	</body>

</html>