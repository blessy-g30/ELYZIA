<?php
require_once 'config/db.php';
$page_title = "Login";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $email_esc = $conn->real_escape_string($email);

    $res = $conn->query("SELECT * FROM users WHERE email = '$email_esc'");
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
                exit;
            }
            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No account found with this email.";
    }
}

include 'includes/header.php';
?>

<div class="auth-card">
  <h2>Welcome Back</h2>
  <p class="sub">Log in to continue shopping on Elyzia.</p>

  <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <label>Email Address</label>
      <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-block">Log In</button>
  </form>
  <p style="margin-top:16px; font-size:13.5px; text-align:center;">
    New to Elyzia? <a href="register.php" style="color:var(--gold); font-weight:600;">Create an account</a>
  </p>
  <p style="margin-top:6px; font-size:12px; text-align:center; color:var(--muted);">
    Admin demo login: admin@elyzia.com / admin123
  </p>
</div>

<?php include 'includes/footer.php'; ?>
