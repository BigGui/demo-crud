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
        addError('update_ko');
    }
}

// Add a new prodcut from form
else if ($_REQUEST['action'] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_REQUEST['name_product']) || strlen($_REQUEST['name_product']) === 0) {
        addError('product_name');
    }
    
    if (strlen($_REQUEST['name_product']) > 50) {
        addError('product_name_size');
    }

    if (!isset($_REQUEST['price']) || !is_numeric($_REQUEST['price'])) {
        addError('product_price');
    }

    if (!empty($_SESSION['errorsList'])) {
        $_SESSION['formData'] = [
            'name_product' => $_REQUEST['name_product'],
            'price' => $_REQUEST['price']
        ];
        redirectTo('index.php');
    }

    $insert = $dbCo->prepare("INSERT INTO `product`(`name_product`, `price`) VALUES (:name, :price);");

    $isInsertOk = $insert->execute([
        'name' => htmlspecialchars($_POST['name_product']),
        'price' => round($_POST['price'], 2)
    ]);

    if ($isInsertOk) {
        $_SESSION['msg'] = 'insert_ok';
    } else {
        addError('insert_ko');
    }
}

redirectTo('index.php');
