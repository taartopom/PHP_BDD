<?php
$pdo = new PDO("mysql:host=localhost;dbname=php_formation;charset=UTF8",
            'root', '');

$statement = $pdo->prepare('SELECT * FROM country');
$result = $statement->execute();
$pays = $statement->fetchAll(PDO::FETCH_ASSOC);

$statement = $pdo->prepare('SELECT * FROM couleur');
$result = $statement->execute();
$couleurs = $statement->fetchAll(PDO::FETCH_ASSOC);

$idToUpdate = $_GET['id'];
if (isset($_POST['btn_pays_couleurs'])) {
    $paysSelectionne = filter_input(INPUT_POST, 'pays');
    $couleursSelectionnees = filter_input(INPUT_POST, 'couleurs',
                                        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);



    // récupérer les ids des pays en base pour voir
    // le choix de l'utilisateur est bien l'un d'eux
    $paysIds = [];
    foreach ($pays as $pEnBase) {
        $paysIds[] = $pEnBase['id'];
    }

    $errors = [];
    if (!in_array($paysSelectionne, $paysIds)) {
        $errors[] = "Veuillez sélectionner un pays valide";
    }

    if (count($errors) == 0) {
        $statement = $pdo->prepare('UPDATE product SET pays_id=:pays WHERE id=:id');
        $statement->execute([
            ':pays' => $paysSelectionne,
            ':id' => $idToUpdate
        ]);

        // insertion des couleurs en base pour ce produit
        // la liste droulante est multiple, il faut insérer les couleurs une à une dans
        // la table intermédiaire "product_couleur"

        // on peut supprimer toutes les associations produits/couleurs
        // avant de réinsérer les nouvelles associations
        // DELETE FROM product_couleur WHERE product_id=:idProduit
        foreach ($couleursSelectionnees as $couleursSelectionnee) {
            // on peut vérifier si le duo existe déjà en base :
            // SELECT * FROM product_couleur  WHERE product_id=:idProduit AND couleur_id=:idCouleur
             // $statement->rowCount() : nombre de résultat de la requête
            $statement = $pdo->prepare('INSERT INTO product_couleur(product_id,couleur_id)
                                VALUES (:idProduit, :idCouleur)');
            $result = $statement->execute([
                            ':idProduit' => $idToUpdate,
                            ':idCouleur' => $couleursSelectionnee
                        ]);
        }

    }




}
?>

<form method="post">
    <select name="pays">
        <?php
            foreach ($pays as $p) {
                echo "<option value='".$p['id']."'>".$p['name']."</option>";
            }
        ?>
    </select>

    <select name="couleurs[]" multiple>
        <?php
            foreach ($couleurs as $couleur) {
                echo "<option value='".$couleur['id']."'>".$couleur['name']."</option>";
            }
        ?>
    </select>

    <input type="submit" name="btn_pays_couleurs" />
</form>
