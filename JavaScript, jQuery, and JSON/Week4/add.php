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
        if (isset($_POST['first_name']) and isset($_POST['last_name']) and isset($_POST['email']) and isset($_POST['headline']) and isset($_POST['summary'])) {
            $firstName = $_POST['first_name'];
            $lastName = $_POST['last_name'];
            $email = $_POST['email'];
            $headline = $_POST['headline'];
            $summary = $_POST['summary'];

            if ($firstName == "" or $lastName == "" or $email == "" or $headline == "" or $summary == "") {
                $_SESSION['errors'] = "All fields are required"; 
                header("Location: add.php");
                return ;
            } else {
                if (strpos($email, '@') == false) {
                    $_SESSION['errors'] = "Email address must contain @";
                    header("Location: add.php");
                    return ;
                } else {
                    for($i=1; $i<=9; $i++) {
                        if ( ! isset($_POST['posYear'.$i]) ) continue;
                        if ( ! isset($_POST['desc'.$i]) ) continue;
                    
                        $posYear = $_POST['posYear'.$i];
                        $desc = $_POST['desc'.$i];
                    
                        if ( strlen($posYear) == 0 || strlen($desc) == 0 ) {
                            $_SESSION['errors'] = "All fields are required";
                            header("Location: add.php");
                            return ;
                        }
                    
                        if (!is_numeric($posYear) ) {
                            $_SESSION['errors'] = "Year must be numeric";
                            header("Location: add.php");
                            return ;
                        }
                    }

                    for($i=1; $i<=9; $i++) {
                        if ( ! isset($_POST['educationYear'.$i]) ) continue;
                        if ( ! isset($_POST['schoolName'.$i]) ) continue;
                    
                        $educationYear = $_POST['educationYear'.$i];
                        $schoolName = $_POST['schoolName'.$i];
                    
                        if ( strlen($educationYear) == 0 || strlen($schoolName) == 0 ) {
                            $_SESSION['errors'] = "All fields are required";
                            header("Location: add.php");
                            return ;
                        }
                    
                        if (!is_numeric($educationYear) ) {
                            $_SESSION['errors'] = "Year must be numeric";
                            header("Location: add.php");
                            return ;
                        }
                    }

                    $stmt = $pdo->prepare('INSERT INTO Profile
                                        (user_id, first_name, last_name, email, headline, summary) VALUES (:userId, :firstName, :lastName, :email, :headline, :summary)');
                    $stmt->execute(array(':userId' => $_SESSION['user_id'],
                                        ':firstName' => $firstName,
                                        ':lastName' => $lastName,
                                        ':email' => $email,
                                        ':headline' => $headline,
                                        ':summary' => $summary)
                                        );

                    $profile_id = $pdo->lastInsertId();
                    
                    $rank = 1;
                    for($i=1; $i<=9; $i++) {
                        if ( ! isset($_POST['posYear'.$i]) ) continue;
                        if ( ! isset($_POST['desc'.$i]) ) continue;
                    
                        $year = $_POST['posYear'.$i];
                        $desc = $_POST['desc'.$i];

                        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

                        $stmt->execute(array(
                        ':pid' => $profile_id,
                        ':rank' => $rank,
                        ':year' => $posYear,
                        ':desc' => $desc)
                        );

                        $rank++;
                    }
                    
                    $rank = 1;
                    for($i=1; $i<=9; $i++) {
                        if ( ! isset($_POST['educationYear'.$i]) ) continue;
                        if ( ! isset($_POST['schoolName'.$i]) ) continue;
                    
                        $educationYear = $_POST['educationYear'.$i];
                        $schoolName = $_POST['schoolName'.$i];

                        $stmt = $pdo->prepare('SELECT institution_id
                                            FROM Institution 
                                            WHERE Institution.name = :schoolName');
                        $stmt->execute(array(
                            ':schoolName' => $schoolName
                        ));
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($row) {   
                            $institution_id = $row[institution_id];
                        } else {
                            $stmt = $pdo->prepare('INSERT INTO Institution (name) 
                                                VALUES (:name)');

                            $stmt->execute(array(
                                ':name' => $schoolName)
                            );

                            $institution_id = $pdo->lastInsertId();
                        }
                        
                        $stmt = $pdo->prepare('INSERT INTO Education (profile_id, institution_id, rank, year)
                        VALUES ( :pid, :institution_id, :rank, :year)');

                        $stmt->execute(array(
                            ':pid' => $profile_id,
                            ':rank' => $rank,
                            ':year' => $educationYear,
                            ':institution_id' => $institution_id)
                        );

                        $rank++;
                    }
                    
                    $_SESSION['success'] = "Profile added.";
                    header("Location: index.php");
                    return ;
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>microease's Profile Add</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css"> 

        <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

        <script src = "https://code.jquery.com/jquery-1.10.2.js"></script>
        <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    </head>

    <body>
        <div class="container">
            <h1>Adding Profile for  
                <?php
                    echo htmlentities($_SESSION['name']);
                ?>
            </h1>

            <div style="color: red;"> 
                <?php 
                    echo $_SESSION['errors'];
                    unset($_SESSION['errors'])
                ?>    
            </div>
            
            <form method="post">
                <p>First Name:
                    <input type="text" name="first_name" size="60"/>
                </p>
                    
                <p>Last Name:
                    <input type="text" name="last_name" size="60"/>
                </p>
                
                <p>
                    Email:
                    <input type="text" name="email" size="30"/>
                </p>
                
                <p>
                    Headline:<br/>
                    <input type="text" name="headline" size="80"/>
                </p>
                
                <p>
                    Summary:<br/>
                    <textarea name="summary" rows="8" cols="80"></textarea>

                <p>
                    Education: <input type="submit" id="addEducation" value="+">

                    <div id="education_fields">
                    </div>
                </p>

                <p>
                    Position: <input type="submit" id="addPos" value="+">

                    <div id="position_fields">
                    </div>
                </p>

                <p>
                    <input type="submit" value="Add">
                    <input type="submit" name="cancel" value="Cancel">
                </p>
            </form>

            <script type="text/javascript">
                countPos = 0;

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
                            <p>Year: <input type="text" name="posYear'+countPos+'" value="" /> \
                            <input type="button" value="-" \
                                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                            </div>');
                    });

                    countEducation = 0;
                    $('#addEducation').click(function(event){
                        // http://api.jquery.com/event.preventdefault/
                        event.preventDefault();
                        if ( countEducation >= 9 ) {
                            alert("Maximum of nine education entries exceeded");
                            return;
                        }
                        countEducation++;
                        window.console && console.log("Adding Education "+countEducation);
                        $('#education_fields').append(
                            '<div id="education'+countEducation+'"> \
                            <p>Year: <input type="text" name="educationYear'+countEducation+'" value="" /> \
                            <input type="button" value="-" \
                                onclick="$(\'#education'+countEducation+'\').remove();return false;"></p> \
                            <p>School: <input type="text" size = "80" name="schoolName'+countEducation+'" class="school" value=""/> \
                            </div>');
 
                        $('.school').autocomplete({
                        source: "school.php"
                        });
                    });
                });
            </script>
        </div>
    </body>
</html>
