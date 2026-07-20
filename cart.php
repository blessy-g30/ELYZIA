<?php
require_once 'config/db.php';
$page_title = "Your Cart";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

$sql = "SELECT c.id AS cart_id, c.quantity, p.* FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $user_id";
$items = $conn->query($sql);

$subtotal = 0;
$rows = [];
if ($items) {
    while ($row = $items->fetch_assoc()) {
        $rows[] = $row;
        $subtotal += $row['price'] * $row['quantity'];
    }
}
$delivery = $subtotal > 999 || $subtotal == 0 ? 0 : 49;
$total = $subtotal + $delivery;

include 'includes/header.php';
?>

<div class="container">
  <div class="section-title"><h2>Your <span>Cart</span></h2></div>

  <?php if (count($rows) === 0): ?>
    <div class="empty-state">
      <p>Your cart is empty.</p>
      <a href="index.php" class="btn" style="margin-top:16px;">Continue Shopping</a>
    </div>
  <?php else: ?>
    <div style="display:grid; grid-template-columns:2fr 1fr; gap:28px; align-items:start;">
      <table class="cart-table">
        <tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['name']); ?></td>
            <td>₹<?php echo number_format($r['price'], 2); ?></td>
            <td>
              <form action="update_cart.php" method="POST" style="display:flex; gap:8px;">
                <input type="hidden" name="cart_id" value="<?php echo $r['cart_id']; ?>">
                <input type="number" name="quantity" value="<?php echo $r['quantity']; ?>" min="1" max="<?php echo $r['stock']; ?>" class="qty-input">
                <button type="submit" class="btn btn-outline btn-sm">Update</button>
              </form>
            </td>
            <td>₹<?php echo number_format($r['price'] * $r['quantity'], 2); ?></td>
            <td>
              <form action="remove_from_cart.php" method="POST">
                <input type="hidden" name="cart_id" value="<?php echo $r['cart_id']; ?>">
                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>

      <div class="summary-box">
        <h3 style="margin-bottom:16px;">Order Summary</h3>
        <div class="summary-row"><span>Subtotal</span><span>₹<?php echo number_format($subtotal, 2); ?></span></div>
        <div class="summary-row"><span>Delivery</span><span><?php echo $delivery == 0 ? 'FREE' : '₹' . number_format($delivery, 2); ?></span></div>
        <div class="summary-row total"><span>Total</span><span>₹<?php echo number_format($total, 2); ?></span></div>
        <a href="checkout.php" class="btn btn-block" style="margin-top:16px; text-align:center;">Proceed to Checkout</a>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
