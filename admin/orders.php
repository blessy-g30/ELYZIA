<?php
require_once '../config/db.php';
require_once 'auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $conn->real_escape_string($_POST['status']);
    $allowed = ['placed','shipped','delivered','cancelled'];
    if (in_array($status, $allowed)) {
        $conn->query("UPDATE orders SET status = '$status' WHERE id = $order_id");
    }
    header("Location: orders.php");
    exit;
}

$orders = $conn->query("SELECT o.*, u.name AS customer_name, u.email FROM orders o
                         JOIN users u ON o.user_id = u.id
                         ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Orders - Elyzia Admin</title>
<link rel="stylesheet" href="/elyzia/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include 'sidebar.php'; ?>
  <div class="admin-main">
    <div class="section-title" style="margin-top:0;"><h2>All Orders</h2></div>

    <table class="admin-table">
      <tr><th>ID</th><th>Customer</th><th>Total</th><th>Date</th><th>Status</th><th>Update</th></tr>
      <?php while ($o = $orders->fetch_assoc()): ?>
        <tr>
          <td>#<?php echo $o['id']; ?></td>
          <td><?php echo htmlspecialchars($o['customer_name']); ?><br><small style="color:var(--muted);"><?php echo htmlspecialchars($o['email']); ?></small></td>
          <td>₹<?php echo number_format($o['total_amount'], 2); ?></td>
          <td><?php echo date("d M Y", strtotime($o['created_at'])); ?></td>
          <td><span class="badge-status badge-<?php echo $o['status']; ?>"><?php echo $o['status']; ?></span></td>
          <td>
            <form method="POST" style="display:flex; gap:8px;">
              <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
              <select name="status">
                <?php foreach (['placed','shipped','delivered','cancelled'] as $s): ?>
                  <option value="<?php echo $s; ?>" <?php echo $s==$o['status']?'selected':''; ?>><?php echo ucfirst($s); ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="btn btn-sm">Update</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body>
</html>
