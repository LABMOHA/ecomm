<?php
require_once '../includes/config.php';
session_start();

$cart;
//$cart_items = $_SESSION['cart'];
$total = 0;
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
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold text-blue-600">E-Shop</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-700 hover:text-blue-600">Go Home</a>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Shopping Cart</h1>

        <div id="empty" class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 text-lg mb-4">Your cart is empty</p>
            <a href="index.php" class="inline-block bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                Start Shopping
            </a>
        </div>

        <div id="nonempty" class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left">Product</th>
                        <th class="px-6 py-3 text-center">Price(for 1)</th>
                        <th class="px-6 py-3 text-center">Quantity</th>
                        <th class="px-6 py-3 text-center">Price total per product</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                </tbody>
            </table>

            <div class="bg-gray-50 px-6 py-4">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-xl font-bold">Total:</span>
                    <div class="text-xl font-bold"><span id="total" class="text-xl font-bold">0</span> Dh</div>
                </div>

                <form id="myForm" action="checkout.php" method="POST">
                    <input type="hidden" name="cart" id="cartInput">
                    <input type="hidden" name="total" id="totalInput">

                    <button type="submit" class="block w-full bg-green-500 text-white text-center py-3 rounded-lg hover:bg-green-600 font-semibold">
                        Go to Checkout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let total = 0;
        let show = [];

        const cartString = localStorage.getItem('cart');

        if (cartString) {
            const cartObj = JSON.parse(cartString);
            show = Object.values(cartObj);
        }

        console.log("show:", show);

        const tbody = document.querySelector("tbody");

        // ✅ FIX 1: Build table rows correctly
        show.forEach(element => {
            // ✅ FIX 2: Calculate correct subtotal (price * quantity)
            let subtotal = element.price * element.quantity;

            tbody.innerHTML += `<tr>
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <img src="${element.image}"
                            alt="${element.name}"
                            class="w-16 h-16 object-cover rounded mr-4">
                        <div>
                            <div class="font-semibold">${element.name}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">${element.price} Dh</td>
                <td class="px-6 py-4 text-center">
                    <input type="number" 
                           data-id="${element.id}" 
                           data-price="${element.price}"
                           name="quantity" 
                           value="${element.quantity}"
                           min="1" 
                           max="${element.stock}"
                           class="quantity-input w-20 px-2 py-1 border rounded text-center">
                </td>
                <td class="px-6 py-4 text-center subtotal" data-id="${element.id}">${subtotal.toFixed(2)} Dh</td>
                <td class="px-6 py-4 text-center">
                    <button class="remove text-red-500 hover:text-red-700" 
                            data-id="${element.id}" 
                            type="button">
                        Remove
                    </button>
                </td>
            </tr>`;
        });

        // ✅ FIX 3: Show/hide empty or non-empty cart
        if (show.length > 0) {
            document.getElementById("empty").style.display = "none";
        } else {
            document.getElementById("nonempty").style.display = "none";
        }

        // ✅ FIX 4: Calculate total correctly
        function addtotal() {
            let tot = 0;
            document.querySelectorAll('.subtotal').forEach(el => {
                let value = parseFloat(el.textContent);
                if (!isNaN(value)) {
                    tot += value;
                }
            });
            document.getElementById("total").innerHTML = tot.toFixed(2);
            return tot;
        }

        // Initial total calculation
        addtotal();

        // ✅ FIX 5: Save cart to localStorage
        function saveCart(cart) {
            const cartObj = {};
            cart.forEach(item => {
                cartObj[item.id] = item;
            });
            localStorage.setItem('cart', JSON.stringify(cartObj));
        }

        // ✅ FIX 6: Update quantity - FIXED to work properly
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('input', function() {
                const id = this.dataset.id;
                const price = parseFloat(this.dataset.price);
                const newQuantity = parseInt(this.value) || 1;

                // ✅ Update the show array
                show.forEach(element => {
                    if (element.id == id) {
                        element.quantity = newQuantity;
                    }
                });

                // ✅ Update subtotal in the table
                const subtotal = price * newQuantity;
                document.querySelectorAll('.subtotal').forEach(el => {
                    if (el.dataset.id == id) {
                        el.innerHTML = subtotal.toFixed(2) + ' Dh';
                    }
                });

                // ✅ Recalculate total
                addtotal();

                // ✅ Save to localStorage
                saveCart(show);

                // ✅ Update hidden inputs
                updateHiddenInputs();
            });
        });

        // ✅ FIX 7: Remove from cart - FIXED
        function removefromCart(id) {
            const cartString = localStorage.getItem('cart');
            console.log("Removing ID:", id);

            if (!cartString) return;

            const cartObj = JSON.parse(cartString);
            delete cartObj[id];

            localStorage.setItem('cart', JSON.stringify(cartObj));

            // Reload page to refresh cart
            window.location.reload();
        }

        // ✅ FIX 8: Add click event to remove buttons
        document.querySelectorAll('.remove').forEach(element => {
            element.addEventListener('click', function() {
                removefromCart(this.dataset.id);
            });
        });

        // ✅ FIX 9: Update hidden form inputs
        function updateHiddenInputs() {
            document.getElementById("cartInput").value = localStorage.getItem("cart");
            document.getElementById("totalInput").value = addtotal().toFixed(2);
        }

        // ✅ Initial update of hidden inputs
        updateHiddenInputs();

        // ✅ FIX 10: Update inputs before form submit
        document.getElementById("myForm").addEventListener('submit', function(e) {
            updateHiddenInputs();
            console.log("Submitting cart:", document.getElementById("cartInput").value);
            console.log("Total:", document.getElementById("totalInput").value);

            localStorage.removeItem("cart");
        });
    </script>
</body>

</html>