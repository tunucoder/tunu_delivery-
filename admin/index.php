<?php
// Admin Index - Redirect to appropriate page
session_start();

// Check if admin is logged in
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>
