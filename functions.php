<?php

    function horaires($debut, $fin, $compare) {


            for($x = $debut ; $x <= $fin; $x++) {

                if($x < 10) {

                    if($x == $compare) {
                        echo '<option selected>' . $compare . '</option>';
                    }
                    else {
                        echo '<option>0' . $x . '</option>';
                    }                          
                }

                else {

                    if($x == $compare) {
                        echo '<option selected>' . $compare . '</option>';
                    }
                    else {
                        echo '<option>' . $x . '</option>';
                    }
                }
            }
    }


function add_modify_event() {

    include 'connecSQL.php';
        
    $check = 1;
    $message = '';
    
    //Vérification si tous les champs sont remplis       
    if(empty($_POST['titre']) || empty($_POST['desc']) || empty($_POST['start_time']) || empty($_POST['end_time']) 
    || empty($_POST['date_debut_event']) || empty($_POST['date_fin_event']) || trim($_POST['titre']) == '') {
    
        $check = 0;
        $message = "Veuillez remplir tous les champs";
    }
    
    //Vérification si l'utilisateur tente de réserver un samedi ou dimanche
    if($check == 1) {
        
        $date_debut = new DateTime($_POST['date_debut_event']);
        $date_fin = new DateTime($_POST['date_fin_event']);

        if($date_debut->format('D') == 'Sat' || $date_debut->format('D') == 'Sun' || $date_fin->format('D') == 'Sat' || $date_fin->format('D') == 'Sun') {
            $check = 0;
            $message = 'Nous sommes fermés les week-ends';
        }
    }
    
    if($check == 1) {
    
        $date_debut = $_POST['date_debut_event'] . ' ' . $_POST['start_time'];      
        $date_fin = $_POST['date_fin_event'] . ' ' . $_POST['end_time'];
    }
        
    if($date_debut >= $date_fin) {       
        $check = 0;
        $message = 'Erreur dans les créneaux horaires';
    }
            
    if($check == 1) {
            
        $request_date_events = "SELECT id, debut, fin FROM reservations WHERE debut BETWEEN '$date_debut' and '$date_fin' OR fin BETWEEN '$date_debut' and '$date_fin'";
        $query_date_events = $mysqli->query($request_date_events);
        $result_date_events = $query_date_events->fetch_all(); 
    
//Je récupère les dates des évènement déjà programmés aux dates de début et de fin que l'utilisateur a sélectionnées. J'utilise ensuite DateTime pour vérifier 
//si les créneaux sélectionnées sont déjà pris, grâce à la boucle for du dessous.
    
            for($x = 0; isset($result_date_events[$x]); $x++){
    
                $date_debut_SQL = new DateTime($result_date_events[$x][1]);
                $date_fin_SQL = new DateTime($result_date_events[$x][2]);
    
//On a aussi besoin de vérifier qu'une réservation ne se chevauche pas avec un créneau déjà pris. Je crée donc une variable i qui va de l'heure' de début à l'
// heure de fin de l'event de l'utilisateur. Si sur l'intervalle, i matche avec un évènement de ma table, c'est que l'event de l'utilisateur va chevaucher un event 
// déjà prévu. J'enlève 1 à l'heure'de fin car si par exemple on veut programmer un event de 15 à 16 et qu'il y en a déjà un de 16 à 17, ma boucle est erronnée.
//On sépare les cas de création et de modification d'event car pour la modif, on a besoin d'exclure de la requête la réservation en question.

                if(isset($_POST['reservation'])) {  
                    if($_POST['end_time'] > $_POST['start_time']) {
    
                        for($i = $_POST['start_time']; $i < $_POST['end_time']; $i++) {
                            if($i == $date_debut_SQL->format('H') || $i == ($date_fin_SQL->format('H') - 1)) {
                                $check = 0;
                                $message = 'Votre réservation se chevauche sur une autre';
                                break;
                            }
                        }
                    }  
    
                    if($date_debut_SQL->format('H') == $_POST['start_time'] || $date_fin_SQL->format('H') == $_POST['end_time']) {
                        $check = 0;
                        $message = 'Cette plage horaire est déjà réservée';
                        break;
                    }  
    
                }
    
                elseif(isset($_POST['modify'])) {  
                    if($_POST['end_time'] > $_POST['start_time']) {
    
                        for($i = $_POST['start_time']; $i < $_POST['end_time']; $i++) {
                            if($i == $date_debut_SQL->format('H') || $i == ($date_fin_SQL->format('H') - 1)) {
                                if($result_date_events[$x][0] !== $_SESSION['eventID']) {
                                    $check = 0;
                                    $message = 'Votre réservation se chevauche sur une autre';
                                    break;
                                }
                            }
                        }
                    }  
    
                    if($date_debut_SQL->format('H') == $_POST['start_time'] || $date_fin_SQL->format('H') == $_POST['end_time']) {
                        if($result_date_events[$x][0] !== $_SESSION['eventID']) {
                            $check = 0;
                            $message = 'Cette plage horaire est déjà réservée';
                            break;
                        }
                    }  
                }
            }
        }
    
 //Finalement si toutes les conditions sont réunies et $check est resté == 1, je crée/modifie mon event.      
        
    if($check == 1) {
    
            if(isset($_POST['reservation'])) {
        
                $titre = mysqli_real_escape_string($mysqli, $_POST['titre']);
                $desc = mysqli_real_escape_string($mysqli, $_POST['desc']);
            
                $request_add_event = "INSERT INTO reservations (titre, description, debut, fin, id_utilisateur) 
                VALUES ('$titre', '$desc', '$date_debut', '$date_fin', '$_SESSION[userID]')";
                
                $query_add_event = $mysqli->query($request_add_event);
    
                $message = 'réservation réussie';
            }
    
            elseif(isset($_POST['modify'])) {
    
                $titre = mysqli_real_escape_string($mysqli, $_POST['titre']);
                $desc = mysqli_real_escape_string($mysqli, $_POST['desc']);
            
                $request_modify_event = "UPDATE reservations SET titre = '$titre', description =  '$desc', debut = '$date_debut' , fin = '$date_fin' WHERE id = '$_SESSION[eventID]'";
                
                $query_modify_event = $mysqli->query($request_modify_event);
                
                $message = 'modification effectuée';   
            }   
        }

    return $message;

    }

?>