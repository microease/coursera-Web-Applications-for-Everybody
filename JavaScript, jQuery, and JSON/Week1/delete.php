<?php
    session_start();
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=microease', 'microease', 'huyankai');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  

    if (!isset($_SESSION['login'])) {
        die("ACCESS DENIED");
    } else {
        $stmt = $pdo->prepare('SELECT profile_id, first_name, last_name
                               FROM Profile  
                               WHERE profile_id = :profile_id');
        $stmt->execute(array(':profile_id' => $_GET['profile_id']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row == false) {
            $_SESSION['errors'] = "Could not load profile";
            header("Location: index.php");
            return ;
        }
        
        if (isset($_POST['cancel'])) {
            header("Location: index.php");
            return ;
        }

        if (isset($_POST['delete'])) {
            $stmt = $pdo->prepare('DELETE
                                   FROM Profile
                                   WHERE profile_id = :profile_id');
            $stmt->execute(array(':profile_id' => $_GET["profile_id"]));
            
            $_SESSION['success'] = "Profile deleted";  
            header("Location: index.php");
            return ;
        }
    }
?>


<html>
    <head>
        <title>
        microease's Resume Registry
        </title>

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
            <p> <h1>Deleting Profile</h1> </p>
            <p>First Name: <?php echo $row['first_name'] ?></p>
            <p>Second Name: <?php echo $row['last_name'] ?></p>
            <form method="post">
                <input type="submit" value="Delete" name="delete">
                <input type="submit" value="Cancel" name="cancel">
            </form>
        </div>
    </body>
</html>
