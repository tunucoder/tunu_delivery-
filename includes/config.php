<?php
// Database Configuration - TUNU DELIVERY
// Created by Kadili Dev
// Email: kadiliy17@gmail.com
// Contact: 0618240534

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tunu_delivery');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to sanitize input
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to generate order number
function generate_order_number() {
    return 'TUNU-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}
?>
