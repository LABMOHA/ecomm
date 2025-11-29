<?php
require_once '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

//$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];

$_SESSION['cart'] = array(); 
//[] = [
//     'id' => $id,
//     'name' =>  $name,
//     'price' => $price,
//     'stock' => $stock,
//     'image' => $image
// ];

array_push($_SESSION['cart'], [
    'id' =>   $_POST['id'],
    'name' =>  $_POST['name'],
    'price' => $_POST['price'],
    'stock' => $_POST['stock'],
    'image' =>  $_POST['image_url']
]);

if (isset($_SESSION['cart'])) {
    redirect('cart.php');
}
