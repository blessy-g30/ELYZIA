<?php
require_once 'config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = $conn->query("SELECT p.*, c.name AS category_name FROM products p
                      LEFT JOIN categories c ON p.category_id = c.id
                      WHERE p.id = $id");
if (!$res || $res->num_rows == 0) {
    header("Location: index.php");
    exit;
}
$p = $res->fetch_assoc();
$page_title = $p['name'];

// ---- Gallery images ----
$gallery = [];
$gal_res = $conn->query("SELECT image FROM product_images WHERE product_id = $id");
if ($gal_res && $gal_res->num_rows > 0) {
    while ($g = $gal_res->fetch_assoc()) $gallery[] = $g['image'];
} else {
    $gallery[] = $p['image'];
}

// ---- Handle new review submission ----
$review_error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_text'])) {
    if (!isset($_SESSION['user_id'])) {
        $review_error = "Please log in to write a review.";
    } else {
        $rating = max(1, min(5, (int)$_POST['rating']));
        $text = trim($_POST['review_text']);
        if ($text !== '') {
            $stmt = $conn->prepare("INSERT INTO reviews (product_id, customer_name, rating, review_text) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis", $id, $_SESSION['user_name'], $rating, $text);
            $stmt->execute();
            header("Location: product.php?id=$id#reviews");
            exit;
        }
    }
}

// ---- Fetch reviews ----
$reviews = $conn->query("SELECT * FROM reviews WHERE product_id = $id ORDER BY created_at DESC");
$review_count = $reviews->num_rows;
$avg_rating = $p['rating'];
if ($review_count > 0) {
    $sum_res = $conn->query("SELECT AVG(rating) avg_r FROM reviews WHERE product_id = $id");
    $avg_rating = round($sum_res->fetch_assoc()['avg_r'], 1);
}

include 'includes/header.php';
?>

<div class="container">
  <div style="display:grid; grid-template-columns:1fr 1fr; gap:44px; margin:36px 0;">

    <!-- ============ IMAGE GALLERY (fully inline-styled, no CSS file dependency) ============ -->
    <div>
      <div style="width:100%; height:400px; background:#F3F0E8; border-radius:10px;
                  display:flex; align-items:center; justify-content:center; overflow:hidden; margin-bottom:12px;">
        <img id="pdMainImg" src="/elyzia/assets/images/<?php echo htmlspecialchars($gallery[0]); ?>"
             alt="<?php echo htmlspecialchars($p['name']); ?>"
             style="max-width:90%; max-height:90%; width:auto; height:auto; object-fit:contain; display:block;">
      </div>

      <?php if (count($gallery) > 1): ?>
      <div style="display:flex; gap:10px; flex-wrap:nowrap; overflow-x:auto;">
        <?php foreach ($gallery as $i => $img): ?>
          <div onclick="switchImage(this, '/elyzia/assets/images/<?php echo htmlspecialchars($img); ?>')"
               style="width:70px; height:70px; flex:0 0 70px; border-radius:8px;
                      border:2px solid <?php echo $i === 0 ? '#B8863B' : '#DCE3DC'; ?>;
                      overflow:hidden; cursor:pointer; background:#F3F0E8;
                      display:flex; align-items:center; justify-content:center;">
            <img src="/elyzia/assets/images/<?php echo htmlspecialchars($img); ?>" alt="thumb <?php echo $i+1; ?>"
            
                 style="max-width:90%; max-height:90%; width:auto; height:auto; object-fit:contain; display:block;">
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <!-- ============ END GALLERY ============ -->

    <div>
      <div style="color:#5C6B63; font-size:13px; margin-bottom:6px;">
        <?php echo htmlspecialchars($p['category_name'] ?? 'General'); ?>
      </div>
      <h1 style="font-size:28px; font-weight:600; margin-bottom:8px;"><?php echo htmlspecialchars($p['name']); ?></h1>
      <div class="rating">★ <?php echo $avg_rating; ?> · <?php echo $review_count; ?> review<?php echo $review_count == 1 ? '' : 's'; ?></div>
      <div style="font-size:30px; font-weight:700; margin:14px 0;">₹<?php echo number_format($p['price'], 2); ?></div>

      <?php if ($p['stock'] > 0): ?>
        <span class="stock-badge in-stock">In Stock (<?php echo $p['stock']; ?> left)</span>
      <?php else: ?>
        <span class="stock-badge out-stock">Out of Stock</span>
      <?php endif; ?>

      <p style="color:#5C6B63; margin:14px 0; font-size:14.5px;"><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>

      <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success">Added to cart successfully!</div>
      <?php endif; ?>

      <?php if ($p['stock'] > 0): ?>
        <form action="add_to_cart.php" method="POST" style="display:flex; gap:12px; align-items:center; margin-top:20px;">
          <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
          <input type="number" name="quantity" value="1" min="1" max="<?php echo $p['stock']; ?>" class="qty-input">
          <button type="submit" class="btn">Add to Cart</button>
          <button type="submit" name="buy_now" value="1" class="btn btn-outline">Buy Now</button>
        </form>
      <?php endif; ?>
    </div>

  </div>

  <!-- ================= REVIEWS SECTION ================= -->
  <div id="reviews" class="section-title"><h2>Customer <span>Reviews</span></h2></div>

  <div style="display:grid; grid-template-columns:2fr 1fr; gap:28px; align-items:start; margin-bottom:50px;">
    <div style="display:flex; flex-direction:column; gap:16px;">
      <?php if ($review_count === 0): ?>
        <div class="empty-state">No reviews yet. Be the first to review this product!</div>
      <?php else: ?>
        <?php while ($r = $reviews->fetch_assoc()): ?>
          <div class="review-card">
            <div class="rating">
              <?php for ($i = 0; $i < 5; $i++) echo $i < $r['rating'] ? '★' : '☆'; ?>
            </div>
            <p class="review-text">"<?php echo htmlspecialchars($r['review_text']); ?>"</p>
            <div class="review-name">— <?php echo htmlspecialchars($r['customer_name']); ?>
              <span style="color:var(--muted); font-weight:400;"> · <?php echo date("d M Y", strtotime($r['created_at'])); ?></span>
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>

    <div class="review-form-box">
      <h3 style="margin-bottom:14px;">Write a Review</h3>
      <?php if ($review_error): ?><div class="alert alert-error"><?php echo htmlspecialchars($review_error); ?></div><?php endif; ?>

      <?php if (isset($_SESSION['user_id'])): ?>
        <form method="POST">
          <div class="form-group">
            <label>Rating</label>
            <select name="rating">
              <option value="5">★★★★★ Excellent</option>
              <option value="4">★★★★☆ Good</option>
              <option value="3">★★★☆☆ Average</option>
              <option value="2">★★☆☆☆ Below Average</option>
              <option value="1">★☆☆☆☆ Poor</option>
            </select>
          </div>
          <div class="form-group">
            <label>Your Review</label>
            <textarea name="review_text" rows="4" required placeholder="Share your experience with this product..."></textarea>
          </div>
          <button type="submit" class="btn btn-block">Submit Review</button>
        </form>
      <?php else: ?>
        <p class="sub">Please <a href="login.php" style="color:var(--gold); font-weight:600;">log in</a> to write a review.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
function switchImage(el, src){
  document.getElementById('pdMainImg').src = src;
  el.parentElement.querySelectorAll('div').forEach(function(t){
    t.style.borderColor = '#DCE3DC';
  });
  el.style.borderColor = '#B8863B';
}
</script>

<?php include 'includes/footer.php'; ?>