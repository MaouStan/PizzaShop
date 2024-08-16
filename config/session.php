<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // check location
    if (basename($_SERVER['PHP_SELF']) != 'login.php') {
        header("Location: login.php");
        exit();
    }
} else if (basename($_SERVER['PHP_SELF']) == 'login.php') {
} else if ($_SESSION['order_id'] == -1) {
    if (basename($_SERVER['PHP_SELF']) != 'admin.php' && basename($_SERVER['PHP_SELF']) != 'order.php') {
        header("Location: admin.php");
        exit();
    }
}
