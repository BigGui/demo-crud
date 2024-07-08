<?php
session_start();

include 'includes/_config.php';
include 'includes/_functions.php';
include 'includes/_database.php';

header('Content-type:application/json');

$inputData = json_decode(file_get_contents('php://input'), true);

if (!isset($inputData['action'])) {
    triggerError('no_action');
}

// Check CSRF
preventCSRFAPI($inputData);

// Increase price from link
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $inputData['action'] === 'increase' && isset($inputData['id']) && is_numeric($inputData['id'])) {

    $query = $dbCo->prepare("UPDATE product SET price = price * 1.1 WHERE ref_product = :id;");
    $isUpdateOk = $query->execute(['id' => intval($inputData['id'])]);

    $query2 = $dbCo->prepare("SELECT price FROM product WHERE ref_product = :id;");
    $isQuery2Ok = $query2->execute(['id' => intval($inputData['id'])]);

    if (!$isUpdateOk || !$isQuery2Ok) triggerError('update_ko');

    echo json_encode([
        'isOk' => $isUpdateOk && $isQuery2Ok,
        'id' => intval($inputData['id']),
        'price' => $query2->fetchColumn()
    ]);
}
