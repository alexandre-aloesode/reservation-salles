<?php 

include 'connecSQL.php';
include 'connec.php';

// Ci-dessous ma requête pour récupérer les évènements programmés.
$request_events = "SELECT reservations.id, reservations.titre, reservations.debut, reservations.fin, reservations.id_utilisateur, utilisateurs.id, utilisateurs.login 
FROM reservations 
INNER JOIN utilisateurs ON reservations.id_utilisateur = utilisateurs.id";

$query_events = $mysqli->query($request_events);
$result_events = $query_events->fetch_all();



// Ci-dessous je gère la navigation dans le calendrier semaine par semaine. Je crée 2 variables de session, week qui commence à 0 et augmente ou diminue de 1, 
// et date qui gère la requête de la classe DateTime.

if(!isset($_SESSION['week'])) {
    $_SESSION['week'] = 0;
}

if($_SESSION['week'] == 0) {
    $_SESSION['date'] = 'this week';
}
    
if(isset($_GET['next_week'])) {

    $_SESSION['week'] ++;

    if($_SESSION['week'] == 0) {
        $_SESSION['date'] = 'this week';
    }

    if($_SESSION['week'] == 1){
        $_SESSION['date'] = 'next week';
    }

    if($_SESSION['week'] > 1) {
        $_SESSION['date'] ="this week + $_SESSION[week] weeks";
    }

    if($_SESSION['week'] < 0){
        $_SESSION['date'] = "this week  $_SESSION[week] weeks";
    }

    header('Location: planning.php');
}

if(isset($_GET['previous_week'])){

    $_SESSION['week'] --;

    if($_SESSION['week'] == 1){
        $_SESSION['date'] = 'next week';
    }

    if($_SESSION['week'] == 0){
        $_SESSION['date'] = 'this week';
    }

    if($_SESSION['week'] > 1){
        $_SESSION['date'] = "this week + $_SESSION[week] weeks";
    }

    if($_SESSION['week'] == -1){
        $_SESSION['date'] = "last week";
    }

    if($_SESSION['week'] < -1){
        $_SESSION['date'] = "this week  $_SESSION[week] weeks";
    }

    header('Location: planning.php');
}

if(isset($_GET['reset'])){
    $_SESSION['week'] = 0;
    $_SESSION['date'] = 'this week';
    header('Location: planning.php');
}

// Ci-dessous la boucle qui m'affiche les titres du tableau, à savoir les jours de la semaine.
function jours_planning() {

$date = new DateTime("$_SESSION[date]"); new DateTimeZone("Europe/Paris");

                for($x = 0; $x < 7; $x++){
           
                    echo '<th>' . $date->format('D j M Y') . '</th>';

                    $date->modify("next day");        
                }
}

//  Pour générer mon tableau j'ai 3 boucle for: -->
//     <!-- -La première génère 12 lignes, à savoir les 12 créneaux horires de 8 à 19h
//     -La deuxième génère les colonnes, à savoir les 7 jours
//     -la 3ême vient parcourir ma requete dans les reservations et s'il y a un match de date m'affiche l'évènement.

