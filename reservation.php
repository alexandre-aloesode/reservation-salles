<?php

include 'connecSQL.php';
include 'connec.php';

// Ci_dessous mes 2 requêtes pour afficher les détails de la résa si l'utilisateur est connecté, et pour la supprimer si la résa appartient à l'utilisateur connecté.
if(isset($_GET['id'])) {

    $_SESSION['commentID'] = $_GET['id'];

    $request_event = "SELECT * FROM reservations 
    INNER JOIN utilisateurs ON reservations.id_utilisateur = utilisateurs.id
    WHERE reservations.id = '$_GET[id]'";

    $query_event = $mysqli->query($request_event);
    $result_event = $query_event->fetch_all();
}
    
if(isset($_GET['delete_resa'])) {

        $request_delete_event = "DELETE FROM reservations WHERE id = '$_SESSION[commentID]'";
        $query_delete_event = $mysqli->query($request_delete_event);
        header('Location: planning.php');
}

//Ci-dessous mes 2 requêtes pour récupérer les infos de la résa existantes, puis pour la modifier
if(isset($_GET['modify_resa'])) {

    $request_event = "SELECT * FROM reservations WHERE id = '$_SESSION[commentID]'";
    $query_event = $mysqli->query($request_event);
    $result_event = $query_event->fetch_all();

    $date_min = new DateTime('today');

    $date_de_debut= $result_event[0][3];
    $date_debut_modify = new DateTime(($date_de_debut));

    $date_de_fin= $result_event[0][4];
    $date_fin_modify = new DateTime(($date_de_fin));
}

