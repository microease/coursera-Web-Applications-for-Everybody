<?php 
    session_start();
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=microease', 'microease', 'huyankai');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  

    $stmt = $pdo->prepare('SELECT profile_id, first_name, last_name, email, headline, summary
                        FROM Profile  
                        WHERE profile_id = :profile_id');
    $stmt->execute(array(':profile_id' => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row == false) {
        $_SESSION['errors'] = "Could not load profile";
        header("Location: index.php");
        return ;
    }

    $stmt_position = $pdo->prepare('SELECT profile_id, year, description
                                    FROM Position  
                                    WHERE profile_id = :profile_id');
    $stmt_position->execute(array(':profile_id' => $row['profile_id']));
    $rows_position = $stmt_position->fetchAll(PDO::FETCH_ASSOC);

    $stmt_Institution_Education = $pdo->prepare('SELECT Institution.name, Education.year
                                                 FROM Education, Institution 
                                                 WHERE Education.profile_id = :profile_id AND
                                                       Education.institution_id = Institution.institution_id');
    $stmt_Institution_Education->execute(array(':profile_id' => $row['profile_id']));
    $rows_Institution_Education = $stmt_Institution_Education->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
    <head>
    <title>microease's Profile View</title>
    <!-- bootstrap.php - this is HTML -->

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" 
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
        integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
        crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" 
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
        integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
        crossorigin="anonymous">
    </head>

    <body>
        <div class="container">
            <h1>Profile information</h1>
            <p>
                First Name: <?php echo $row['first_name']; ?>
            </p>

            <p>
                Last Name: <?php echo $row['last_name']; ?>
            </p>

            <p>
                Email: <?php echo $row['email']; ?>
            </p>
            
            <p>
                Headline: <br/>
                <?php echo $row['headline']; ?>
            </p>

            <p>
                Summary:<br/>
                <?php echo $row['summary']; ?>
            </p>
            
            <p>
                <?php 
                    if ($rows_Institution_Education) {
                        echo "Education";
                     } 
                ?>
            </p>
            
            <p> 
                <ul>
                    <?php
                        foreach ($rows_Institution_Education as $row_Institution_Education) {
                            echo "<li>" . $row_Institution_Education['year'] . ": " . $row_Institution_Education['name'] . "</li> \n";
                        }
                    ?>
                </ul>
            </p>

            <p>
                <?php 
                    if ($rows_position) {
                        echo "Position";
                     } 
                ?>
            </p>
            
            <p> 
                <ul>
                    <?php
                        foreach ($rows_position as $row_position) {
                            echo "<li>" . $row_position['year'] . ": " . $row_position['description'] . "</li> \n";
                        }
                    ?>
                </ul>
            </p>

            <a href="index.php">Done</a>
        </div>

        <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    </body>
</html>

