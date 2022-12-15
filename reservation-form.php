<?php 

include 'connecSQL.php';
include 'connec.php';

//Ci-dessous mes vérifications si l'utilisateur envoie sa demande. La variable check_add reste à 1 si toutes les conditions sont validées. 
//Dès qu'une condition ne l'est pas la variable = 0 et donc toutes les conditions qui restent s'annulent.

    if(isset($_POST['reservation'])) {
        
        $check_add = 1;

//Vérification si tous les champs sont remplis       
        if(empty($_POST['titre']) || empty($_POST['desc']) || empty($_POST['start_time']) || empty($_POST['end_time']) 
        || empty($_POST['date_debut_event']) || empty($_POST['date_fin_event'])) {
            $check_add = 0;
            $message = "Veuillez remplir tous les champs";
        }

//Vérification si l'utilisateur tente de réserver un samedi ou dimanche
        if($check_add == 1){
            $date_debut = new DateTime($_POST['date_debut_event']);
            $date_fin = new DateTime($_POST['date_fin_event']);
            if($date_debut->format('D') == 'Sat' || $date_debut->format('D') == 'Sun' || $date_fin->format('D') == 'Sat' || $date_fin->format('D') == 'Sun') {
                $check_add = 0;
                $message = "Nous sommes fermés les week-ends";
            }
        }

        if($check_add == 1) {

            $date_debut = $_POST['date_debut_event'] . ' ' . $_POST['start_time'];      
            $date_fin = $_POST['date_fin_event'] . ' ' . $_POST['end_time'];

            if($date_debut >= $date_fin) {
            $check_add = 0;
            $message = "Erreur dans les créneaux horaires";
            }

            if($check_add == 1) {
 
            $request_date_events = "SELECT debut, fin FROM reservations WHERE debut BETWEEN '$date_debut' and '$date_fin' OR  fin BETWEEN '$date_debut' and '$date_fin'";
            $query_date_events = $mysqli->query($request_date_events);
            $result_date_events = $query_date_events->fetch_all(); 

//Je récupère les dates des évènement déjà programmés à la date de début que l'utilisateur a sélectionnée. J'utilise ensuite DateTime pour vérifier 
//si les créneaux sélectionnées sont déjà pris, grâce à la boucle for du dessous.

                for($x = 0; isset($result_date_events[$x]); $x++){

                    $heure_debut = str_split($result_date_events[$x][0], 10);
                    $heure_debut = $heure_debut[1];
                    $check_heure_debut = new DateTime($heure_debut);

                    $heure_fin = str_split($result_date_events[$x][1], 10);
                    $heure_fin = $heure_fin[1];
                    $check_heure_fin = new DateTime($heure_fin);

                    if($check_heure_debut->format('H') == $_POST['start_time'] || $check_heure_fin->format('H') == $_POST['end_time']) {
                        $check_add = 0;
                        $message = 'Cette plage horaire est déjà réservée';
                        break;
                    }

//On a aussi besoin de vérifier qu'une réservation ne se chevauche pas avec un créneau déjà pris. Je crée donc une variable i qui va de l'heure' de début à l'
// heure de fin de l'event de l'utilisateur. Si sur l'intervalle, i matche avec un évènement de ma table, c'est que l'event de l'utilisateur va chevaucher un event 
// déjà prévu. J'enlève 1 à l'heure'de fin car si par exemple on veut programmer un event de 15 à 16 et qu'il y en a déjà un de 16 à 17, ma boucle est erronnée.
                    if($check_add == 1) {
                        for($i = $_POST['start_time']; $i < $_POST['end_time']; $i++) {
                            if($i == $check_heure_debut->format('H') || $i == ($check_heure_fin->format('H') - 1)) {
                                $check_add = 0;
                                $message = 'Votre réservation se chevauche sur une autre';
                                break;
                            }
                        }
                    }        
                }
            }

//Finalement si toutes les conditions sont réunies et $check_add est resté == 1, je crée mon event.      
            if($check_add == 1) {
            
                $titre = mysqli_real_escape_string($mysqli, $_POST['titre']);
                $desc = mysqli_real_escape_string($mysqli, $_POST['desc']);
            
                $request_add_event = "INSERT INTO reservations (titre, description, debut, fin, id_utilisateur) 
                VALUES ('$titre', '$desc', '$date_debut', '$date_fin', '$_SESSION[userID]')";
                
                $query_add_event = $mysqli->query($request_add_event);

                if($query_add_event == true){
                    $message = 'réservation réussie';
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

            <h2>
                <?php echo 'Formulaire de réservation' ?> 
            </h2>

            <h3>
                <?php 
                    if(isset($_POST['reservation'])) { 
                        echo $message;
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
                <input type="date" name="date_debut_event" value="<?php if(isset($_GET['date'])) echo $_GET['date']?>" 
                min="<?php echo $date_min->format('Y-m-d') ?>">
                <br>

                <label for="start_time">Heure de début :</label>
                <select name="start_time" value="15">

                    <?php for($x = 8; $x < 19; $x++) {

                        if($x < 10) {
                            if(isset($_GET['heure']) && $x == $_GET['heure']){
                                echo '<option selected>0' . $x . '</option>';
                            }
                            else{
                                echo '<option>0' . $x . '</option>';
                            }
                        }

                        else {
                            if(isset($_GET['heure']) && $x == $_GET['heure']){
                                echo '<option selected>' . $x . '</option>';
                            }
                            else{
                                echo '<option>' . $x . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
                <br>
                
                <label for="date_fin_event">Date de fin:</label>
                <input type="date" name="date_fin_event" value="<?php if(isset($_GET['date'])) echo $_GET['date']?>"
                min="<?php echo $date_min->format('Y-m-d') ?>">
                <br>

                <label for="end_time">Heure de fin :</label>
                <select name="end_time">

                    <?php for($x = 9; $x < 20; $x++) {

                        if($x < 10) {
                            if($x == $_GET['heure']){
                                echo '<option selected>0' . $x . '</option>';
                            }
                            else{
                                echo '<option>0' . $x . '</option>';
                            }
                            
                        }
                        else {
                            if($x == $_GET['heure']){
                                $x++;
                                echo '<option selected>' . $x . '</option>';
                            }
                            else{
                                echo '<option>' . $x . '</option>';
                            }
                        }
                    }
                    ?>
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