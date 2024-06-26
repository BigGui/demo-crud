<?php
session_start();


include 'includes/_functions.php';
include 'includes/_database.php';

generateToken();

if (!empty($_POST)) {

    if (!isset($_SERVER['HTTP_REFERER']) || !str_contains($_SERVER['HTTP_REFERER'], 'http://localhost:8080')) {
        $_SESSION['error'] = 'referer';
        header('Location: index.php');
        exit;
    }

    if (!isset($_SESSION['token']) || !isset($_POST['token']) || $_SESSION['token'] !== $_POST['token']) {
        $_SESSION['error'] = 'csrf';
        header('Location: index.php');
        exit;
    }

    $errorsList = [];
    if (!isset($_POST['name_product']) || strlen($_POST['name_product']) === 0) {
        $errorsList[] = 'Saisissez un nom pour le produit';
    }

    if (strlen($_POST['name_product']) > 50) {
        $errorsList[] = 'Saisissez un nom pour de produit de 50 caractères au maximum';
    }

    if (!isset($_POST['price']) || !is_numeric($_POST['price'])) {
        $errorsList[] = 'Saisissez un prix au format numérique.';
    }

    if (empty($errorsList)) {
        $insert = $dbCo->prepare("INSERT INTO `product`(`name_product`, `price`) VALUES (:name, :price);");

        $bindValues = [
            'name' => htmlspecialchars($_POST['name_product']),
            'price' => round($_POST['price'], 2)
        ];

        $isInsertOk = $insert->execute($bindValues);

        if ($isInsertOk) {
            $_SESSION['msg'] = 'insert_ok';
        } else {
            $_SESSION['error'] = 'insert_ko';
        }
        header('Location: index.php');
        exit;
    }
}

if (!empty($_GET) && isset($_GET['action']) && $_GET['action'] === 'increase' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $query = $dbCo->prepare("UPDATE product SET price = price * 1.1 WHERE ref_product = :id;");
    $isUpdateOk = $query->execute(['id' => intval($_GET['id'])]);

    if ($isUpdateOk) {
        $_SESSION['msg'] = 'update_ok';
    } else {
        $_SESSION['error'] = 'update_ko';
    }
    header('Location: index.php');
    exit;
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

    $errors = [
        'csrf' => 'Votre session est invalide.',
        'referer' => 'D\'où venez vous ?',
        'insert_ko' => 'Erreur lors de la sauvegarde de la produit.',
        'update_ko' => 'Erreur lors de la modif du produit.'
    ];
    if (isset($_SESSION['error'])) {
        echo '<p class="notif-error">' . $errors[$_SESSION['error']] . '</p>';
        unset($_SESSION['error']);
    }

    $messages = [
        'insert_ok' => 'Produit sauvegardé.',
        'update_ok' => 'Produit modifié.'
    ];
    if (isset($_SESSION['msg'])) {
        echo '<p class="notif-success">' . $messages[$_SESSION['msg']] . '</p>';
        unset($_SESSION['msg']);
    }
    ?>

    <h2>Ajouter un produit</h2>

    <?php
    if (!empty($errorsList)) {
        echo '<ul>' . implode(array_map(fn ($e) => '<li>' . $e . '</li>', $errorsList)) . '</ul>';
    }
    ?>
    <form action="" method="post">
        <ul>
            <li>
                <label for="name_product">Nom du produit</label>
                <input type="text" name="name_product" id="name_product" value="<?= isset($_POST['name_product']) ? $_POST['name_product'] : '' ?>" placeholder="Oil - Canola" maxlength="50" required>
            </li>
            <li>
                <label for="price">Prix du produit</label>
                <input type="text" name="price" id="price" value="<?= isset($_POST['price']) ? $_POST['price'] : '' ?>" placeholder="9.90" maxlength="16" required>
            </li>
        </ul>
        <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
        <input type="submit" value="Ajouter le produit">
    </form>
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

        // foreach ($query->fetchAll() as $product) {
        while ($product = $query->fetch()) {
            echo '<li>'
                . $product['name_product'] . ' (' . $product['price'] . ' €)'
                . ' <a href="?action=increase&id=' . $product['ref_product'] . '">augmenter</a>'
                . '</li>';
        }
        ?>
    </ul>
</body>

</html>