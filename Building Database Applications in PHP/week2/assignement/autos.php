
<?php
// Demand a GET parameter
require_once "pdo.php";

if(!isset($_GET["name"]) || strlen($_GET["name"]) < 1)
	die("Name parameter missing");

// If the user requested logout, go back to index.php
if(isset($_POST["logout"]))
{
	header("Location: index.php");
	return;
}

$failure = false;
$insertedRecord = false;

if(isset($_POST["make"]) && isset($_POST["year"]) && isset($_POST["mileage"]))
{
	$make = htmlentities($_POST["make"]);
	$year = htmlentities($_POST["year"]);
	$mileage = htmlentities($_POST["mileage"]);
	$insertedRecord = false;

	if(strlen($make) < 1)
		$failure = "Make is required";
	elseif(!(is_numeric($year) && is_numeric($mileage)))
		$failure = "Mileage and year must be numeric";
	else
	{
		//*
		$sql = "INSERT INTO autos (make, year, mileage) values ( :mk, :yr, :mi)";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(":mk" => $make, ":yr" => $year, ":mi" => $mileage));
		//*/
		$insertedRecord = true;
	}
}

?>

<!DOCTYPE html>

<html lang = "en">

	<head>
		<meta charset = "utf-8">
		<title>microease Automobile Tracker</title>
		<?php require_once "bootstrap.php"; require_once "pdo.php" ?>
	</head>

	<body>
		<div class = "container">
			<?php  
				$checkName = $_REQUEST["name"];
				if(isset($checkName))
					echo "<h1>Tracking Autos for " . htmlentities($checkName) . "</h1>\n";
				if($failure !== false)
					echo ('<p style = "color : red;">' . htmlentities($failure) . "</p>\n");
				elseif($insertedRecord)
					echo ('<p style = "color : green;">Record inserted' . "</p>\n");
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
				<input type = "submit" value = "Add">
				<input type = "submit" name = "logout" value = "Logout">
			</form>

			<h2>Automobiles</h2>
			<p>
				<ul>
					<?php
						$sql = "SELECT make, year, mileage FROM autos ORDER BY make";
						$stmt = $pdo -> query($sql);
						while($row = $stmt -> fetch(PDO::FETCH_ASSOC))
						{
							echo "<li>" . $row["year"] . " " . $row["make"] . " / " . $row["mileage"] . "</li>\n";
						}
					?>
				</ul>
			</p>

		</div>
	</body>

</html>