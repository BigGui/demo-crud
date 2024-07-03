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
        addMessage('update_ok');
    } else {
        addError('update_ko');
    }
}

// Update a product from form
else if ($_REQUEST['action'] === 'modify' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_REQUEST['ref_product']) && is_numeric($_REQUEST['ref_product'])) {
    if (!checkProductInfo($_REQUEST)) {
        redirectTo('index.php');
    }

    $query = $dbCo->prepare("UPDATE product SET name_product = :name_product, price = :price WHERE ref_product = :ref_product;");

    $isUpdateOk = $query->execute([
        'name_product' => htmlspecialchars($_REQUEST['name_product']),
        'price' => round($_REQUEST['price'], 2),
        'ref_product' => intval($_REQUEST['ref_product'])
    ]);

    if ($isUpdateOk && $query->rowCount() === 1) {
        addMessage('update_ok');
    } else {
        addError('update_ko');
    }
}

// Add a new product from form
else if ($_REQUEST['action'] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkProductInfo($_REQUEST)) {
        $_SESSION['formData'] = [
            'name_product' => $_REQUEST['name_product'],
            'price' => $_REQUEST['price']
        ];
        redirectTo('index.php');
    }

    $insert = $dbCo->prepare("INSERT INTO `product`(`name_product`, `price`) VALUES (:name, :price);");

    $isInsertOk = $insert->execute([
        'name' => htmlspecialchars($_REQUEST['name_product']),
        'price' => round($_REQUEST['price'], 2)
    ]);

    if ($isInsertOk) {
        addMessage('insert_ok');
    } else {
        addError('insert_ko');
    }
}

// Rise priority
else if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'up' && isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {

    $query = $dbCo->prepare("SELECT ref_product FROM product WHERE priority = (
	    SELECT priority - 1 FROM product WHERE ref_product = :id
    );");
    $query->execute(['id' => intval($_REQUEST['id'])]);
    
    $idToMove = intval($query->fetchColumn());
    if ($idToMove !== false) {
        $queryUpdate = $dbCo->prepare("UPDATE product SET priority = priority + 1 WHERE ref_product = :id;");
        $queryUpdate->execute(['id' => $idToMove]);
    } 

    $queryUpdate = $dbCo->prepare("UPDATE product SET priority = priority - 1 WHERE ref_product = :id;");
    $isUpdateOk = $queryUpdate->execute(['id' => intval($_REQUEST['id'])]);

    if ($isUpdateOk) {
        addMessage('update_ok');
    } else {
        addError('update_ko');
    }
}

redirectTo('index.php');
