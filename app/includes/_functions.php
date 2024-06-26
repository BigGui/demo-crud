<?php

/**
 * Generate a unique token and addto the session 
 *
 * @return void
 */
function generateToken() {
    if (!isset($_SESSION['token'])) {
        $_SESSION['token'] = md5(uniqid(mt_rand(), true));
    }
}