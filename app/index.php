<?php
session_start();

include 'includes/_config.php';
include 'includes/_functions.php';
include 'includes/_database.php';

generateToken();



if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {

    $query = $dbCo->prepare("SELECT name_product, ref_product, price FROM product WHERE ref_product = :ref_product;");

    $isQueryOk = $query->execute([
        'ref_product' => intval($_GET['id'])
    ]);

    $productToEdit = $query->fetch();

    if (!$isQueryOk || $productToEdit === false) {
        addError('product_edit_not_exist');
        redirectTo('index.php');
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>
        <a href="">Mes tâches</a>
    </h1>

    <?php

    echo getHtmlMessages($messages);

    echo getHtmlErrors($errors);

    if (isset($productToEdit) && is_array($productToEdit)) {
    ?>
        <h2>Modifier un produit</h2>

        <form action="actions.php" method="post">
            <ul>
                <li>
                    <label for="name_product">Nom du produit</label>
                    <input type="text" name="name_product" id="name_product" value="<?= $productToEdit['name_product'] ?>" placeholder="Oil - Canola" maxlength="50" required>
                </li>
                <li>
                    <label for="price">Prix du produit</label>
                    <input type="text" name="price" id="price" value="<?= $productToEdit['price'] ?>" placeholder="9.90" maxlength="16" required>
                </li>
            </ul>
            <input type="hidden" name="ref_product" value="<?= $productToEdit['ref_product'] ?>">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="hidden" name="action" value="modify">
            <input type="submit" value="Modifier le produit">
        </form>
    <?php
    } else {
    ?>
        <h2>Ajouter un produit</h2>

        <form action="actions.php" method="post">
            <ul>
                <li>
                    <label for="name_product">Nom du produit</label>
                    <input type="text" name="name_product" id="name_product" value="<?= isset($_SESSION['formData']) ? $_SESSION['formData']['name_product'] : '' ?>" placeholder="Oil - Canola" maxlength="50" required>
                </li>
                <li>
                    <label for="price">Prix du produit</label>
                    <input type="text" name="price" id="price" value="<?= isset($_SESSION['formData']) ? $_SESSION['formData']['price'] : '' ?>" placeholder="9.90" maxlength="16" required>
                </li>
            </ul>
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="hidden" name="action" value="create">
            <input type="submit" value="Ajouter le produit">
        </form>
    <?php
    }
    eraseFormData();
    ?>
    <h2>Tous nos produits</h2>
    <ul>
        <?php

        $minPrice = 0;
        $maxPrice = 100;

        $query = $dbCo->prepare("SELECT ref_product, name_product, price FROM product WHERE price BETWEEN :min AND :max;");

        $query->execute([
            'min' => $minPrice,
            'max' => $maxPrice
        ]);

        while ($product = $query->fetch()) {
            echo '<li>' . getHTMLProduct($product) . '</li>';
        }
        ?>
    </ul>
</body>

</html>