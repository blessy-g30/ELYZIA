<?php
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: cart.php"); exit; }

$user_id = $_SESSION['user_id'];
$address = $conn->real_escape_string($_POST['address']);
$phone = $conn->real_escape_string($_POST['phone']);

$sql = "SELECT c.product_id, c.quantity, p.price, p.stock FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $user_id";
$items = $conn->query($sql);
$rows = [];
$total = 0;
while ($row = $items->fetch_assoc()) {
    $rows[] = $row;
    $total += $row['price'] * $row['quantity'];
}
if (count($rows) === 0) { header("Location: cart.php"); exit; }
if ($total <= 999) $total += 49;

$conn->begin_transaction();
try {
    $conn->query("INSERT INTO orders (user_id, total_amount, address, phone, status)
                  VALUES ($user_id, $total, '$address', '$phone', 'placed')");
    $order_id = $conn->insert_id;

    foreach ($rows as $r) {
        $pid = (int)$r['product_id'];
        $qty = (int)$r['quantity'];
        $price = (float)$r['price'];
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price)
                      VALUES ($order_id, $pid, $qty, $price)");
        $conn->query("UPDATE products SET stock = GREATEST(stock - $qty, 0) WHERE id = $pid");
    }

    $conn->query("DELETE FROM cart WHERE user_id = $user_id");
    $conn->commit();

    header("Location: order_success.php?order_id=$order_id");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    die("Something went wrong while placing your order. Please try again.");
}
