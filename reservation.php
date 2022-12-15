<?php

include 'connecSQL.php';
include 'connec.php';

$request_event = "SELECT *
FROM reservations 
INNER JOIN utilisateurs ON reservations.id_utilisateur = utilisateurs.id
WHERE reservations.id = '$_GET[id]'";

$query_event = $mysqli->query($request_event);
$result_event = $query_event->fetch_all();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" 
    integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" 
    crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="formulaires.css" rel = "stylesheet">
    <link href="index.css" rel = "stylesheet">
    <link href="header.css" rel = "stylesheet">
    <link href="footer.css" rel = "stylesheet">
    <title>Inscription</title>
</head>
<body>

    <?php include 'header.php' ?>

    <main>
        <form class ="formulaire">
            <?php if(isset($_SESSION['userID'])): ?>
                <h1><?php echo $result_event[0][1] ?></h1>
                <h2> Créé par:  <?php echo $result_event[0][7] ?> </h2>
                <h3>Description :</h3>
                <p> <?php echo $result_event[0][2] ?> </p>
                <h3>Date de début :</h3>
                <p> <?php echo $result_event[0][3] ?> </p>
                <h3>Date de fin :</h3>
                <p> <?php echo $result_event[0][4] ?> </p>

            <?php else: ?>
                <h1>Connecte toi petit coquin!</h1>
            <?php endif ?>
        </form>
    </main> 

    <?php include 'footer.php' ?>
    
</body>
</html>