<?php
require_once '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id'] ?? 0);
$total_amount = 0;
$status =

    $cart = get_cart();

switch ($action) {
    case 'add':
        if ($product_id > 0) {


            // Verify product exists and has stock
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->execute([':id' => $product_id]);
            $product = $stmt->fetch();


            // verify orders
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ");
            $stmt->execute([':user_id' => $user_id]);
            $orders = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product && $product['stock'] > 0) {
                if ($orders && $orders['status'] == 'pending') {
                    $stmt = $pdo->prepare("
                    INSERT INTO orders (user_id, total_amount) 
                    VALUES (:user_id, :total_amount)");
                    $result = $stmt->execute([
                        ':user_id' => $user_id,
                        ':total_amount' => $total_amount
                    ]);
                }

                $_SESSION['product_id'] = $product_id;
                $stmt = $pdo->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, price) 
                    VALUES (:order_id, :product_id, :quantity, :price)
                ");
                $result = $stmt->execute([
                    ':order_id' => $orders['id'],
                    ':product_id' => $product_id,
                    ':quantity' => 1,
                    ':price' => $product['price']
                ]);
            }
        }
        break;

        // case 'remove':
        //     if (isset($cart[$product_id])) {
        //         unset($cart[$product_id]);
        //         save_cart($cart);
        //     }
        //     break;

        // case 'update':
        //     $quantity = intval($_POST['quantity'] ?? 0);
        //     if ($quantity > 0 && isset($cart[$product_id])) {
        //         $cart[$product_id] = $quantity;
        //         save_cart($cart);
        //     } elseif ($quantity == 0 && isset($cart[$product_id])) {
        //         unset($cart[$product_id]);
        //         save_cart($cart);
        //     }
        //     break;
}

redirect(isset($_POST['redirect']) ? $_POST['redirect'] : 'cart.php');
