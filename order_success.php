<?php
require_once 'config/db.php';
$page_title = "Order Confirmed";

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

include 'includes/header.php';
?>

<div class="container">
  <div class="auth-card" style="text-align:center; max-width:520px;">
    <div style="font-size:52px; color:#2E6B3A; margin-bottom:10px;">✓</div>
    <h2>Order Placed Successfully!</h2>
    <p class="sub">Your order <strong>#<?php echo $order_id; ?></strong> has been confirmed and will be delivered soon.</p>
    <a href="orders.php" class="btn" style="margin-top:10px;">View My Orders</a>
    <a href="index.php" class="btn btn-outline" style="margin-top:10px;">Continue Shopping</a>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
