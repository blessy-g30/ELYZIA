<?php
require_once '../config/db.php';
require_once 'auth_check.php';

$error = ""; $success = "";
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category_id = (int)$_POST['category_id'];
    $image = 'no-image.png';

    // handle image upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array(strtolower($ext), $allowed)) {
            $image = 'prod_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/' . $image);
        }
    }

    if ($name === '' || $price <= 0) {
        $error = "Please provide a valid product name and price.";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (category_id, name, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdis", $category_id, $name, $description, $price, $stock, $image);
        if ($stmt->execute()) {
            $success = "Product added successfully!";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Product - Elyzia Admin</title>
<link rel="stylesheet" href="/elyzia/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include 'sidebar.php'; ?>
  <div class="admin-main">
    <div class="section-title" style="margin-top:0;"><h2>Add New Product</h2></div>

    <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <div class="auth-card" style="margin:0; max-width:560px;">
      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label>Product Name</label>
          <input type="text" name="name" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="4"></textarea>
        </div>
        <div class="form-group">
          <label>Category</label>
          <select name="category_id">
            <?php while ($c = $categories->fetch_assoc()): ?>
              <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Price (₹)</label>
          <input type="number" step="0.01" name="price" required>
        </div>
        <div class="form-group">
          <label>Stock Quantity</label>
          <input type="number" name="stock" required>
        </div>
        <div class="form-group">
          <label>Product Image (optional)</label>
          <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-block">Add Product</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
