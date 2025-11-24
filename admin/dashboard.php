<?php
require_once '../includes/config.php';


    //redirect('login.php');


$message = '';

// Handle stock update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $product_id = intval($_POST['product_id']);
    $new_stock = intval($_POST['stock']);

    if ($product_id > 0 && $new_stock >= 0) {
        $stmt = $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id");
        $stmt->execute([':stock' => $new_stock, ':id' => $product_id]);
        $message = 'Stock updated successfully!';
    }
}

// Get all products
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND name LIKE :search";
    $params[':search'] = "%$search%";
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="dashboard.php" class="text-2xl font-bold text-blue-600">Admin Panel</a>
                    <a href="products.php" class="text-blue-600 font-semibold">Products</a>
                    <a href="orders.php" class="text-gray-700 hover:text-blue-600">Orders</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="../index.php" class="text-gray-700 hover:text-blue-600">View Store</a>
                    <a href="../logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Product Management</h1>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo sanitize_output($message); ?>
            </div>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="mb-6">
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" value="<?php echo sanitize_output($search); ?>"
                    placeholder="Search products..."
                    class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                    Search
                </button>
                <?php if ($search): ?>
                    <a href="products.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                        Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Update Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($products as $product): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="<?php echo sanitize_output($product['image_url']); ?>"
                                            alt="<?php echo sanitize_output($product['name']); ?>"
                                            class="w-12 h-12 object-cover rounded mr-3">
                                        <div>
                                            <div class="font-medium"><?php echo sanitize_output($product['name']); ?></div>
                                            <div class="text-sm text-gray-500">ID: <?php echo $product['id']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold">$<?php echo number_format($product['price'], 2); ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                                        <?php echo $product['stock'] > 10 ? 'bg-green-100 text-green-800' : ($product['stock'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo $product['stock']; ?> units
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="" class="flex items-center gap-2">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="number" name="stock" value="<?php echo $product['stock']; ?>"
                                            min="0" class="w-24 px-2 py-1 border rounded">
                                        <button type="submit" name="update_stock"
                                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                                            Update
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="../index.php" class="text-blue-600 hover:underline text-sm">View in Store</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-12">
                <p class="text-gray-500 text-xl">No products found.</p>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>