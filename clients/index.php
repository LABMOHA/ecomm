<?php
session_start();
require_once "../includes/config.php";

try {
    $sql = "SELECT * FROM products";
    $stmt = $pdo->query($sql);
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
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold text-blue-600">E-Shop</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="cart.php" class="text-gray-700 hover:text-blue-600">
                        🛒 Cart (<span id="cart-count">0</span>)
                    </a>
                    <?php if (isset($_SESSION["user_id"])): ?>
                        <a href="../logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Logout
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                        class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h3>
                        <p class="text-gray-600 text-sm mb-3">
                            <?php echo htmlspecialchars(substr($product['description'], 0, 80)); ?>
                        </p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xl font-bold text-blue-600">
                                <?php echo number_format($product['price'], 2); ?> dh
                            </span>
                            <span class="text-sm text-gray-500">
                                Stock: <?php echo $product['stock']; ?>
                            </span>
                        </div>

                        <!-- ✅ BEST: Using data attributes -->
                        <button type="button"
                            class="add-to-cart-btn w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 disabled:bg-gray-400"
                            data-id="<?php echo $product['id']; ?>"
                            data-name="<?php echo htmlspecialchars($product['name']); ?>"
                            data-price="<?php echo $product['price']; ?>"
                            data-stock="<?php echo $product['stock']; ?>"
                            data-image="<?php echo htmlspecialchars($product['image_url']); ?>"
                            <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>>
                            <?php echo $product['stock'] > 0 ? '🛒 Add to Cart' : 'Out of Stock'; ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>



        // Get cart from localStorage
        function getCart() {
            const cart = localStorage.getItem('cart');
            return cart ? JSON.parse(cart) : {};
        }

        // Save cart to localStorage
        function saveCart(cart) {
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
        }

        // Add to cart function
        function addToCart(id, name, price, stock, image) {
            const cart = getCart();

            if (cart[id]) {
                if (cart[id].quantity >= parseInt(stock)) {
                    alert('⚠️ Maximum stock reached: ' + stock);
                    return;
                }
                cart[id].quantity++;
            } else {
                cart[id] = {
                    id: parseInt(id),
                    name: name,
                    price: parseFloat(price),
                    stock: parseInt(stock),
                    image: image,
                    quantity: 1
                };
            }

            saveCart(cart);
            alert('✓ ' + name + ' added to cart!');
        }

        // Update cart count
        function updateCartCount() {
            const cart = getCart();
            let count = 0;
            for (let id in cart) {
                count += cart[id].quantity;
            }
            document.getElementById('cart-count').textContent = count;
        }

        // Add event listeners to all add-to-cart buttons
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();

            document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    addToCart(
                        this.dataset.id,
                        this.dataset.name,
                        this.dataset.price,
                        this.dataset.stock,
                        this.dataset.image
                    );
                });
            });
        });
    </script>
</body>

</html>