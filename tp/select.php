<?php
    // connexion à la base avec l'objet PDO
    $pdo = new PDO("mysql:host=localhost;dbname=php_formation;charset=UTF8", 'root', '');

    $sql = "SELECT * FROM product ORDER BY created_at DESC";
    $statement = $pdo->query($sql);
    $products = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Affichages des produits</title>
    </head>

    <body>
        <h1>Affichages des produit</h1>

        <?php
            foreach ($products as  $product) {
                echo "<div style='border:1px solid;margin-bottom:10px;'>
                        Code: ".$product['code']."<br>
                        Nom:  ".$product['name']."<br>
                        Description: ".$product['description']."<br>
                        Prix: ".$product['price']."<br>
                        Stock: ".$product['stock']."<br>
                        Categorie: ".$product['category']."<br>
                        <a href='delete.php?id_to_delete=".$product['id']."'>Supprimer</a><br>
                        <a href='insert.php?id_to_update=".$product['id']."'>Modifier</a>
                    </div>";
            }
        ?>

    </body>

</html>
