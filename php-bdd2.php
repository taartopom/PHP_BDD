<?php
    include("fonctions.php");

    // connexion à la base avec l'objet PDO
    $pdo = new PDO("mysql:host=localhost;dbname=php_formation;charset=UTF8", 'root', '');

    // détecter la validation du formulaire d'édition
    if (isset($_POST['btn_edit_user'])) {
        // modification de l'utilisateur
        $id = filter_input(INPUT_POST, 'id');
        $nom = filter_input(INPUT_POST, "nom");
        $email = filter_input(INPUT_POST, "email");
        $dateNaissance = filter_input(INPUT_POST, "date_naissance");
        // repasser la date au format sql
        /*
        $dateNaissanceObjet = DateTime::createFromFormat("d/m/Y", $dateNaissance);
        $dateNaissance = $dateNaissanceObjet->format("Y-m-d");
        */
        $dateNaissance = modifDate($dateNaissance, "d/m/Y", "Y-m-d");

        // préaprer la requête sécurisée
        $statement = $pdo->prepare("UPDATE utilisateur
                              SET nom=:nomUser, email=:emailUser, date_naissance=:dateNaissanceUser
                              WHERE id=:idUser");
        // bindParam directement dans un tableau (passé en paramètre de la fonction execute)
        $statement->execute([
                ':nomUser' => $nom,
                ':emailUser' => $email,
                ':dateNaissanceUser' => $dateNaissance,
                ':idUser' => $id
        ]);
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
        <h1>BDD - Delete et Update</h1>

        <?php
            // suppression d'un enregistrement
            // supprimer un utilisateur
            // si un id a été passé dans l'url
            if (isset($_GET['id_to_remove'])) {
                $idToRemove = $_GET['id_to_remove'];
                $statement = $pdo->prepare("DELETE FROM utilisateur WHERE id=:idUser");
                $statement->bindParam(':idUser', $idToRemove);
                $statement->execute();
            }

            // modifier un enregistrement
            // UPDATE utilisateur SET nom='Nouveau nom', email='nouvelemail@mail.fr'
            // WHERE id=10


            $statement = $pdo->query("SELECT id, UPPER(nom) AS nom, email,date_naissance FROM utilisateur");
            $users = $statement->fetchAll(PDO::FETCH_ASSOC);


        ?>

        <h1>Liste des utilisateurs en BDD</h1>

        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Date de naissance</th>
                <th>Actions</th>
            </tr>

            <?php
                foreach ($users as $user) {
                    // comme la date est au format sql
                    // il faut la transformer au français avant de l'afficher
                    $dateNaissanceObjet = DateTime::createFromFormat("Y-m-d", $user['date_naissance']);
                    $dateNaissance = $dateNaissanceObjet->format("d/m/Y");

                    echo "<tr>
                                <td>".$user['id']."</td>
                                <td>".$user['nom']."</td>
                                <td>".$user['email']."</td>
                                <td>".$dateNaissance."</td>
                                <td>
                                    <a href='php-bdd2.php?id_to_remove=".$user['id']."'>
                                        <img width='15' src='delete.png'/>
                                    </a>
                                    <a href='php-bdd2.php?id_to_update=".$user['id']."'>
                                        Modifier
                                    </a>
                                </td>
                            </tr>";
                }
            ?>
        </table>

        <h1>Modifier un utilisateur en base de données</h1>
        <?php
            if (isset($_GET['id_to_update'])) {
                // récupérer l'id de l'utilisateur passé dans l'URL
                $idUser = $_GET['id_to_update'];
                // récupérer les informations de cet utilisateur en particulier
                $statement = $pdo->prepare("SELECT * FROM utilisateur WHERE id=:idUser");
                $statement->bindParam(":idUser", $idUser);
                $statement->execute();
                $userToUpdate = $statement->fetch();

                // repasser la date au format FR
                $dateNaissanceObjet = DateTime::createFromFormat("Y-m-d", $userToUpdate['date_naissance']);
                $dateNaissance = $dateNaissanceObjet->format("d/m/Y");

        ?>
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?php echo $userToUpdate['id']; ?>" />
                    <input type="text" name="nom" placeholder="Nom" value="<?php echo $userToUpdate['nom']; ?>"/>
                    <input type="text" name="email" placeholder="Email" value="<?php echo $userToUpdate['email']; ?>"/>
                    <input type="text" name="date_naissance" placeholder="Naissance jj/mm/aaaa"
                           value="<?php echo $dateNaissance; ?>"/>
                    <input type="submit" name="btn_edit_user"/>
                </form>
        <?php
            }
        ?>
    </body>
</html>