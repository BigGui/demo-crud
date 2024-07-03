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
        echo '<h2>Modifier un produit</h2>'
            . getHtmlProductForm('modify', $productToEdit);
    } else {
        echo '<h2>Ajouter un produit</h2>'
            . getHtmlProductForm('create', isset($_SESSION['formData']) ? $_SESSION['formData'] : []);
    }
    eraseFormData();
    ?>
    <h2>Tous nos produits</h2>
    <ul>
        <?php

        $query = $dbCo->query("SELECT ref_product, name_product, price, priority FROM product ORDER BY priority ASC;");

        while ($product = $query->fetch()) {
            echo '<li>' . getHTMLProduct($product) . '</li>';
        }
        ?>
    </ul>
</body>

</html>