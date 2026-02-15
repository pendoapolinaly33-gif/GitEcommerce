<?php
// ===== DB CONFIG =====
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "ecommerce_demo";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
  die("DB Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Ensure cart exists
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}
?>
