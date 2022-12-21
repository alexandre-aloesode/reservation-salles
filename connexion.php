<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="index.css" rel = "stylesheet">
    <link href="formulaires.css" rel = "stylesheet">
    <link href="header.css" rel = "stylesheet">
    <link href="footer.css" rel = "stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" 
    integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" 
    crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <title>Page de connexion</title>
</head>
<body>

    <?php 
        include 'header.php';
        include 'connec.php';
    ?>
        <main>
            <form method="post" class="formulaire">

                <?php if($check !== 2): ?>

                    <h2>CONNEXION</h2>
                
                <?php endif ?>

                <h3>

                    <?php if(isset($_POST['connexion'])) echo $message ?>
                    
                </h3>

                <?php if(isset($_POST['connexion']) && $check == 2): ?>

                    <h2>Bonjour et bienvenue <?= $_POST['pseudo'] ?> !</h2>
                
                <?php else: ?>
                               
                    <label for="pseudo">Pseudo :</label>
                    <input type="text" name="pseudo" class="form_input">
                    <br>

                    <label for="password">Mot de passe :</label>
                    <input type="password" name="mdp" class="form_input">
                    <br>

                    <button type="submit" name="connexion">Se connecter</button>
                
                <?php endif ?>
            </form>    
        </main>

    <?php include 'footer.php' ?>
    
</body>
</html>