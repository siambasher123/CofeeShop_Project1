<?php
session_start();
include_once 'config.php';

// Redirect to login if not logged in
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user'){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for this user
$cart_query = $conn->query("
    SELECT c.id AS cart_id, p.id AS product_id, p.name, p.price, p.discount_price, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");

if($cart_query->num_rows == 0){
    echo "<div style='text-align:center; margin-top:50px;'>
            <h3>Your cart is empty.</h3>
            <a href='menu.php' class='btn btn-primary mt-3'>Go to Menu</a>
          </div>";
    exit();
}

// Handle order submission
if(isset($_POST['place_order'])){
    $conn->begin_transaction();

    try {
        // Insert new order
        $conn->query("INSERT INTO orders (user_id, order_date, status) VALUES ($user_id, NOW(), 'Pending')");
        $order_id = $conn->insert_id;

        $total_amount = 0;

        // Move items from cart to order_items
        while($item = $cart_query->fetch_assoc()){
            $price = !empty($item['discount_price']) ? $item['discount_price'] : $item['price'];
            $subtotal = $price * $item['quantity'];
            $total_amount += $subtotal;

            $conn->query("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, $price)
            ");
        }

        // Add transaction record
        $conn->query("
            INSERT INTO transaction1 (user_id, order_id, total, created_at)
            VALUES ($user_id, $order_id, $total_amount, NOW())
        ");

        // Clear user's cart
        $conn->query("DELETE FROM cart WHERE user_id = $user_id");

        $conn->commit();

        header("Location: menu.php?msg=checkout");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='alert alert-danger text-center'>Error processing order. Please try again.</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Take Order - Coffee Bliss</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
</nav>