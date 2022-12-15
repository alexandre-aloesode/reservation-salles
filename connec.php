<?php

    if(session_id() == ''){
        session_start();
    }

    if(isset($_GET['deco']) && $_GET['deco'] == 'deco'){
        session_destroy();
        header('Location: index.php');
    }

    // if(isset($_SESSION['user']) && empty($_SESSION['userID'])){
    //     include 'connecSQL.php';
    //     $request_ID = "SELECT id from utilisateurs WHERE login = '$_SESSION[user]'";
    //     $query_ID = $mysqli->query($request_ID);
    //     $result_ID = $query_ID->fetch_all();
    //     $_SESSION['userID'] = $result_ID[0][0];
    // }
// Ci-dessus une requête pour récupérer la user ID au cas où il vient de créer son compte, info important car c'est celle là que j'utilise pour afficher mon header par exemple.
// je l'ai commenté car l'exercice demande une redirection vers la page de connexion et non pas de connecter l'user à la création de son compte.

    $check = 0;
// Le $check me sert pour la connexion. si $check = 0 le user n'existe pas, 
// si = 1 le nom d'user et le mdp ne correspondent pas
// si =2 tout est bon et la connexion se fait
    $message;

    if(isset($_POST['connexion']))
    {
        include 'connecSQL.php';
        $request_login= "SELECT `login`, `password`,`id` FROM `utilisateurs`";
        $query_login = $mysqli->query($request_login);
        $result_login = $query_login->fetch_all();
        for($x = 0; isset($result_login[$x]); $x++){
            if($result_login[$x][0] == $_POST['pseudo']){
                $check ++;
                    if(password_verify($_POST['mdp'], $result_login[$x][1])) {
                        $check ++;
                        $_SESSION['userID'] = $result_login[$x][2];
// le $_SESSION['userID'] permet de récupérer l'ID du user au cas où il souhaite modifier son profil
// et pour lui afficher ses infos comme demandé dans l'énoncé.
// Ma requête sql demande login, mdp et id donc l'index 2 est bien l'id si $x trouve un user existant 
                    }
            }       
          }
        if($check == 0){
            $message = "Ce nom d'utilisateur n'existe pas.";
        } elseif($check == 1){
            $message = "Le nom d'utilisateur et le mot de passe ne correspondent pas.";
        } elseif($check == 2){
            $message = "Connexion réussie.";
            $_SESSION['user'] = $_POST['pseudo'];
        }
    } 
?>