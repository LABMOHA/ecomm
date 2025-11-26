<?php
require_once '../includes/config.php';


//redirect('login.php');


$message = '';

// Handle stock update




$sql = "SELECT users.full_name, orders.* 
        FROM users 
        JOIN orders ON users.id = orders.user_id";

$stmt = $pdo->query($sql);
$User_orders = $stmt->fetchAll();
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
                    <a href="products.php" class="text-blue-600 font-semibold">Products manger</a>
                    <!-- <a href="orders.php" class="text-gray-700 hover:text-blue-600">Orders</a> -->
                </div>
                <div class="flex items-center space-x-4">
                    <a href="../logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Home</h1>
        </div>





        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">total amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($User_orders as $User_order): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="font-medium">ID: <?php echo $User_order['user_id']; ?></div>
                                    </div>
            </div>
            </td>
            <td class="px-6 py-4 font-semibold"><?php echo $User_order['full_name'];; ?></td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-full text-sm font-semibold">
                    <?php echo $User_order['total_amount']; ?> Dh
                </span>
            </td>
            <td class="px-6 py-4">
                <form method="POST" action="" class="flex items-center gap-2">
                    <input type="hidden" name="product_id" value="<?php echo $User_order['id']; ?>">

                    <button type="submit" name="update_stock"
                        class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                        Show_details
                    </button>
                </form>
            </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
        </table>
        </div>
    </div>

    <?php if (empty($User_orders)): ?>
        <div class="text-center py-12">
            <p class="text-gray-500 text-xl">No products found.</p>
        </div>
    <?php endif; ?>
    </div>
</body>

</html>