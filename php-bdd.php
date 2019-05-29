<?php
if (isset($_POST['btn_new_user'])) {
    // le formulaire a été validé
    $link = mysqli_connect("localhost", "root", "", "php_formation");
    if (!$link) {
        echo "Erreur : Impossible de se connecter à MySQL.";exit;
    }

    // 1- préparer la requêtes
    $nom = filter_input(INPUT_POST, 'nom');
    $email = filter_input(INPUT_POST, 'email');
    $dateNaissance =  filter_input(INPUT_POST, 'date_naissance');

    // comme la date est au format français jj/mm/aaaa
    // il faut la transformer en format sql yyyy-mm-dd
    $dateNaissanceObjet = DateTime::createFromFormat("d/m/Y", $dateNaissance);
    $dateNaissance = $dateNaissanceObjet->format("Y-m-d");

    /* avant on pouvait faire ça :
    // protéger les valeurs contre les injections SQL
    $nom = str_replace("'", "\'", $nom);

    $sql = "INSERT INTO utilisateur (id, nom, email, date_naissance)
            VALUES (NULL, '".$nom."', '".$email."', '".$dateNaissance."')"
          ;

    // envoyer la requête
    $result = mysqli_query($link, $sql);
    */

    /* version sécurisée */
    /* récupérer dse valeurs depuis un formulaire : */

    // 1- préparer la requête sans les valeurs
    $sql = "INSERT INTO utilisateur (id, nom, email, date_naissance)
                    VALUES (NULL, ?, ?, ?)
                  ";
    // 2- remplacer les ? par les valeurs
    $statement = mysqli_prepare($link, $sql);
    /* types possibles :
    i - Integer
    d - Double
    s - String
    b - Blob
    */
    mysqli_stmt_bind_param($statement, "sss", $nom, $email, $dateNaissance);
    $result = mysqli_stmt_execute($statement);

    // une insertion ne renvoie pas d'enregistrement,
    // mais un boolean vrai si l'insertion est ok, et faux si erreur mysql
    // d'insertion
    if ($result) {
        echo "Ok";
    }
    else {
        // afficher la dernière erreur générée
        // ne pas afficher l'erreur à l'utilisateur : cette fonction est pour débugger
        echo mysqli_error($link);
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>php mysql</title>
        <meta charset="UTF-8">

        <style>
            table {
                border:1px solid;
                border-collapse: collapse;
            }

            td, th {
                padding: 7px;
                border:1px solid;
            }

            th {
                color:red;
            }
        </style>
    </head>

    <body>
        <h1>BDD - Connexion / Requêtes</h1>

        <?php
            // écrire dans un fichier
            // file_put_contents() ouvre (ou crée) un fichier,
            // écrit dans le fichier (et écrase s'il existe déjà du texte) puis ferme le fichier
            file_put_contents("data2.txt", "Bonjour dans le fichier.");

            // utiliser le troisième pour ajouter du texte plutôt que d'écraser
            file_put_contents("data.txt", "\nSalut", FILE_APPEND);

            // lire un fichier
            $contenu = file_get_contents('data.txt');
            // echo $contenu;
            // transformer des \n en <br> : nl2br
            $contenu = nl2br($contenu);
            // echo $contenu;


            // connexion à la base de donnée php_formation sur le serveur localhost
            // 1ere étape : connexion au serveur
            $link = mysqli_connect("localhost", "root", "", "php_formation");
            if (!$link) {
                echo "Erreur : Impossible de se connecter à MySQL." . PHP_EOL;
                echo "Errno de débogage : " . mysqli_connect_errno() . PHP_EOL;
                echo "Erreur de débogage : " . mysqli_connect_error() . PHP_EOL;
                exit;
            }
            mysqli_set_charset($link, "utf8");

            // envoyer une requête à la base
            // 1- préparer la requête sql
            $sql = "SELECT * FROM utilisateur";
            // 2- envoyer la requête à mysql
            $result = mysqli_query($link, $sql);
            // result est un objet de type mysql_result

            // on récupère les utilisateurs un par un (ici dans un tableau associatif)
            // MYSQLI_NUM pour tableau numérique
            // MYSQLI_ASSOC pour tableau associatif
            // MYSQLI_BOTH : numérique et associatif
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                // afficher un utilisateur méthode 1
                echo $row["id"]." ".$row['nom']." ".$row['email']." ".$row['date_naissance'];
                echo "<br>";

                // afficher l'utilisateur grâce à une boucle méthode 2
                foreach ($row as $value) {
                    echo $value." ";
                }
                echo "<br>";
            }

            // on récupère les utilisateurs  tous d'un coup (ici dans un tableau associatif grâce à fetch_all)
            $sql = "SELECT * FROM utilisateur";
            $result = mysqli_query($link, $sql);
            $users = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($users as $user) {
                // afficher un utilisateur méthode 1
                echo $user["id"]." ".$user['nom']." ".$user['email']." ".$user['date_naissance'];
                echo "<br>";
            }
        ?>

        <h1>Liste des utilisateurs en BDD</h1>

        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Date de naissance</th>
            </tr>

            <?php
                foreach ($users as $user) {
                    echo "<tr>
                            <td>".$user['id']."</td>
                            <td>".$user['nom']."</td>
                            <td>".$user['email']."</td>
                            <td>".$user['date_naissance']."</td>
                        </tr>";
                }
            ?>
        </table>

        <h1>Insérer un utilisateur en base de données</h1>

        <form method="POST" action="">
            <input type="text" name="nom" placeholder="Nom"/>
            <input type="text" name="email" placeholder="Email"/>
            <input type="text" name="date_naissance" placeholder="Naissance jj/mm/aaaa"/>
            <input type="submit" name="btn_new_user" />
        </form>

    </body>
</html>