function corps_planning($requete_events) {

    for($x = 0; $x < 11; $x++) {  

        $date = new DateTime("$_SESSION[date] midnight + 8 hours + $x hours"); new DateTimeZone("Europe/Paris");
// Pour afficher dans le planning les créneaux en mettant de x à y heures, j'ai besoin d'une 2ème variable datetime à laquelle j'ajoute une heure.
        $date_creneau_sup = new DateTime("$_SESSION[date] midnight + 8 hours + $x hours"); new DateTimeZone("Europe/Paris");

        echo '<tr>';

            echo '<td class="creneaux">' . $date->format('H') . ' à ' . $date_creneau_sup->modify('+ 1 hours')->format('H') .' H </td>';

                for($i = 0; $i < 7; $i++) {

                    echo '<td>';

                        $check = 1;
// Je crée une variable check qui vient changer si un évènement est affiché dans une des cases. Si le check reste = 1 on affiche le href réserver. 
// Elle me sert aussi ci-dessous à check si le jour est un weekend

                        if($date->format('D')== 'Sat' || $date->format('D')== 'Sun') {

                            echo '<p class="closed"> fermé</p>';
                            $check = 0;
                        }

// Ci-dessous je crée une variable qui récupère la date à laquelle je suis dans ma boucle, et avec laquelle je vais parcourir ma requête des évènements pour trouver un match. 
// Je modifie les dates du planning, et les dates de la table réservation en format Y-m-d H pour qu'elles aient la même structure, comme ça je peux 
// les comparer. Si elles sont == je viens donc afficher l'évènement. J'ai besoin donc de supprimer les minutes/secondes pour avoir un match.
                    
                        for($j = 0; isset($requete_events[$j]); $j++){
        
                            $date_sql_debut = new DateTime($requete_events[$j][2]);
               
                            $date_sql_fin = new DateTime($requete_events[$j][3]);
                            $date_sql_fin -> modify('-1 hour');
// J'ai besoin d'enlever une heure à la date de fin pour qu'elle soit prise en compte dans mon tableau.

                            if($date->format('Y-m-d H') >= $date_sql_debut->format('Y-m-d H') && $date->format('Y-m-d H') <= $date_sql_fin->format('Y-m-d H')) {
//Grâce aux 2 conditions du dessus je remplis également les cases entre la date de début et celle de fin, quand la résa dure plus d'1 heure.
                                   
                                    $check = 0;

// Pour les 2 if suivants, ils viennent check si l'utilisateur est connecté, et affichent le tableau en fonction.

                                    if(isset($_SESSION['userID'])) {

                                        // if($date_sql_fin > $date_sql_debut && $date_planning > $date_sql_debut->format('Y-m-d H')) {

                                        //     echo '<a class="booked" href="reservation.php?id=' . $planning[$j][0] .'">';

                                        //     echo '<p class="booked" rowspan = "' . ($date_sql_fin->format('H') - $date_sql_debut->format('H')) . '"> <br>';

                                        //     echo '</p>';

                                        //     echo '</a>';
                                        // }

                                        // else {

                                        echo '<a class="booked" href="reservation.php?id=' . $requete_events[$j][0] .'">';

                                        echo '<p class="booked">' . $requete_events[$j][6] . '<br>';

                                        echo $requete_events[$j][1] . '</p>';

                                        echo '</a>';
                                        // }
                                    }

                                    else {

                                        echo '<p class="booked">' . $requete_events[$j][6] . '<br>';

                                        echo $requete_events[$j][1] . '</p>';
                                    }                                                                            
                            }                                                                 
                        }

                         if($check == 1){

                            if(isset($_SESSION['userID'])) {

//Pour que quand l'utilisateur connecté clique sur un créneau vide et soit renvoyé vers le formulaire pré-rempli avec par défaut une heure de plus en heure de fin:
                            
                                echo '<a class="bookable" href="reservation-form.php?date=' . $date->format('Y-m-d').'&heure_debut=' 
                                . $date->format('H') .'&heure_fin=' . $date_creneau_sup->format('H') .'">';

                                echo '<p class="bookable"> <b> Réserver </b> </p>';

                                echo '</a>';
                            }

                            else {
                                
                                echo '<a href="connexion.php">';

                                echo '<p class="bookable"> Connectez-vous pour réserver </p>';

                                echo '</a>';
                            }
                         }
                        
                    echo '</td>';

// au début de ma boucle for des créneaux, je reset à chaque fois la date à aujourd'hui. Ici dans ma boucle qui parcourt les jours j'ai besoin à chaque tour de rajouter un jour.
                    $date->modify('next day');                          
                }   

        echo '</tr>';                        
    }

}

?>