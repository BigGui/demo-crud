<?php

try {
    $dbCo = new PDO(
        'mysql:host=db;dbname=ecom;charset=utf8',
        'app-php',
        'dwwm2024'
    );

    $dbCo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('ERREUR CONNEXION MYSQL' . $e->getMessage());
}
