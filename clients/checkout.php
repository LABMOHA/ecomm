<?php
require_once '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$total   = $_POST['total'];
$cart    = json_decode($_POST["cart"], true);

$status = "completed";
var_dump($cart);

// Insert order
$insert = $pdo->prepare("
    INSERT INTO orders (user_id, total_amount, status)
    VALUES (?, ?, ?)
");
$insert->execute([$user_id, $total, $status]);

// Get last order id of this user
$stmt = $pdo->prepare("
    SELECT id 
    FROM orders
    WHERE user_id = :user_id
    ORDER BY created_at DESC
    LIMIT 1
");
$stmt->execute(['user_id' => $user_id]);

$order_data = $stmt->fetch(PDO::FETCH_ASSOC);
$order_id = $order_data['id'];

// Insert items
foreach ($cart as $value) {
    $insertItem = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");

    $insertItem->execute([
        $order_id,
        $value["id"],
        $value["quantity"],
        $value["price"]
    ]);
}

header("Location: index.php");
exit;
