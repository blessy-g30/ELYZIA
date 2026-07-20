<?php
require_once '../config/db.php';
require_once 'auth_check.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: manage_products.php");
    exit;
}

$products = $conn->query("SELECT p.*, c.name AS category_name FROM products p
                           LEFT JOIN categories c ON p.category_id = c.id
                           ORDER BY p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Products - Elyzia Admin</title>
<link rel="stylesheet" href="/elyzia/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include 'sidebar.php'; ?>
  <div class="admin-main">
    <div class="section-title" style="margin-top:0;">
      <h2>Manage Products</h2>
      <a href="add_product.php" class="btn btn-sm">+ Add Product</a>
    </div>

    <table class="admin-table">
      <tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
      <?php while ($p = $products->fetch_assoc()): ?>
        <tr>
          <td>#<?php echo $p['id']; ?></td>
          <td><?php echo htmlspecialchars($p['name']); ?></td>
          <td><?php echo htmlspecialchars($p['category_name'] ?? '-'); ?></td>
          <td>₹<?php echo number_format($p['price'], 2); ?></td>
          <td><?php echo $p['stock']; ?></td>
          <td>
            <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
            <a href="manage_products.php?delete=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('Delete this product?');">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body>
</html>
