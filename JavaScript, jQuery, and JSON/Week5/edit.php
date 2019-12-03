<?php
    session_start();
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=microease', 'microease', 'huyankai');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  

    if (!isset($_SESSION['login'])) {
        die("ACCESS DENIED");
    } else if (isset($_POST['cancel'])) {
        header('Location: index.php');
        return ;
    } else {
        $stmt = $pdo->prepare('SELECT * 
                               FROM Profile  
                               WHERE profile_id = :profile_id');
        $stmt->execute(array(':profile_id' => $_GET['profile_id']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row == false) {
            $_SESSION['errors'] = "Could not load profile";
            header("Location: index.php");
            return ;
        } else {
            if (isset($_POST['first_name']) and isset($_POST['last_name']) and isset($_POST['email']) and isset($_POST['headline']) and isset($_POST['summary'])) {
                $firstName = $_POST['first_name'];
                $lastName = $_POST['last_name'];
                $email = $_POST['email'];
                $headline = $_POST['headline'];
                $summary = $_POST['summary'];

                if ($firstName == "" or $lastName == "" or $email == "" or $headline == "" or $summary == "") {
                    $_SESSION['errors'] = "All fields are required"; 
                    header("Location: edit.php?profile_id=" . $_GET['profile_id']);
                    return ;
                } else {                
                    if (strpos($email, '@') == false) {
                        $_SESSION['errors'] = "Email address must contain @";
                        header("Location: edit.php?profile_id=" . $_GET['profile_id']);
                        return ;
                    } else {
                        for($i=1; $i<=9; $i++) {
                            if ( ! isset($_POST['year'.$i]) ) continue;
                            if ( ! isset($_POST['desc'.$i]) ) continue;
                        
                            $year = $_POST['year'.$i];
                            $desc = $_POST['desc'.$i];
                        
                            if ( strlen($year) == 0 || strlen($desc) == 0 ) {
                                $_SESSION['errors'] = "All fields are required";
                                header("Location: edit.php?profile_id=" . $_GET['profile_id']);
                                return ;
                            }
                        
                            if (!is_numeric($year) ) {
                                $_SESSION['errors'] = "Year must be numeric";
                                header("Location: edit.php?profile_id=" . $_GET['profile_id']);
                                return ;
                            }
                          }

                        $stmt = $pdo->prepare('UPDATE Profile
                                               SET first_name = :firstName, last_name = :lastName, email = :email, headline = :headline, summary = :summary
                                               WHERE profile_id = :profile_id');
                        $stmt->execute(array(':firstName' => $firstName,
                                            ':lastName' => $lastName,
                                            ':email' => $email,
                                            ':headline' => $headline,
                                            ':summary' => $summary,
                                            'profile_id' => $_GET['profile_id'])
                                        );
                        
                        $stmt = $pdo->prepare('DELETE 
                                               FROM Position
                                               WHERE profile_id = :profile_id');
                        $stmt->execute(array(
                            ':profile_id' => $_GET['profile_id']));

                        $rank = 1;
                        for($i=1; $i<=9; $i++) {
                            if ( ! isset($_POST['year'.$i]) ) continue;
                            if ( ! isset($_POST['desc'.$i]) ) continue;
                        
                            $year = $_POST['year'.$i];
                            $desc = $_POST['desc'.$i];

                            $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

                            $stmt->execute(array(
                                ':pid' => $_GET['profile_id'],
                                ':rank' => $rank,
                                ':year' => $year,
                                ':desc' => $desc)
                                );
        
                            $rank++;
                        }
                        
                        $_SESSION['success'] = "Profile updated";
                        header("Location: index.php");
                        return ;
                    }
                }
            }
        }

        $stmt_retrieve = $pdo->prepare('SELECT users.name
                                        FROM users, Profile
                                        WHERE Profile.profile_id = :profile_id AND 
                                              Profile.user_id = users.user_id');
        $stmt_retrieve->execute(array(':profile_id' => $_GET['profile_id']));
        $row_retrieve = $stmt_retrieve->fetch(PDO::FETCH_ASSOC);

        $stmt_position = $pdo->prepare('SELECT *
                                        FROM Position  
                                        WHERE profile_id = :profile_id');
        $stmt_position->execute(array(':profile_id' => $row['profile_id']));
        $rows_position = $stmt_position->fetchAll(PDO::FETCH_ASSOC);

        $totalProfiles = 0;
        foreach ($rows_position as $row_position) {
            $totalProfiles = $totalProfiles + 1;
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>microease's Profile Edit</title>

        <link rel="stylesheet" 
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
            integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
            crossorigin="anonymous">

        <link rel="stylesheet" 
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
            integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
            crossorigin="anonymous">

        <link rel="stylesheet" 
            href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

        <script
        src="https://code.jquery.com/jquery-3.2.1.js"
        integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
        crossorigin="anonymous"></script>

        <script
        src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
        integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
        crossorigin="anonymous"></script>
    </head>

    <body>
        <div class="container">
            <h1>Editing Profile for  
                <?php
                    $name = htmlentities($row_retrieve['name']); 
                    echo($name); 
                ?> 
            </h1>

            <div style="color: red;"> 
                <?php 
                    echo $_SESSION['errors'];
                    unset($_SESSION['errors']);
                ?>    
            </div>
            
            <form method="post">
                <p>First Name:
                    <input type="text" name="first_name" size="60" value = "<?php echo($row['first_name']); ?>" />
                </p>
                    
                <p>Last Name:
                    <input type="text" name="last_name" size="60" value = "<?php echo($row['last_name']); ?>" />
                </p>
                
                <p>
                    Email:
                    <input type="text" name="email" size="30" value = "<?php echo($row['email']); ?>" />
                </p>
                
                <p>
                    Headline:<br/>
                    <input type="text" name="headline" size="80" value = "<?php echo($row['headline']); ?>" />
                </p>
                
                <p>
                    Summary:<br/>
                    <textarea name="summary" rows="8" cols="80"><?php echo($row['summary']); ?></textarea>

                <p>
                    Position: <input type="submit" id="addPos" value="+">

                    <div id="position_fields">
                        <?php 
                            foreach ($rows_position as $row_position) {
                                $rank = $row_position['rank'];
                                $year = $row_position['year'];
                                $desc = $row_position['description'];

                                echo '<div id="position' . $rank . '">.'; 
                                echo '<p>Year: <input type="text" name="year' . $rank . '" value="' . $year . '" />';
                                echo '<input type="button" value="-" onclick="$(' . "'#position" . $rank . "').remove();return false;" . '">';
                                echo "</p>";
                                echo '<textarea name="desc' . $rank . '" rows="8" cols="80">';
                                echo $desc;
                                echo "</textarea>";
                                echo "</div>";
                            }
                        ?>
                    </div>
                </p>

                <p>
                    <input type="submit" value="Save">
                    <input type="submit" name="cancel" value="Cancel">
                </p>
            </form>

            <script>
                countPos = <?php echo $totalProfiles ?>;

                $(document).ready(function(){
                    window.console && console.log('Document ready called');
                    $('#addPos').click(function(event){
                        // http://api.jquery.com/event.preventdefault/
                        event.preventDefault();
                        if ( countPos >= 9 ) {
                            alert("Maximum of nine position entries exceeded");
                            return;
                        }
                        countPos++;
                        window.console && console.log("Adding position "+countPos);
                        $('#position_fields').append(
                            '<div id="position'+countPos+'"> \
                            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                            <input type="button" value="-" \
                                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                            </div>');
                    });
                });
            </script>
        </div>
        
        <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    </body>
</html>

