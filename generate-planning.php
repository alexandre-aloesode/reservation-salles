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

function corps_planning($planning) {

    for($x = 0; $x < 11; $x++) {  

        $date = new DateTime("$_SESSION[date] midnight + 8 hours + $x hours"); new DateTimeZone("Europe/Paris");

        echo '<tr>';

            echo '<td class="creneaux">' . $date->format('H') . ' H </td>';

                for($i = 0; $i < 7; $i++) {

                    echo '<td>';

                    $date_planning = $date->format('Y-m-d H');

                        $check = 1;
// Je crée une variable check qui vient changer si un évènement est affiché dans une des cases. Si le check reste = 1 on affiche le href réserver. 
// Elle me sert aussi ci-dessous à check si le jour est un weekend

                        if($date->format('D')== 'Sat' || $date->format('D')== 'Sun') {

                            echo '<p class="closed"> fermé</p>';
                            $check = 0;
                        }

// Ci-dessous je crée une variable qui récupère la date à laquelle je suis dans ma boucle, et avec laquelle je vais parcourir ma requête des évènements pour trouver un match. 
// Je modifie les dates du planning, et les dates de la table réservation en format Y-m-d H pour qu'elles aient la même structure et soit en format string, comme ça je peux 
// les comparer. Si elles sont == je viens donc afficher l'évènement. J'ai besoin donc de supprimer les minutes/secondes pour avoir un match.
                    
                        for($j = 0; isset($planning[$j]); $j++){
        
                            $date_sql_debut = new DateTime($planning[$j][2]);
               
                            $date_sql_fin = new DateTime($planning[$j][3]);
                            $date_sql_fin -> modify('-1 hour');
// J'ai besoin d'enlever une heure à la date de fin pour qu'elle soit prise en compte dans mon tableau.

                            if($date_planning >= $date_sql_debut->format('Y-m-d H') && $date_planning <= $date_sql_fin->format('Y-m-d H')) {
//Grâce aux 2 conditions du dessus je remplis également les cases entre la date de début et celle de fin, quand la résa dure plus de 2 heures.
                                   
                                    $check = 0;

// Pour les 2 if suivants, ils viennent check si l'utilisateur est connecté, et affichent le tableau en fonction.

                                    if(isset($_SESSION['userID'])) {

                                        echo '<a class="booked" href="reservation.php?id=' . $planning[$j][0] .'">';

                                        echo '<p class="booked">' . $planning[$j][6] . '<br>';

                                        echo $planning[$j][1] . '</p>';

                                        echo '</a>';
                                    }

                                    else {

                                        echo '<p class="booked">' . $planning[$j][6] . '<br>';

                                        echo $planning[$j][1] . '</p>';
                                    }
                                                                             
                            }                                                                 
                        }

                         if($check == 1){

                            if(isset($_SESSION['userID'])) {
//Pour que quand l'utilisateur connecté clique sur un créneau vide et soit renvoyé vers le formulaire pré-rempli  je crée la variable ci-dessous qui 
// split la date du planning en 2: la date et l'heure
                                $form_pre_rempli = str_split($date_planning, 10);

                                echo '<a class="bookable" href="reservation-form.php?date=' 
                                . $form_pre_rempli[0].'&heure=' . $form_pre_rempli[1] .'">';

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