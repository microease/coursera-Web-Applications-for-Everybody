<?php

session_start();

if(!isset($_SESSION["email"]) || strlen($_SESSION["email"]) < 1)
	die("Not logged in");
?>

<!DOCTYPE html>

<html lang = "en">

	<head>
		<meta charset = "utf-8">
		<title>microease Automobile Tracker</title>
		<?php require_once "bootstrap.php"; require_once "pdo.php"; ?>
	</head>

	<body>
		<div class = "container">
			<h1>Tracking Autos for <?php echo(htmlentities($_SESSION["email"])) ?></h1>
			<?php
				if(isset($_SESSION["success"]))
				{
					echo('<p style = "color: green;">' . htmlentities($_SESSION["success"]) . "</p>\n");
					unset($_SESSION["success"]);
				}
			?>
			<h2>Automobiles</h2>
			<ul>
				<?php
					$sql = "SELECT make, year, mileage FROM autos ORDER BY make";
					$stmt = $pdo -> query($sql);
					while($row = $stmt -> fetch(PDO::FETCH_ASSOC))
						echo("<li>" . $row["year"] . " " . $row["make"] . " / " . $row["mileage"] . "</li>\n");
				?>
			</ul>
			<p>
				<a href = "add.php">Add New</a> | <a href = "logout.php">Logout</a>
			</p>
		</div>
	</body>

</html>