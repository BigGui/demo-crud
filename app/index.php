<?php
session_start();

include 'includes/_config.php';
include 'includes/_functions.php';
include 'includes/_templates.php';
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
    <ul id="errorsList" class="errors"></ul>
    <ul id="messagesList" class="messages"></ul>
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
    <ul id="productList">
        <?php

        $query = $dbCo->query("SELECT ref_product, name_product, price, priority FROM product ORDER BY priority ASC;");

        while ($product = $query->fetch()) {
            echo '<li class="js-product">' . getHTMLProduct($product) . '</li>';
        }
        ?>
    </ul>

    <template id="templateError">
        <li data-error-message="" class="errors__itm">Ici vient le message d'erreur</li>
    </template>

    <template id="templateMessage">
        <li data-message="" class="messages__itm">Ici vient le message</li>
    </template>

    <template id="templateProduct">
        <li class="js-product" data-product-id="159">
            <span data-product-name="">aze</span> (<span data-price-id="159">62.00</span> €)
            <button type="button" data-increase-id="159">💰</button> 
            <a data-up-link="" href="actions.php?action=up&amp;id=159&amp;token=99c6f3954cc44567f3c05e688873dd47">⬆️</a> 
            <a data-down-link="" href="actions.php?action=down&amp;id=159&amp;token=99c6f3954cc44567f3c05e688873dd47">⬇️</a> 
            <button type="button" data-delete-id="159">🗑️</button>
            <a data-edit-link="" href="index.php?action=edit&amp;id=159">🖋️</a>
        </li>
    </template>


    <script type="module" src="js/script.js"></script>
</body>

</html>