<?php

    include 'connec.php';
    include 'connecSQL.php';

// ci_dessous une requête php my admin qui me permet de récupérer les infos du profil pour les 
// utiliser dans la page profil.php pour afficher les infos du profil du user connecté
   
    $request_fetch_user_info= "SELECT * FROM `utilisateurs` where id = '$_SESSION[userID]'";
    $query_fetch_user_info = $mysqli->query($request_fetch_user_info);
    $result_fetch_user_info = $query_fetch_user_info->fetch_all();


    $message;
    $check = 1 ;

    if(isset($_POST['profile_change'])) {  

        if(empty($_POST['pseudo']) || empty($_POST['new_mdp']) || trim($_POST['pseudo'] == '' || trim($_POST['new_mdp'])) == '') {
            $check = 0;
            $message = 'Certains champs sont vides';
        }

        if($_POST['new_mdp'] !== $_POST['new_mdp_confirm']) {
            $check = 0;
            $message = 'Les nouveaux mots de passe ne correspondent pas';
        }

        if($check == 1) {

            if(!password_verify($_POST['mdp'], $result_fetch_user_info[0][2])) {
                $check = 0;
                $message = 'Ancien mot de passe incorrect';
            }

            if($check == 1) {   

                $request_user_info= "SELECT * FROM `utilisateurs`";
                $query_user_info = $mysqli->query($request_user_info);
                $result_user_info = $query_user_info->fetch_all();

                for($x = 0; isset($result_user_info[$x]); $x++ ) {
                        if($result_user_info[$x][1] == $_POST['pseudo'] && $result_user_info[$x][0] !== $_SESSION['userID']) {
                            $check = 0;
                            $message = 'Ce pseudo existe déjà';
                        }
                }
            }
        }

        if($check == 1) {
                
        $modified_mdp_hashed = password_hash($_POST['new_mdp'], PASSWORD_DEFAULT);

        $update_user_profile = "UPDATE utilisateurs 
        SET login = '$_POST[pseudo]', password = '$modified_mdp_hashed' 
        WHERE id= '$_SESSION[userID]'";
        $query_update_user_profile = $mysqli->query($update_user_profile);

        $message = "informations modifiées.";
        }
    }


// Ci_dessous ma requête pour supprimer le profil, et suppression en cascade de ses réservations dans la base de données.
    if(isset($_POST['delete_profile'])) {
        
        $request_delete_profile_resa = "DELETE FROM `reservations` WHERE reservations.id_utilisateur = '$_SESSION[userID]'";
        $query_delete_profile_resa = $mysqli->query($request_delete_profile_resa);

        $request_delete_profile = "DELETE FROM `utilisateurs` WHERE utilisateurs.id = '$_SESSION[userID]'";
        $query_delete_profile = $mysqli->query($request_delete_profile);
        session_destroy();
        header('Location: index.php');
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="formulaires.css" rel="stylesheet">
    <link href="index.css" rel = "stylesheet">
    <link href="header.css" rel = "stylesheet">
    <link href="footer.css" rel = "stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" 
    integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" 
    crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <title>Profil</title>
</head>
<body>

    <?php include 'header.php'?>

    <main>
        
        <form method="post" class ="formulaire">

        <h2>MODIFICATION DE PROFIL</h2>

        <h3>
           <?php 
                if(isset($_POST['profile_change'])) {
                    echo $message;
                }
            ?>
        </h3>

            <label for="pseudo">Pseudo : </label>
            <input type="text" name="pseudo" value="<?= $result_fetch_user_info[0][1]?>" >
            <br>       
<!-- infos des values récupérées grâce à ma requête sql du haut de la page -->

            <label for="new_mdp">Nouveau mot de passe : </label>
            <input type="password" name="new_mdp">
            <br>

            <label for="new_mdp_confirm">Confirmez votre nouveau mot de passe</label>
            <input type="password" name="new_mdp_confirm">
            <br>

            <label for="mdp">Tapez votre ancien mot de passe pour confirmer les changements</label>
            <input type="password" name="mdp">
            <br>

            <button type="submit" name="profile_change">Modifier</button>
        
        </form>

        <form method="post" class="formulaire">
                <button type="submit" id="delete_profile" name="delete_profile">Supprimer mon compte</button>
        </form>

    </main>

    <?php include 'footer.php' ?>

</body>
</html>