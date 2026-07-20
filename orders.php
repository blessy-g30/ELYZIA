<?php
require_once 'config/db.php';
$page_title = "My Orders";

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$user_id = $_SESSION['user_id'];

$orders = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");

include 'includes/header.php';
?>

<div class="container">
  <div class="section-title"><h2>My <span>Orders</span></h2></div>

  <?php if ($orders->num_rows === 0): ?>
    <div class="empty-state">
      <p>You haven't placed any orders yet.</p>
      <a href="index.php" class="btn" style="margin-top:16px;">Start Shopping</a>
    </div>
  <?php else: ?>
    <table class="admin-table">
      <tr><th>Order ID</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th></tr>
      <?php while ($o = $orders->fetch_assoc()):
          $items_res = $conn->query("SELECT oi.quantity, p.name FROM order_items oi
                                      JOIN products p ON oi.product_id = p.id
                                      WHERE oi.order_id = {$o['id']}");
          $item_names = [];
          while ($it = $items_res->fetch_assoc()) {
              $item_names[] = htmlspecialchars($it['name']) . ' ×' . $it['quantity'];
          }
      ?>
        <tr>
          <td>#<?php echo $o['id']; ?></td>
          <td><?php echo date("d M Y", strtotime($o['created_at'])); ?></td>
          <td><?php echo implode(', ', $item_names); ?></td>
          <td>₹<?php echo number_format($o['total_amount'], 2); ?></td>
          <td><span class="badge-status badge-<?php echo $o['status']; ?>"><?php echo $o['status']; ?></span></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
