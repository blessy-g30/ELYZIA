<?php
require_once '../config/db.php';
require_once 'auth_check.php';

$total_products = $conn->query("SELECT COUNT(*) c FROM products")->fetch_assoc()['c'];
$total_orders = $conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()['c'];
$total_users = $conn->query("SELECT COUNT(*) c FROM users WHERE role='customer'")->fetch_assoc()['c'];
$total_revenue = $conn->query("SELECT COALESCE(SUM(total_amount),0) r FROM orders WHERE status != 'cancelled'")->fetch_assoc()['r'];

$recent_orders = $conn->query("SELECT o.*, u.name AS customer_name FROM orders o
                                JOIN users u ON o.user_id = u.id
                                ORDER BY o.created_at DESC LIMIT 8");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - Elyzia</title>
<link rel="stylesheet" href="/elyzia/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include 'sidebar.php'; ?>

  <div class="admin-main">
    <div class="section-title" style="margin-top:0;"><h2>Dashboard</h2></div>

    <div class="stat-cards">
      <div class="stat-card"><div class="num"><?php echo $total_products; ?></div><div class="label">Total Products</div></div>
      <div class="stat-card"><div class="num"><?php echo $total_orders; ?></div><div class="label">Total Orders</div></div>
      <div class="stat-card"><div class="num"><?php echo $total_users; ?></div><div class="label">Registered Customers</div></div>
      <div class="stat-card"><div class="num">₹<?php echo number_format($total_revenue, 0); ?></div><div class="label">Total Revenue</div></div>
    </div>

    <h3 style="margin-bottom:14px;">Recent Orders</h3>
    <table class="admin-table">
      <tr><th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr>
      <?php while ($o = $recent_orders->fetch_assoc()): ?>
        <tr>
          <td>#<?php echo $o['id']; ?></td>
          <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
          <td>₹<?php echo number_format($o['total_amount'], 2); ?></td>
          <td><span class="badge-status badge-<?php echo $o['status']; ?>"><?php echo $o['status']; ?></span></td>
          <td><?php echo date("d M Y", strtotime($o['created_at'])); ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body>
</html>
