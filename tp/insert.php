<?php
    // 1- connexion à la base
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=php_formation;charset=UTF8", 'root', '');
    }
    catch (PDOException $exception) {
        // pour le dev : debugger, le message d'erreur précis peut se récupérer comme ça :
        // $exception->getMessage();
        echo "Erreur de connexion à la base";
        exit;
    }

    // récupérer les données du produit à modifier
    if (isset($_GET['id_to_update'])) {
        //requête pour récupérer le produit à modifier
        $statement = $pdo->prepare("SELECT * FROM product WHERE id = :id");

        // bindParam directement dans un tableau (passé en paramètre de la fonction execute)
        $result = $statement->execute([':id' => $_GET['id_to_update']]);
        $product = $statement->fetch();
    }
    else {
        $product['code'] = "";
        $product['name'] = "";
        $product['description'] = "";
        $product['price'] = "";
        $product['stock'] = "";
        $product['category'] = "";
    }

    if (isset($_POST['btn_create_product'])) {
        // 1- récupérer les valeurs envoyées depuis le formulaire
        $id = filter_input(INPUT_POST, 'id');
        $code = filter_input(INPUT_POST, 'code');
        $name = filter_input(INPUT_POST, 'name');
        $description = filter_input(INPUT_POST, 'description');
        $price = filter_input(INPUT_POST, 'price');
        $stock = filter_input(INPUT_POST, 'stock');
        $category = filter_input(INPUT_POST, 'category');

        // 2- vérifier que les données sont valides
        $errors = [];
        if ($id == null && !preg_match("/[a-zA-Z]{3}-[0-9]{3}/", $code)) {
            $errors[] = "Veuillez saisir un code produit valide";
        }
        if ($name == null || $name == "" || mb_strlen($name) > 150) {
            $errors[] = "Veuillez saisir un nom de produit valide";
        }
        if ($description == null || $description == "") {
            $errors[] = "Veuillez saisir une description";
        }
        //if (!preg_match("/[0-9]{5}(,[0-9]{1,2})?/", $price)) {
        if (!preg_match("/[0-9]{1,5}(.[0-9]{1,2})?/", $price)) {
            $errors[] = "Veuillez saisir un prix valide";
        }
        if (!preg_match("/[0-9]{1,5}/", $stock)) {
            $errors[] = "Veuillez saisir un stock";
        }
        $categories = ["Processeur", "Carte graphique", "Ecran"];
        // mb_strlen($category) > 50 : pas utile car la vérif d'après englobe cette vérif
        if ($category != "" && !in_array($category, $categories)) {
            $errors[] = "Veuillez sélectionner une catégorie valide";
        }

        if (count($errors) > 0) {
            echo "Merci de corriger ces erreurs :<br>";
            foreach ($errors as $error) {
                echo $error."<br>";
            }
        }
        else {
            // enregistrement en bdd

            // 2- formater les données
            // remplacer la virgule du price par un point
            $price = str_replace(',', '.', $price);

            if ($id == null) {
                // rajouter la partie manquante dans le format du code : la date générée automatique
                $code = $code."-".date('Ymd');
                // préparer la date/heure d'insertion
                $createdAt = date('Y-m-d H:i:s');

                // 3- requête d'insertion
                $statement = $pdo->prepare("
                            INSERT INTO product
                            (code, name, description, price, stock, category, created_at)
                            VALUES (:code,:name, :desc, :price, :stock, :cat, :created);
              ");
                // bindParam directement dans un tableau (passé en paramètre de la fonction execute)
                $result = $statement->execute([
                    ':code' => $code,
                    ':name' => $name,
                    ':desc' => $description,
                    ':price' => $price,
                    ':stock' => $stock,
                    ':cat' => $category,
                    ':created' => $createdAt
                ]);
            }
            else {
                $statement = $pdo->prepare("UPDATE product 
                SET name=:name, description=:desc, price=:price,
                stock=:stock, category=:cat WHERE id = :id");

                // bindParam directement dans un tableau (passé en paramètre de la fonction execute)
                $result = $statement->execute([
                    ':name' => $name,
                    ':desc' => $description,
                    ':price' => $price,
                    ':stock' => $stock,
                    ':cat' => $category,
                    ':id' => $id
                ]);
            }

            if ($result) {
                echo "Produit bien enregistré en base";
            }
            else {
                echo "Erreur denregistrement en bdd";
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Création de produits</title>
    </head>

    <body>
        <h1>
            <?php
                if (isset($_GET['id_to_update'])) {
                    echo "Modifier un produit";
                }
                else {
                    echo "Créer un produit";
                }
            ?>

        </h1>

        <form method="post" action="insert.php">
            <?php
                if (isset($_GET['id_to_update'])) {
                    echo "<input type='hidden' name='id' value='".$product['id']."' />";
                }
                else {
                    echo '<input type="text" name="code" placeholder="Code produit (AAA-111)"
                   required pattern="[a-zA-Z]{3}-[0-9]{3}" value="'.$product['code'].'"/><br>';
                }
            ?>

            <input type="text" name="name" placeholder="Nom du produit" required maxlength="150" value="<?php echo $product['name']; ?>"/><br>
            <textarea name="description" required><?php echo $product['description']; ?></textarea><br>
            <input type="number" name="price" step="any"required pattern="[0-9]{1,5},[0-9]{1,2}" value="<?php echo $product['price']; ?>"/><br>
            <input type="number" name="stock" required pattern="[0-9]{1,5}" value="<?php echo $product['stock']; ?>"/><br>

            <select name="category">
                <option></option>
                <option <?php if ($product['category'] == "Processeur") echo "selected"; ?>>Processeur</option>
                <option <?php if ($product['category'] == "Carte graphique") echo "selected"; ?>>Carte graphique</option>
                <option <?php if ($product['category'] == "Ecran") echo "selected"; ?>>Ecran</option>
            </select><br>

            <input type="submit" name="btn_create_product" />
        </form>
    </body>

</html>
