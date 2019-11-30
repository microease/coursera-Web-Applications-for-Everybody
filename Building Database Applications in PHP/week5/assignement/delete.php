<?php

require_once "pdo.php";
session_start();

if(isset($_POST["delete"]) && isset($_POST["autos_id"]))
{
	$_SESSION["delete"] = $_POST["delete"];
	$_SESSION["autos_id"] = $_POST["autos_id"];
	header("Location:delete.php?autos_id=" . $_POST["autos_id"]);
	return;
}

if (isset($_SESSION["delete"]) && isset($_SESSION["autos_id"])) 
{
	$sql = "DELETE FROM autos WHERE autos_id = :zip";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(":zip" => $_SESSION["autos_id"]));
	$_SESSION["success"] = "Record deleted";
	unset($_SESSION["delete"]);
	unset($_SESSION["autos_id"]);
	header("Location: index.php");
	return;
}

// Guardian: Make sure that autos_id is present
if(!isset($_GET["autos_id"]))
{
	$_SESSION["error"] = "Missing id";
	header("Location: index.php");
	return;
}

$stmt = $pdo->prepare("SELECT make, autos_id FROM autos WHERE autos_id = :xyz");
$stmt->execute(array("xyz" => $_GET["autos_id"]));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row === false)
{
	$_SESSION["error"] = "Bad value for id";
	header("Location: index.php");
	return;
}

$make = $row["make"];
$autos_id = $row["autos_id"];

?>

<!DOCTYPE html>

<html lang = "en">

	<head>
		<meta charset = "utf-8">
		<title>microease - Deleting...</title>
		<?php require_once "bootstrap.php" ?>
	</head>

	<body>
		<div class = "container">
			<p>
				Confirm: Deleting <?php echo($make); ?>
			</p>
			<form method = "post">
				<input type="hidden" name="autos_id" value=<?php echo('"' . $autos_id . '"'); ?>>
				<input type = "submit" name="delete" value = "Delete">
				<a href = "index.php">Cancel</a>
			</form>
		</div>
	</body>

</html>