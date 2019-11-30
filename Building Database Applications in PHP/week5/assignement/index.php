<?php

require_once "pdo.php";
session_start();

?>

<!DOCTYPE html>

<html lang = "en">

	<head>
		<meta charset = "utf-8">
		<title>microease Index Page</title>
		<?php require_once "bootstrap.php" ?>
	</head>

	<body>
		<div class = "container">

			<h2>Welcome to the Automobiles Database</h2>

			<?php
				if (isset($_SESSION["error"])) 
				{
					echo('<p style = "color:red;">' . $_SESSION["error"] . "</p>\n");
					unset($_SESSION["error"]);
				}
				if (isset($_SESSION["success"])) 
				{
					echo('<p style = "color:green;">' . $_SESSION["success"] . "</p>\n");
					unset($_SESSION["success"]);
				}

				//index view 
				if(!isset($_SESSION["email"]) || !isset($_SESSION["pass"]))
				{
					echo('<p> <a href = "login.php">Please log in</a> </p>' . "\n");
					echo('<p> Attempt to <a href = "add.php">add data</a> without logging in </p>' . "\n");
				}
				else
				{

					$stmt = $pdo -> query("SELECT autos_id, make, model, year, mileage FROM autos");

					if($stmt -> rowCount() > 0)
					{
						echo('<table border = "1">' . "\n");

						echo "<tr><th>Make</th><th>Model</th><th>Year</th><th>Mileage</th><th>Action</th></tr>";

						while($row = $stmt -> fetch(PDO::FETCH_ASSOC))
						{
							echo "<tr><td>";
							echo(htmlentities($row["make"]));
							echo "</td><td>";
							echo(htmlentities($row["model"]));
							echo "</td><td>";
							echo(htmlentities($row["year"]));
							echo "</td><td>";
							echo(htmlentities($row["mileage"]));
							echo "</td><td>";
							echo('<a href = "edit.php?autos_id=' . $row["autos_id"] . '">Edit</a> / ');
							echo('<a href = "delete.php?autos_id=' . $row["autos_id"] . '">Delete</a>');
							echo "</td></tr>\n";
						}

						echo "</table>\n";
					}
					else
					{
						echo "<p>No rows found</p>";
					}

					echo('<p> <a href = "add.php">Add New Entry</a> </p>' . "\n");
					echo('<p> <a href = "logout.php">Logout</a> </p>' . "\n");
				}
			?>

		</div>
	</body>

</html>