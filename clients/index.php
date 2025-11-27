<?php
session_start();
require_once "../includes/config.php";



// Fetch products
try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $sql = "SELECT * FROM products";
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll();


    try {
        // Get search term (if any)
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Base query
        $sql = "SELECT * FROM products";
        $params = [];

        // If searching, add WHERE condition
        if (!empty($search)) {
            $sql .= " WHERE name LIKE :search";
            $params[':search'] = "%$search%";
        }



        // Prepare and execute
        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            // Handle integers separately if you use pagination
            if ($key === ':limit' || $key === ':offset') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        die("Database query failed: " . $e->getMessage());
    }



    $stmt->execute();
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
}


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Store</title>
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

                <?php if (isset($_SESSION["user_id"])): ?>
                    <div class="flex items-center space-x-4">
                        <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Logout
                        </a>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" value="<?php echo sanitize_output($search); ?>"
                    placeholder="Search products..."
                    class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                    Search
                </button>
                <?php if ($search): ?>
                    <a href="index.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                        Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img src="<?php echo $product['image_url']; ?>"
                        alt="<?php echo sanitize_output($product['name']); ?>"
                        class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2"><?php echo sanitize_output($product['name']); ?></h3>
                        <p class="text-gray-600 text-sm mb-3"><?php echo sanitize_output(substr($product['description'], 0, 80)) . '...'; ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-blue-600"><?php echo number_format($product['price'], 2); ?> dh</span>
                            <span class="text-sm text-gray-500">Stock: <?php echo $product['stock']; ?></span>
                        </div>
                        <form method="POST" action="cart_action.php" class="mt-4">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="name" value="<?php echo $product['name']; ?>">
                            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                            <input type="hidden" name="stock" value="<?php echo $product['stock']; ?>">
                            <input type="hidden" name="image_url" value="<?php echo $product['image_url']; ?>">

                            <button type="submit"
                                class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 disabled:bg-gray-400"
                                   > Add to cart

                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-12">
                <p class="text-gray-500 text-xl">No products found.</p>
            </div>
        <?php endif; ?>

        <!-- Pagination -->

    </div>
</body>

</html>