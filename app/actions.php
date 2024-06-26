<?php
session_start();

include 'includes/_config.php';
include 'includes/_functions.php';
include 'includes/_database.php';

if (!isset($_REQUEST['action'])) {
    redirectTo('index.php');
}

// Check Referer and CSRF
preventCSRF();

// Increase price from link
if ($_REQUEST['action'] === 'increase' && isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {

    $query = $dbCo->prepare("UPDATE product SET price = price * 1.1 WHERE ref_product = :id;");
    $isUpdateOk = $query->execute(['id' => intval($_REQUEST['id'])]);

    if ($isUpdateOk) {
        $_SESSION['msg'] = 'update_ok';
    } else {
        $_SESSION['error'] = 'update_ko';
    }
}

// Add a new prodcut from form
else if ($_REQUEST['action'] === 'create') {
    
    $errorsList = [];
    if (!isset($_POST['name_product']) || strlen($_POST['name_product']) === 0) {
        $errorsList[] = 'Saisissez un nom pour le produit';
    }

    if (strlen($_POST['name_product']) > 50) {
        $errorsList[] = 'Saisissez un nom pour le produit de 50 caractères au maximum';
    }

    if (!isset($_POST['price']) || !is_numeric($_POST['price'])) {
        $errorsList[] = 'Saisissez un prix au format numérique.';
    }

    if (!empty($errorsList)) {
        $_SESSION['errorsList'] = $errorsList;
        redirectTo('index.php');
    }

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
}

redirectTo('index.php');
