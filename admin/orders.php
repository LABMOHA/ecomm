<?php
require_once '../includes/config.php';

// if (!is_admin()) {
//     redirect('../login.php');
// }

$message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];

    $allowed_statuses = ['pending', 'processing', 'completed', 'cancelled'];

    if ($order_id > 0 && in_array($new_status, $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $new_status, ':id' => $order_id]);
        $message = 'Order status updated successfully!';
    }
}

// Get all orders
$stmt = $pdo->query("
    SELECT o.*, u.full_name, u.email, COUNT(oi.id) as item_count
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleOrderDetails(orderId) {
            const details = document.getElementById('order-details-' + orderId);
            details.classList.toggle('hidden');
        }
    </script>
</head>

<body class="bg-gray-50">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="dashboard.php" class="text-2xl font-bold text-blue-600">Admin Panel</a>
                    <a href="products.php" class="text-gray-700 hover:text-blue-600">Products</a>
                    <a href="orders.php" class="text-blue-600 font-semibold">Orders</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="../index.php" class="text-gray-700 hover:text-blue-600">View Store</a>
                    <a href="../logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Order Management</h1>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo sanitize_output($message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-500 text-lg">No orders found.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($orders as $order): ?>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <!-- Order Header -->
                        <div class="bg-gray-50 px-6 py-4 border-b cursor-pointer hover:bg-gray-100"
                            onclick="toggleOrderDetails(<?php echo $order['id']; ?>)">
                            <div class="flex flex-wrap justify-between items-center">
                                <div class="flex items-center space-x-4">
                                    <span class="font-bold text-lg">Order #<?php echo $order['id']; ?></span>
                                    <span class="text-gray-500">
                                        <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <div class="font-medium"><?php echo sanitize_output($order['full_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo sanitize_output($order['email']); ?></div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500"><?php echo $order['item_count']; ?> items</div>
                                        <div class="font-bold text-lg">$<?php echo number_format($order['total_amount'], 2); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Details (Toggle) -->
                        <div id="order-details-<?php echo $order['id']; ?>" class="hidden">
                            <div class="px-6 py-4">
                                <?php
                                // Get order items
                                $stmt = $pdo->prepare("
                                    SELECT oi.*, p.name, p.image_url 
                                    FROM order_items oi
                                    JOIN products p ON oi.product_id = p.id
                                    WHERE oi.order_id = :order_id
                                ");
                                $stmt->execute([':order_id' => $order['id']]);
                                $items = $stmt->fetchAll();
                                ?>

                                <h3 class="font-semibold mb-3">Order Items:</h3>
                                <div class="space-y-2 mb-4">
                                    <?php foreach ($items as $item): ?>
                                        <div class="flex items-center justify-between border-b pb-2">
                                            <div class="flex items-center">
                                                <img src="<?php echo sanitize_output($item['image_url']); ?>"
                                                    alt="<?php echo sanitize_output($item['name']); ?>"
                                                    class="w-12 h-12 object-cover rounded mr-3">
                                                <div>
                                                    <div class="font-medium"><?php echo sanitize_output($item['name']); ?></div>
                                                    <div class="text-sm text-gray-500">
                                                        $<?php echo number_format($item['price'], 2); ?> × <?php echo $item['quantity']; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="font-semibold">
                                                $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Update Status Form -->
                                <form method="POST" action="" class="flex items-center gap-3">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <label class="font-semibold">Status:</label>
                                    <select name="status" class="px-3 py-2 border rounded-lg">
                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status"
                                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                        Update Status
                                    </button>
                                    <span class="ml-auto px-3 py-1 rounded-full text-sm font-semibold
                                        <?php
                                        switch ($order['status']) {
                                            case 'pending':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'processing':
                                                echo 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'completed':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            case 'cancelled':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                        }
                                        ?>">
                                        Current: <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>