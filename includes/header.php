<?php
// Expects $conn and session already started via config/db.php
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $res = $conn->query("SELECT SUM(quantity) AS total FROM cart WHERE user_id = $uid");
    if ($res && $row = $res->fetch_assoc()) {
        $cart_count = $row['total'] ? $row['total'] : 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? $page_title . " - Elyzia" : "Elyzia - Shop everything you love"; ?></title>
<link rel="stylesheet" href="/elyzia/assets/css/style.css">
</head>
<body>

<div class="topbar">
  <div class="container">
    <span>Free delivery on orders above ₹999</span>
    <span>Helpline: +91 98765 43210</span>
  </div>
</div>

<header class="site-header">
  <div class="container header-row">
    <a href="/elyzia/index.php" class="brand">Ely<span>zia</span></a>

    <form action="/elyzia/index.php" method="GET" class="search-form">
      <input type="text" name="q" placeholder="Search for products, brands and more"
             value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
      <button type="submit">Search</button>
    </form>

    <div class="header-actions">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span>Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="/elyzia/orders.php">My Orders</a>
        <a href="/elyzia/logout.php">Logout</a>
      <?php else: ?>
        <a href="/elyzia/login.php">Login</a>
        <a href="/elyzia/register.php">Sign Up</a>
      <?php endif; ?>
      <a href="/elyzia/cart.php">Cart<span class="cart-count"><?php echo $cart_count; ?></span></a>
    </div>
  </div>

  <nav class="category-nav">
    <div class="container">
      <?php
      $cat_res = $conn->query("SELECT * FROM categories ORDER BY name");
      while ($cat = $cat_res->fetch_assoc()) {
          echo '<a href="/elyzia/index.php?category=' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</a>';
      }
      ?>
    </div>
  </nav>
</header>
