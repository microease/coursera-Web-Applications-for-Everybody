<?php
    session_start();
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=microease', 'microease', 'huyankai');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    header('Content-Type: application/json; charset=utf-8');
    $stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :prefix');
    $stmt->execute(array(':prefix' => $_GET['term']."%"));
    $retval = array();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $retval[] = $row['name'];
    }

    echo(json_encode($retval));
?>
