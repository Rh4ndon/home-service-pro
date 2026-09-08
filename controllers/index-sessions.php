<?php
include 'models/functions.php';
session_start();

// Check if the user is logged in
if (isset($_SESSION['is_logged_in'])) {
    // User is logged in
    if (isset($_SESSION['adminId'])) {
        header('Location: admin/');
    } else if (isset($_SESSION['repairmanId'])) {
        header('Location: repairman/');
    } else if (isset($_SESSION['customerId'])) {
        header('Location: customer/');
    }
}
