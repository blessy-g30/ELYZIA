<?php
// Include after config/db.php is loaded. Ensures only admins can view admin pages.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
