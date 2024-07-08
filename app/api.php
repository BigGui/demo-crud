<?php
session_start();

include 'includes/_config.php';
include 'includes/_functions.php';
include 'includes/_database.php';

header('Content-type:application/json');

if (!isset($_REQUEST['action'])) {
    triggerError('no_action');
}

// Check CSRF
preventCSRFAPI();

// Increase price from link
if ($_REQUEST['action'] === 'increase' && isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {

    $query = $dbCo->prepare("UPDATE product SET price = price * 1.1 WHERE ref_product = :id;");
    $isUpdateOk = $query->execute(['id' => intval($_REQUEST['id'])]);

    $query2 = $dbCo->prepare("SELECT price FROM product WHERE ref_product = :id;");
    $isQuery2Ok = $query2->execute(['id' => intval($_REQUEST['id'])]);

    if (!$isUpdateOk || !$isQuery2Ok) triggerError('update_ko');

    echo json_encode([
        'isOk' => $isUpdateOk && $isQuery2Ok,
        'id' => intval($_REQUEST['id']),
        'price' => $query2->fetchColumn()
    ]);
}
