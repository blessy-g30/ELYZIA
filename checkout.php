<?php
require_once 'config/db.php';
$page_title = "Checkout";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

$sql = "SELECT c.quantity, p.price, p.name FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $user_id";
$items = $conn->query($sql);
$subtotal = 0;
$rows = [];
while ($row = $items->fetch_assoc()) {
    $rows[] = $row;
    $subtotal += $row['price'] * $row['quantity'];
}
if (count($rows) === 0) {
    header("Location: cart.php");
    exit;
}
$delivery = $subtotal > 999 ? 0 : 49;
$total = $subtotal + $delivery;

include 'includes/header.php';
?>

<div class="container">
  <div class="section-title"><h2>Checkout</h2></div>

  <div style="display:grid; grid-template-columns:2fr 1fr; gap:28px; align-items:start;">
    <div class="auth-card" style="margin:0;">
      <h2>Delivery Details</h2>
      <p class="sub">Where should we deliver your order?</p>
      <form action="place_order.php" method="POST">
        <div class="form-group">
          <label>Full Address</label>
          <textarea name="address" rows="4" required placeholder="House no, street, city, state, pincode"></textarea>
        </div>
        <div class="form-group">
          <label>Phone Number</label>
          <input type="text" name="phone" required placeholder="10-digit mobile number">
        </div>
        <div class="form-group">
          <label>Payment Method</label>
          <select name="payment_method">
            <option value="COD">Cash on Delivery</option>
            <option value="Card">Credit / Debit Card</option>
            <option value="UPI">UPI</option>
          </select>
        </div>
        <button type="submit" class="btn btn-block">Place Order</button>
      </form>
    </div>

    <div class="summary-box">
      <h3 style="margin-bottom:16px;">Order Summary</h3>
      <?php foreach ($rows as $r): ?>
        <div class="summary-row">
          <span><?php echo htmlspecialchars($r['name']); ?> × <?php echo $r['quantity']; ?></span>
          <span>₹<?php echo number_format($r['price'] * $r['quantity'], 2); ?></span>
        </div>
      <?php endforeach; ?>
      <div class="summary-row"><span>Delivery</span><span><?php echo $delivery == 0 ? 'FREE' : '₹' . number_format($delivery, 2); ?></span></div>
      <div class="summary-row total"><span>Total</span><span>₹<?php echo number_format($total, 2); ?></span></div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
