<?php
require_once '../includes/config.php';
session_start();

$cart ;
$cart_items = $_SESSION['cart'];
$total = 0;


//$ids = array_keys($cart);
//$placeholders = str_repeat('?,', count($ids) - 1) . '?';
if ($_SESSION['user_id']) {
    // $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    // $stmt->execute([':id' => $_SESSION['user_id']]);
    
    //  $stmt = $pdo->prepare("SELECT * FROM order_items WHERE product_id = :id");
    // $stmt->execute([':id' => $_SESSION['product_id']]);



    //$orders = $stmt->fetchAll();

    // foreach ($products as $product) {
    //     $quantity = $cart[$product['id']];
    //     $subtotal = $product['price'] * $quantity;
    //     $total += $subtotal;

    //     $cart_items[] = [
    //         'product' => $product,
    //         'quantity' => $quantity,
    //         'subtotal' => $subtotal
    //     ];
    // }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold text-blue-600">E-Shop</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-700 hover:text-blue-600">Continue Shopping</a>
                    <?php //if (is_logged_in()): 
                    ?>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
                    <?php //else: 
                    ?>
                    
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Shopping Cart</h1>

        <?php if (empty($cart_items)): ?>
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-500 text-lg mb-4">Your cart is empty</p>
                <a href="index.php" class="inline-block bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                    Start Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left">Product</th>
                            <th class="px-6 py-3 text-center">Price</th>
                            <th class="px-6 py-3 text-center">Quantity</th>
                            <th class="px-6 py-3 text-center">Subtotal</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="<?php echo sanitize_output($item['image']); ?>"
                                            alt="<?php echo sanitize_output($item['name']); ?>"
                                            class="w-16 h-16 object-cover rounded mr-4">
                                        <div>
                                            <div class="font-semibold"><?php echo sanitize_output($item['name']); ?></div>
                                            <div class="text-sm text-gray-500">Stock: <?php echo $item['stock']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">$<?php echo number_format($item['price'], 2); ?></td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="cart_action.php" class="inline">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['stock']; ?>"
                                            min="0" max="<?php echo $item['stock']; ?>"
                                            class="w-20 px-2 py-1 border rounded text-center">
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold"><?php echo number_format($item['price'], 2); ?> dh</td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="cart_action.php" class="inline">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="bg-gray-50 px-6 py-4">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-xl font-bold">Total:</span>
                        <span class="text-2xl font-bold text-blue-600"> <?php echo number_format($total, 2); ?> Dh</span>
                    </div>

                    <?php // if (is_logged_in()): 
                    ?>
                    <a href="checkout.php" class="block w-full bg-green-500 text-white text-center py-3 rounded-lg hover:bg-green-600 font-semibold">
                        Proceed to Checkout
                    </a>
                    
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>