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
    
    
    if($check == 1) {
    
        $debut_event = new DateTime("$_POST[date_debut_event] midnight + $_POST[start_time] hours");      
        $fin_event = new DateTime("$_POST[date_fin_event] midnight + $_POST[end_time] hours");

        if($debut_event->format('D') == 'Sat' || $debut_event->format('D') == 'Sun' || $fin_event->format('D') == 'Sat' || $fin_event->format('D') == 'Sun') {
            $check = 0;
            $message = 'Nous sommes fermés les week-ends';
        }
    }
        
    if($check == 1 && $debut_event->format('Y-m-d H') >= $fin_event->format('Y-m-d H')) {       
        $check = 0;
        $message = 'Erreur dans les créneaux horaires';
    }
            
    if($check == 1) {

        $debut_event_formated = $debut_event->format('Y-m-d H');
        $fin_event_formated = $fin_event->format('Y-m-d H');

        $request_date_events = "SELECT id, debut, fin FROM reservations WHERE debut BETWEEN '$debut_event_formated' and '$fin_event_formated' OR fin BETWEEN '$debut_event_formated' and '$fin_event_formated'";
        $query_date_events = $mysqli->query($request_date_events);
        $result_date_events = $query_date_events->fetch_all(); 
      
        for($x = 0; isset($result_date_events[$x]); $x++){
    
            $date_debut_SQL = new DateTime($result_date_events[$x][1]);
            $date_fin_SQL = new DateTime($result_date_events[$x][2]);

// Ici je vais avoir besoin de séparer les events sur une journée de ceux sur plusieurs jours. Pour l'event sur une journée je crée une boucle qui part de l'heure de départ 
// à l'heure de fin et me renvoie l'erreur s'il y a un match dans la base de données. Pour l'event sur plusieurs journées je commence par une boucle avec la variable k qui va 
//du jour de début au jour de fin. Il me reste à gérer les résa qui se font sur 2 mois distincts.

            if(isset($_POST['reservation'])) {    
                
                if($fin_event->format('Y-m-d') == $debut_event->format('Y-m-d')) {

                    for($a = $debut_event->format('H'); $a < $fin_event->format('H'); $a++) {

                        if($a == $date_debut_SQL->format('H') || $a == ($date_fin_SQL->format('H') - 1)) {
                            $check = 0;
                            $message = 'Votre réservation se chevauche sur une autre';
                            break;
                        }
                    }
                }
                
                elseif($fin_event->format('Y-m-d') > $debut_event->format('Y-m-d')) {

                    for($k = $debut_event->format('d'); $k <= $fin_event->format('d'); $k++) {

                        for($i = $debut_event->format('H'); $i < 20; $i++) {

                            if($k == $date_debut_SQL->format('d') || $k == $date_fin_SQL->format('d')) {

                                if($i == $date_debut_SQL->format('H') || $i == ($date_fin_SQL->format('H') - 1)) {
                                    $check = 0;
                                    $message = 'Votre réservation se chevauche sur une autre';
                                    break;
                                }
                            }                       
                        }

                        for($j = ($fin_event->format('H') - 1 ); $j >= 8; $j--) {

                            if($k == $date_debut_SQL->format('d') || $k == $date_fin_SQL->format('d')) {

                                if($j == $date_debut_SQL->format('H') || $j == ($date_fin_SQL->format('H') - 1)) {
                                    $check = 0;
                                    $message = 'Votre réservation se chevauche sur une autre';
                                    break;
                                }
                            }                       
                        }
                    }
                }
            }
    
            elseif(isset($_POST['modify'])) {  

                if($fin_event->format('Y-m-d') == $debut_event->format('Y-m-d')) {

                    for($a = $debut_event->format('H'); $a < $fin_event->format('H'); $a++) {
    
                        if($a == $date_debut_SQL->format('H') || $a == ($date_fin_SQL->format('H') - 1)) {

// Pour les modifications d'event j'ai besoin de rajouter une condition qui exclue la résa en cours de ma requête sql.
                            if($result_date_events[$x][0] !== $_SESSION['eventID']) {
                                $check = 0;
                                $message = 'Votre réservation se chevauche sur une autre';
                                break;
                            }
                        }
                    }
                }
                    
                elseif($fin_event->format('Y-m-d') > $debut_event->format('Y-m-d')) {

                    for($k = $debut_event->format('d'); $k <= $fin_event->format('d'); $k++) {
    
                        for($i = $debut_event->format('H'); $i < 20; $i++) {

                            if($k == $date_debut_SQL->format('d') || $k == $date_fin_SQL->format('d')) {

                                if($i == $date_debut_SQL->format('H') || $i == ($date_fin_SQL->format('H') - 1)) {

                                    if($result_date_events[$x][0] !== $_SESSION['eventID']) {
                                        $check = 0;
                                        $message = 'Votre réservation se chevauche sur une autre';
                                        break;
                                    }
                                }
                            }                       
                        }
        
                        for($j = ($fin_event->format('H') - 1 ); $j >= 8; $j--) {
                            
                            if($k == $date_debut_SQL->format('d') || $k == $date_fin_SQL->format('d')) {

                                if($j == $date_debut_SQL->format('H') || $j == ($date_fin_SQL->format('H') - 1)) {

                                    if($result_date_events[$x][0] !== $_SESSION['eventID']) {
                                        $check = 0;
                                        $message = 'Votre réservation se chevauche sur une autre';
                                        break;
                                    }
                                }
                            }                       
                        }
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
                VALUES ('$titre', '$desc', '$debut_event_formated', '$fin_event_formated', '$_SESSION[userID]')";
                
                $query_add_event = $mysqli->query($request_add_event);
    
                $message = 'réservation réussie';
            }
    
            elseif(isset($_POST['modify'])) {
    
                $titre = mysqli_real_escape_string($mysqli, $_POST['titre']);
                $desc = mysqli_real_escape_string($mysqli, $_POST['desc']);
            
                $request_modify_event = "UPDATE reservations 
                SET titre = '$titre', description =  '$desc', debut = '$debut_event_formated' , fin = '$fin_event_formated' WHERE id = '$_SESSION[eventID]'";
                
                $query_modify_event = $mysqli->query($request_modify_event);
                
                $message = 'modification effectuée';
            }   
        }

    return $message;

    }

?>