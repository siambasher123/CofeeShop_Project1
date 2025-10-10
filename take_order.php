
<?php
session_start();
include_once 'config.php';

// Redirect to login if not logged in
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user'){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


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