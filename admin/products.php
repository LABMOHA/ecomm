<?php
require_once '../includes/config.php';



$message = '';
$error = '';

//  DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {

        $stmt = $pdo->prepare("SELECT name FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();

        if ($product) {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $message = "Product '{$product['name']}' deleted successfully!";
        } else {
            $error = 'Product not found.';
        }
    } catch (PDOException $e) {
        $error = 'Cannot delete product: It may have existing orders.';
    }
}

// Handle ADD/UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $image_url = trim($_POST['image_url'] ?? '');

    // Validation
    if (empty($name)) {
        $error = 'Product name is required.';
    } elseif ($price <= 0) {
        $error = 'Price must be greater than 0.';
    } elseif ($stock < 0) {
        $error = 'Stock cannot be negative.';
    } else {
        try {
            if ($id > 0) {
                // UPDATE existing product
                $stmt = $pdo->prepare("
                    UPDATE products 
                    SET name = :name, 
                        description = :description, 
                        price = :price, 
                        stock = :stock, 
                        image_url = :image_url 
                    WHERE id = :id
                ");
                $result = $stmt->execute([
                    ':id' => $id,
                    ':name' => $name,
                    ':description' => $description,
                    ':price' => $price,
                    ':stock' => $stock,
                    ':image_url' => $image_url
                ]);

                if ($result) {
                    $message = "Product '$name' updated successfully! (Price: $$price, Stock: $stock units)";
                } else {
                    $error = 'Failed to update product.';
                }
            } else {
                // ADD new product
                $stmt = $pdo->prepare("
                    INSERT INTO products (name, description, price, stock, image_url) 
                    VALUES (:name, :description, :price, :stock, :image_url)
                ");
                $result = $stmt->execute([
                    ':name' => $name,
                    ':description' => $description,
                    ':price' => $price,
                    ':stock' => $stock,
                    ':image_url' => $image_url
                ]);

                if ($result) {
                    $message = "Product '$name' added successfully! (Price: $$price, Stock: $stock units)";
                } else {
                    $error = 'Failed to add product.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Get product for editing
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $edit_product = $stmt->fetch();

    if (!$edit_product) {
        $error = 'Product not found.';
    }
}

// Get all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="dashboard.php" class="text-2xl font-bold text-blue-600">Admin Panel</a>

                </div>
                <div class="flex items-center space-x-4">

                    <a href="../logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Manage Products</h1>
            <div class="text-gray-600">Total Products: <span class="font-bold"><?php echo count($products); ?></span></div>
        </div>

        <!-- Success Message -->
        <?php if ($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                <p class="font-bold">✓ Success</p>
                <p><?php echo sanitize_output($message); ?></p>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <p class="font-bold">✗ Error</p>
                <p><?php echo sanitize_output($error); ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Add/Edit Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6 sticky top-4">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <?php if ($edit_product): ?>
                            <span class="text-orange-600">✏️ Edit Product #<?php echo $edit_product['id']; ?></span>
                        <?php else: ?>
                            <span class="text-green-600">➕ Add New Product</span>
                        <?php endif; ?>
                    </h2>

                    <form method="POST" action="products.php" class="space-y-4">
                        <?php if ($edit_product): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
                            <div class="bg-blue-50 border border-blue-200 rounded p-3 text-sm text-blue-700">
                                <strong>Editing:</strong> You can update the name, price, stock, description, and image.
                            </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name"
                                value="<?php echo $edit_product ? sanitize_output($edit_product['name']) : ''; ?>"
                                placeholder="e.g., Wireless Mouse"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Description</label>
                            <textarea name="description" rows="3"
                                placeholder="Product description..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo $edit_product ? sanitize_output($edit_product['description']) : ''; ?></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">
                                    Price (Dh) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="price" step="0.01" min="0.01"
                                    value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>"
                                    placeholder="0.00"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">
                                    Stock <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="stock" min="0"
                                    value="<?php echo $edit_product ? $edit_product['stock'] : '0'; ?>"
                                    placeholder="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Image URL</label>
                            <input type="url" name="image_url"
                                value="<?php echo $edit_product ? sanitize_output($edit_product['image_url']) : 'https://via.placeholder.com/300x300?text=Product'; ?>"
                                placeholder="https://example.com/image.jpg"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Use placeholder or external image URL</p>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="submit"
                                class="flex-1 bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600 font-semibold transition">
                                <?php echo $edit_product ? '💾 Update Product' : '➕ Add Product'; ?>
                            </button>
                            <?php if ($edit_product): ?>
                                <a href="products.php"
                                    class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold transition">
                                    Cancel
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                        <h2 class="text-xl font-bold">All Products</h2>
                        <p class="text-sm text-blue-100">Manage your product inventory</p>
                    </div>

                    <?php if (empty($products)): ?>
                        <div class="p-12 text-center">
                            <div class="text-6xl mb-4">📦</div>
                            <p class="text-gray-500 text-lg mb-2">No products yet</p>
                            <p class="text-gray-400 text-sm">Add your first product using the form on the left</p>
                        </div>
                    <?php else: ?>
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($products as $product): ?>
                                <div class="p-4 hover:bg-gray-50 transition">
                                    <div class="flex items-center gap-4">
                                        <!-- Product Image -->
                                        <img src="<?php echo sanitize_output($product['image_url']); ?>"
                                            alt="<?php echo sanitize_output($product['name']); ?>"
                                            class="w-24 h-24 object-cover rounded-lg border-2 border-gray-200">

                                        <!-- Product Info -->
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <h3 class="font-bold text-lg text-gray-800">
                                                        <?php echo sanitize_output($product['name']); ?>
                                                    </h3>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        <?php echo sanitize_output(substr($product['description'], 0, 80)); ?>...
                                                    </p>
                                                </div>
                                                <span class="text-xs text-gray-400">#<?php echo $product['id']; ?></span>
                                            </div>

                                            <div class="flex items-center gap-4 mt-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-semibold text-gray-500">PRICE:</span>
                                                    <span class="text-lg font-bold text-green-600">
                                                        <?php echo number_format($product['price'], 2); ?> Dh
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-semibold text-gray-500">STOCK:</span>
                                                    <span class="px-3 py-1 rounded-full text-sm font-bold
                                                        <?php
                                                        if ($product['stock'] > 50) {
                                                            echo 'bg-green-100 text-green-700';
                                                        } elseif ($product['stock'] > 10) {
                                                            echo 'bg-yellow-100 text-yellow-700';
                                                        } elseif ($product['stock'] > 0) {
                                                            echo 'bg-orange-100 text-orange-700';
                                                        } else {
                                                            echo 'bg-red-100 text-red-700';
                                                        }
                                                        ?>">
                                                        <?php echo $product['stock']; ?> units
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex flex-col gap-2">

                                            <a href="products.php?edit=<?php echo $product['id']; ?>"
                                                class="px-5 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm font-semibold text-center transition">
                                                Edit
                                            </a>
                                            <a href="products.php?delete=<?php echo $product['id']; ?>"
                                                onclick="return confirm('⚠️ Are you sure you want to delete &quot;<?php echo sanitize_output($product['name']); ?>&quot;?\n\nThis action cannot be undone!')"
                                                class="px-5 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm font-semibold text-center transition">
                                                Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>