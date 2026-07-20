<?php $current = basename($_SERVER['PHP_SELF']); ?>
<div class="admin-sidebar">
  <h2>Ely<span style="color:var(--gold-light);">zia</span> Admin</h2>
  <a href="dashboard.php" class="<?php echo $current=='dashboard.php'?'active':''; ?>">Dashboard</a>
  <a href="manage_products.php" class="<?php echo $current=='manage_products.php'?'active':''; ?>">Manage Products</a>
  <a href="add_product.php" class="<?php echo $current=='add_product.php'?'active':''; ?>">Add Product</a>
  <a href="orders.php" class="<?php echo $current=='orders.php'?'active':''; ?>">Orders</a>
  <a href="../index.php">View Store</a>
  <a href="../logout.php">Logout</a>
</div>
