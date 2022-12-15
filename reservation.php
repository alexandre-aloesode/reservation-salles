<?php

include 'connecSQL.php';
include 'connec.php';

$request_event = "SELECT * FROM reservations 
INNER JOIN utilisateurs ON reservations.id_utilisateur = utilisateurs.id
WHERE reservations.id = '$_GET[id]'";

$query_event = $mysqli->query($request_event);
$result_event = $query_event->fetch_all();


if(isset($_GET['delete_resa'])) {

    $request_delete_event = "DELETE FROM reservations WHERE id = '$_GET[delete_resa]'";
    $query_delete_event = $mysqli->query($request_delete_event);
    header('Location: planning.php');
}

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

                <h1><?= $result_event[0][1] ?></h1>

                <h2> Créé par:  <?= $result_event[0][7] ?> </h2>

                <h3>Description :</h3>                
                <p> <?= $result_event[0][2] ?> </p>

                <h3>Date de début :</h3>
                <p> <?= $result_event[0][3] ?> </p>

                <h3>Date de fin :</h3>
                <p> <?= $result_event[0][4] ?> </p>

                <?php if($result_event[0][5] == $_SESSION['userID']) :?> 

                    <form method="get" class="formulaire">

                    <button type="submit" id="delete_resa" name="delete_resa" value="<?= $_GET['id'] ?>" >Supprimer ma réservation</button>

                    </form>
                    
                <?php endif ?>
        

            <?php else: ?>

                <h1>Connectez-vous petit coquin!</h1>

            <?php endif ?>

        </form>
        
    </main> 

    <?php include 'footer.php' ?>
    
</body>
</html>