if(isset($_POST['modify'])) {
        
    $check_modify = 1;
    
    if(empty($_POST['titre']) || empty($_POST['desc']) || empty($_POST['start_time']) || empty($_POST['end_time']) 
    || empty($_POST['date_debut_event']) || empty($_POST['date_fin_event'])) {
        $check_modify = 0;
        $message = "Veuillez remplir tous les champs";
    }

    if($check_modify == 1){
        $date_debut = new DateTime($_POST['date_debut_event']);
        $date_fin = new DateTime($_POST['date_fin_event']);
        if($date_debut->format('D') == 'Sat' || $date_debut->format('D') == 'Sun' || $date_fin->format('D') == 'Sat' || $date_fin->format('D') == 'Sun') {
            $check_modify = 0;
            $message = "Nous sommes fermés les week-ends";
        }
    }

    if($check_modify == 1) {

        $date_debut = $_POST['date_debut_event'] . ' ' . $_POST['start_time'];      
        $date_fin = $_POST['date_fin_event'] . ' ' . $_POST['end_time'];

        if($date_debut >= $date_fin) {
        $check_modify = 0;
        $message = "Erreur dans les créneaux horaires";
        }

        if($check_modify == 1) {

        $request_date_events = "SELECT debut, fin FROM reservations WHERE debut BETWEEN '$date_debut' and '$date_fin' OR fin BETWEEN '$date_debut' and '$date_fin'
        EXCEPT SELECT debut, fin FROM reservations WHERE id = '$_SESSION[commentID]'";
        $query_date_events = $mysqli->query($request_date_events);
        $result_date_events = $query_date_events->fetch_all(); 

            for($x = 0; isset($result_date_events[$x]); $x++){

                $heure_debut = str_split($result_date_events[$x][0], 10);
                $heure_debut = $heure_debut[1];
                $check_heure_debut = new DateTime($heure_debut);

                $heure_fin = str_split($result_date_events[$x][1], 10);
                $heure_fin = $heure_fin[1];
                $check_heure_fin = new DateTime($heure_fin);

                if($check_heure_debut->format('H') == $_POST['start_time'] || $check_heure_fin->format('H') == $_POST['end_time']) {
                    $check_modify = 0;
                    $message = 'Cette plage horaire est déjà réservée';
                    break;
                }

                if($check_modify == 1) {
                    for($i = $_POST['start_time']; $i < $_POST['end_time']; $i++) {
                        if($i == $check_heure_debut->format('H') || $i == ($check_heure_fin->format('H') - 1)) {
                            $check_modify = 0;
                            $message = 'Votre réservation se chevauche sur une autre';
                            break;
                        }
                    }
                }        
            }
        }

        if($check_modify == 1) {
        
            $titre = mysqli_real_escape_string($mysqli, $_POST['titre']);
            $desc = mysqli_real_escape_string($mysqli, $_POST['desc']);
        
            $request_modify_event = "UPDATE reservations SET titre = '$titre', description =  '$desc', debut = '$date_debut' , fin = '$date_fin' WHERE id = '$_SESSION[commentID]'";
            
            $query_modify_event = $mysqli->query($request_modify_event);
            
            if($query_modify_event == true){
                $message = 'modification effectuée';
            }
        }
    }
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

<!-- Ci-dessous j'affiche le formulaire avec les infos de la résa. Accessible seulement si le visiteur est connecté. -->

            <?php if(isset($_SESSION['userID']) && isset($_GET['id'])): ?>
        <form class ="formulaire">
                <h1><?= $result_event[0][1] ?></h1>

                <h2> Créé par :  <?= $result_event[0][7] ?> </h2>

                <h3>Description :</h3>                
                <p> <?= $result_event[0][2] ?> </p>

                <h3>Date de début :</h3>
                <p> <?= $result_event[0][3] ?> </p>

                <h3>Date de fin :</h3>
                <p> <?= $result_event[0][4] ?> </p>

<!-- Si la résa appartient à l'utilisateur connecté, j'affiche ci-dessous 2 boutons: supprimer et modifier. -->
                <?php if($result_event[0][5] == $_SESSION['userID']) :?> 

                <form method="get" class="formulaire">

                    <button type="submit" id="modify_resa" name="modify_resa" value="<?= $_GET['id'] ?>">Modifier</button>     

                    <button type="submit" id="delete_resa" name="delete_resa" value="<?= $_GET['id'] ?>">Supprimer</button>           
                    
                <?php endif ?>
            
                </form>
        </form>
<!-- Si l'utilisateur a cliqué pour modifier sa résa, j'affiche ci-dessous le form de modification en affichant les détails actuels de la résa -->

            <?php elseif(isset($_GET['modify_resa'])): ?>

        <form method="post" class ="formulaire">

            <h2> <?= 'Modifier mon évènement' ?> </h2>

            <h3>
                <?php 
                    if(isset($_POST['modify'])) { 
                        echo $message;
                    }
                ?>
            </h3>
                    
            <p>Utilisateur : <?php if(isset($_SESSION['user'])) echo $_SESSION['user'] ?> </p>
            <br>

            <label for="titre">Titre :</label>
            <textarea name="titre"> <?= $result_event[0][1] ?> </textarea>
            <br>
                
            <label for="date_debut_event">Date de début:</label>
             

            <input type="date" name="date_debut_event" value="<?= $date_debut_modify->format('Y-m-d') ?>" 
            min="<?= $date_min->format('Y-m-d') ?>">
            <br>

            <label for="start_time">Heure de début :</label>
            <select name="start_time">

                <?php for($x = 8; $x < 19; $x++) {

                    if($x < 10) {
                        if($x == $date_debut_modify->format('H')) {
                            echo '<option selected>0' . $x . '</option>';
                        }
                        else {
                            echo '<option>0' . $x . '</option>';
                        }
                    }

                    else {
                        if($x == $date_debut_modify->format('H')) {
                            echo '<option selected>' . $x . '</option>';
                        }
                        else {
                            echo '<option>' . $x . '</option>';
                        }
                    }
                }
                    ?>
            </select>
            <br>
                
            <label for="date_fin_event">Date de fin:</label>
            <input type="date" name="date_fin_event" value="<?= $date_fin_modify->format('Y-m-d') ?>" 
            min="<?= $date_min->format('Y-m-d') ?>">
            <br>

            <label for="end_time">Heure de fin :</label>
            <select name="end_time">

                <?php for($x = 9; $x < 20; $x++) {

                    if($x < 10) {
                        if($x == $date_fin_modify->format('H')){
                            echo '<option selected>0' . $x . '</option>';
                        }
                        else{
                            echo '<option>0' . $x . '</option>';
                        }                         
                    }

                    else {
                        if($x == $date_fin_modify->format('H')){
                            echo '<option selected>' . $x . '</option>';
                        }
                        else {
                            echo '<option>' . $x . '</option>';
                        }
                    }
                }
                ?>
            </select>
            <br>
           
            <label for="desc">Description : </label>
            <textarea name="desc"> <?= $result_event[0][2] ?> </textarea>
            <br>

            <button type="submit" name="modify">Modifier</button>
        </form>  

        <?php else: ?>

            <h1>Connecte-toi petit coquin!</h1>

        <?php endif ?>
        
    </main> 

    <?php include 'footer.php' ?>
    
</body>
</html>