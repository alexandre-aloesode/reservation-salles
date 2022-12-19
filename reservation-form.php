<?php 

include 'connecSQL.php';
include 'connec.php';
include 'functions.php';

//Ci-dessous mes vérifications si l'utilisateur envoie sa demande. La variable check_add reste à 1 si toutes les conditions sont validées. 
//Dès qu'une condition ne l'est pas la variable = 0 et donc toutes les conditions qui restent s'annulent.

        
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="index.css" rel = "stylesheet">
    <link href="header.css" rel = "stylesheet">
    <link href="footer.css" rel = "stylesheet">
    <link href="formulaires.css" rel = "stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" 
    integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" 
    crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <title>Réservation</title>
</head>

<body>

    <?php include 'header.php' ?>

    <main> 

        <form method="post" class ="formulaire">

            <h2>Formulaire de réservation</h2>

            <h3>
                <?php 
                    if(isset($_POST['reservation'])) { 
                        echo add_modify_event();
                    }
                ?>
            </h3>

            <?php if(isset($_SESSION['userID'])): ?>

                <p>Utilisateur : <?php if(isset($_SESSION['user'])) echo $_SESSION['user'] ?> </p>
                <br>

                <label for="titre">Titre :</label>
                <textarea name="titre"></textarea>
                <br>
                
                <label for="date_debut_event">Date de début:</label>
                <?php $date_min = new DateTime('today')?>
<!-- J'ai besoin de générer la date du jour pour que dans le calendrier l'utilisateur ne puisse pas sélectionner une date antèrieure au jour présent. 
Je mets donc en min de mon input ci-dessous ma variable. -->
                <input type="date" name="date_debut_event" value="<?php if(isset($_GET['date'])) echo $_GET['date']?>" min="<?= $date_min->format('Y-m-d') ?>">
                <br>

                <label for="start_time">Heure de début :</label>
                <select name="start_time">
                    <?php horaires(8, 18, $_GET['heure_debut']) ?>
                </select>
                <br>
                
                <label for="date_fin_event">Date de fin:</label>
                <input type="date" name="date_fin_event" value="<?php if(isset($_GET['date'])) echo $_GET['date']?>" min="<?= $date_min->format('Y-m-d') ?>">
                <br>

                <label for="end_time">Heure de fin :</label>
                <select name="end_time">
                    <?php horaires(9, 19, $_GET['heure_fin']) ?>
                </select>
                <br>
           
                <label for="desc">Description : </label>
                <textarea name="desc"></textarea>
                <br>

                <button type="submit" name="reservation">Soumettre</button>
        </form>
            
                <?php else: ?>

                    <p>Connectez-vous pour faire une réservation</p>

                <?php endif; ?>
    </main>

    <?php include 'footer.php' ?>

</body>
</html>