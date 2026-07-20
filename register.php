<?php
require_once 'config/db.php';
$page_title = "Sign Up";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name === '' || $email === '' || $password === '') {
        $error = "All fields are required.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $email_esc = $conn->real_escape_string($email);
        $check = $conn->query("SELECT id FROM users WHERE email = '$email_esc'");
        if ($check && $check->num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $name_esc = $conn->real_escape_string($name);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
            $stmt->bind_param("sss", $name_esc, $email_esc, $hash);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['role'] = 'customer';
                header("Location: index.php");
                exit;
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-card">
  <h2>Create Account</h2>
  <p class="sub">Join Elyzia to start shopping.</p>

  <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <label>Full Name</label>
      <input type="text" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
    </div>
    <div class="form-group">
      <label>Email Address</label>
      <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-block">Sign Up</button>
  </form>
  <p style="margin-top:16px; font-size:13.5px; text-align:center;">
    Already have an account? <a href="login.php" style="color:var(--gold); font-weight:600;">Log in</a>
  </p>
</div>

<?php include 'includes/footer.php'; ?>
