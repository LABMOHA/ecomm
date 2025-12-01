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
                    <?php
                    ?>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
                    <?php
                    ?>

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
                        <th class="px-6 py-3 text-center">Price(for 1 )</th>
                        <th class="px-6 py-3 text-center">Quantity</th>
                        <th class="px-6 py-3 text-center">Price total per product </th>


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

                <?php // if (is_logged_in()): 
                ?>
                <a href="checkout.php" class="block w-full bg-green-500 text-white text-center py-3 rounded-lg hover:bg-green-600 font-semibold">
                    Go to Checkout
                </a>

            </div>
        </div>

    </div>
    <script>
        total = 0;
        let show = [];

        const cartString = localStorage.getItem('cart');

        if (cartString) {
            const cartObj = JSON.parse(cartString);
            show = Object.values(cartObj);
        }

        console.log("show:", show);

        const tbody = document.querySelector("tbody");



        show.forEach(element => {
            total += element.price;
            tbody.innerHTML += `<tr>
            <td class="px-6 py-4">
                <div class="fl$ex items-center">
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
                <form method="POST" action="cart_action.php" class="inline">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="product_id" value="${element.id}">
                    <input type="number" data-id="${element.id}" name="quantity" value="1"
                        min="0" max="${element.stock}"
                        class="w-20 px-2 py-1 border rounded text-center">
                </form>
            </td>
            <td class="px-6 py-4 text-center" data-id="${element.id}">${element.price}</td>
            <td class="px-6 py-4 text-center">
                
                    
                    <input type="hidden" name="product_id" value="${element.id}">
                    <button class="remove" data-id="${element.id}" type="submit" class="text-red-500 hover:text-red-700">Remove</button>
                
            </td>
        </tr>
        `;

        });




        if (show.length > 0) {
            document.getElementById("empty").style.display = "none";

        } else {
            document.getElementById("nonempty").style.display = "none";

        }

        function addtotal() {
            let total = document.getElementById("total")
            let tot = 0;
            document.querySelectorAll('td[data-id]').forEach(
                el => {
                    tot += Number(el.textContent);
                }
            );
            total.innerHTML = tot;
            console.log(tot);
        }

        //let newtotal;
        document.querySelectorAll('input[name="quantity"]').forEach(
            input => {
                input.addEventListener('input', () => {
                    let newtotal = 0;
                    show.forEach(element => {
                        if (element.id == input.dataset.id) {
                            element.quantity = Number(input.value);
                            newtotal += element.quantity * element.price;
                            document.querySelectorAll('td[data-id]').forEach(
                                el => {
                                    if (el.dataset.id == input.dataset.id)
                                        el.innerHTML = newtotal;
                                }
                            );
                        }




                        console.log(newtotal);
                        addtotal();

                    });

                });


            }


        );
















        function removefromCart(id) {
            const cartString = localStorage.getItem('cart');
            console.log(id);
            if (!cartString) return;

            const cartObj = JSON.parse(cartString);
            delete cartObj[id];

            localStorage.setItem('cart', JSON.stringify(cartObj));
            document.querySelectorAll('td[data-id]').forEach(
                el => {
                    if (id == el.dataset.id) {
                        let total = document.getElementById("total");
                        total.innerHTML = document(total.value) - el.value;
                    }

                }
            );
            window.location.reload();
        }

        document.querySelectorAll('.remove').forEach(element => {
            element.addEventListener('click', function() {
                removefromCart(this.dataset.id);
                console.log(this.dataset.id);
            });
        });
    </script>

</body>


</html>