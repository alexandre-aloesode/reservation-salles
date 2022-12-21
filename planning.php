<?php include 'generate-planning.php'?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="index.css" rel = "stylesheet">
    <link href="header.css" rel = "stylesheet">
    <link href="footer.css" rel = "stylesheet">
    <link href="planning.css" rel = "stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" 
    integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" 
    crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <title>Planning</title>
</head>

<body>

 <?php include 'header.php' ?>

 <main>

    <form method="get" id ="calendar" class="formulaire">

        <button type="submit" name="previous_week" id="previous_week"> <i class="fa-solid fa-angles-left"> </i> </button>

        <button type="submit" name="reset" id="today_button">Reset</button>

        <button type="submit" name="next_week" id="next_week"> <i class="fa-solid fa-angles-right"> </i></button>

    </form>
 
    <table>

        <thead>

            <tr>

                <th>Créneaux</th>

                <?php jours_planning() ?>

            </tr>

        </thead>

        <tbody>

            <?php corps_planning($result_events) ?>  

        </tbody>

    </table>

</main>

<?php include 'footer.php' ?>

</body>
</html>