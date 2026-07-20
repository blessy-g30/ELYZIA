<?php
require_once '../config/db.php';
require_once 'auth_check.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = $conn->query("SELECT * FROM products WHERE id = $id");
if (!$res || $res->num_rows === 0) { header("Location: manage_products.php"); exit; }
$p = $res->fetch_assoc();
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
$error = ""; $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category_id = (int)$_POST['category_id'];
    $image = $p['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array(strtolower($ext), $allowed)) {
            $image = 'prod_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/' . $image);
        }
    }

    $stmt = $conn->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, image=? WHERE id=?");
    $stmt->bind_param("issdisi", $category_id, $name, $description, $price, $stock, $image, $id);
    if ($stmt->execute()) {
        $success = "Product updated successfully!";
        $p = array_merge($p, ['name'=>$name,'description'=>$description,'price'=>$price,'stock'=>$stock,'category_id'=>$category_id,'image'=>$image]);
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Product - Elyzia Admin</title>
<link rel="stylesheet" href="/elyzia/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include 'sidebar.php'; ?>
  <div class="admin-main">
    <div class="section-title" style="margin-top:0;"><h2>Edit Product</h2></div>

    <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <div class="auth-card" style="margin:0; max-width:560px;">
      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label>Product Name</label>
          <input type="text" name="name" required value="<?php echo htmlspecialchars($p['name']); ?>">
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="4"><?php echo htmlspecialchars($p['description']); ?></textarea>
        </div>
        <div class="form-group">
          <label>Category</label>
          <select name="category_id">
            <?php while ($c = $categories->fetch_assoc()): ?>
              <option value="<?php echo $c['id']; ?>" <?php echo $c['id']==$p['category_id']?'selected':''; ?>>
                <?php echo htmlspecialchars($c['name']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Price (₹)</label>
          <input type="number" step="0.01" name="price" required value="<?php echo $p['price']; ?>">
        </div>
        <div class="form-group">
          <label>Stock Quantity</label>
          <input type="number" name="stock" required value="<?php echo $p['stock']; ?>">
        </div>
        <div class="form-group">
          <label>Replace Image (optional)</label>
          <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-block">Update Product</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
