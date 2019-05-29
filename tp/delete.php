<?php
    if ($_GET['id_to_delete']) {
        // connexion à la base avec l'objet PDO
        $pdo = new PDO("mysql:host=localhost;dbname=php_formation;charset=UTF8", 'root', '');

        // requête de suppression
        $sql = "DELETE FROM product WHERE id=:id";
        $statement = $pdo->prepare($sql);
        $result = $statement->execute(['id'=> $_GET['id_to_delete']]);
        header('Location: select.php');
        exit;
    }
?>