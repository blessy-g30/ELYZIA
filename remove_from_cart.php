<?php
require_once 'config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = (int)$_POST['cart_id'];
    $user_id = $_SESSION['user_id'];
    $conn->query("DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id");
}
header("Location: cart.php");
