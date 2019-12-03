<?php
    session_start(); 

    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=microease', 'microease', 'huyankai');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  

    if (isset($_POST['cancel'])) {
        header("Location: index.php");
        return ;
    }

    if (isset($_POST['email']) and isset($_POST['pass'])) {
        $user = $_POST['email'];
        $pass = $_POST['pass'];
        $salt = "XyZzy12*_";

        $check = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name 
                               FROM users 
                               WHERE email = :em AND password = :pw');
        $stmt->execute(array(':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row == false) {
            $_SESSION["errors"] = "Incorrect Password \n";
            error_log("Login fail " .$_POST['name']. $actualHash);
            header("Location: login.php");
            return ;
        } else {
            error_log("Login success ".$user);
            $_SESSION['name'] = $row['name'];
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $row['user_id'];
            header("Location: index.php");
            return ; 
        }
    } 
?>

<!DOCTYPE html>
<html>
    <head>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

        <!-- Custom styles for this template -->
        <link href="starter-template.css" rel="stylesheet">

        <title>microease's Login Page</title>
    </head>

    <body>
        <div class="container">
            <h1>Please Log In</h1>

            <div style="color: red;"> 
                <?php 
                    echo $_SESSION["errors"];
                    unset($_SESSION["errors"]);
                ?>    
            </div>

            <form method="POST">
                <label for="email">Email</label>
                <input type="text" name="email" id="email"/><br/>
                <label for="id_1723">Password</label>
                <input type="password" name="pass" id="id_1723"/><br/>
                <input type="submit" onclick="return doValidate();" value="Log In">
                <input type="submit" name = "cancel" value="Cancel">
            </form>

            <p>
                For a password hint, view source and find a password hint
                in the HTML comments.
                <!-- Hint: The password is the three character name of the 
                programming language used in this class (all lower case) 
                followed by 123. -->
            </p>

            <script>
                function doValidate() {
                    console.log('Validating...');
                    try {
                        addr = document.getElementById('email').value;
                        pw = document.getElementById('id_1723').value;
                        console.log("Validating addr="+addr+" pw="+pw);
                        if (addr == null || addr == "" || pw == null || pw == "") {
                            alert("Both fields must be filled out");
                            return false;
                        }
                        if ( addr.indexOf('@') == -1 ) {
                            alert("Invalid email address");
                            return false;
                        }
                        return true;
                    } catch(e) {
                        return false;
                    }
                    return false;
                }
            </script>

        </div>
    </body>
</html> 
