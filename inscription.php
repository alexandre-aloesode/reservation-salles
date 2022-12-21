<?php

include 'connecSQL.php';

$request_user_info= "SELECT * FROM `utilisateurs`";
$query_user_info = $mysqli->query($request_user_info);
$result_user_info = $query_user_info->fetch_all();

$message;
$check = 1;

if(isset($_POST['inscription'])){

    if(empty($_POST['pseudo']) || empty($_POST['mdp']) || trim($_POST['pseudo'] == '' || trim($_POST['mdp'])) == '') {
        
        $check = 0;
        $message = 'Certains champs sont vides';
    }

    if($_POST['mdp'] !== $_POST['mdp_confirm']) {
        $check = 0;
        $message =  'Les mots de passe ne correspondent pas';
    }

    if ($check == 1) {

        for($x = 0; isset($result_user_info[$x]); $x++ ) {

                if($result_user_info[$x][1] == $_POST['pseudo']) {
                    $check = 0 ;
                    $message = 'Ce pseudo existe déjà';
                }
        } 
    }
        
    if($check == 1) {

        $login = $_POST['pseudo'];
        $mdp_hash = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
        $request_create = "INSERT INTO `utilisateurs`(`login`, `password`) VALUES ('$login','$mdp_hash')";
        $query_create = $mysqli->query($request_create);

// Les lignes ci_dessous me permettent de connecter automatiquement l'utilisateur après avoir créé son compte. Ensuite dans connec.php je vais récupérer son ID.
        if(session_id() == '') {
            session_start();
            $_SESSION['user'] = $_POST['pseudo'];
        }
            
        $message =  'Compte créé avec succès. <br> Vous êtes désormais connecté.';
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

        <form method="post" class ="formulaire">

            <h2>
                <?php

                    if(isset($_POST['inscription']) && isset($_SESSION['user'])) {
                        echo 'Félicitations!';
                    }

                    else {
                        echo 'INSCRIPTION';
                    }

                ?> 
            </h2>

            <h3>
                <?php if(isset($_POST['inscription'])) echo $message ?>
            </h3>

            <?php if(!isset($_POST['inscription']) && $check !== 1): ?>

                <label for="pseudo">Pseudo:</label>
                <input type="text" name="pseudo">
                <br>

                <label for="pseudo">Mot de passe:</label>
                <input type="password" name="mdp">
                <br>
                
                <label for="pseudo">Confirmation mot de passe:</label>
                <input type="password" name="mdp_confirm">
                <br>

                <button type="submit" name="inscription">S'inscrire</button>

        </form>
            
            <?php endif; ?>
    </main>

    <?php include 'footer.php' ?>
    
</body>
</html>