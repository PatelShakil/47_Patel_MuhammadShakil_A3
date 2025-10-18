<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'shakil');
define('DB_NAME', 'shopping_cart');

function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>
