<?php
session_start();
include_once 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Update quantity or remove
if(isset($_POST['update'])){
    foreach($_POST['quantities'] as $cart_id => $qty){
        $qty = intval($qty);
        if($qty > 0){
            $conn->query("UPDATE cart SET quantity=$qty WHERE id=$cart_id AND user_id=$user_id");
        } else {
            $conn->query("DELETE FROM cart WHERE id=$cart_id AND user_id=$user_id");
        }
    }
}

// Handle checkout
if(isset($_POST['checkout'])){
    $cart_items = $conn->query("
        SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.image, c.quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $user_id
    ");

    $total = 0;
    while($item = $cart_items->fetch_assoc()){
        $total += $item['price'] * $item['quantity'];
    }

    $conn->query("INSERT INTO orders (user_id, total) VALUES ($user_id, $total)");
    $order_id = $conn->insert_id;

    $cart_items = $conn->query("SELECT * FROM cart WHERE user_id=$user_id");
    while($item = $cart_items->fetch_assoc()){
        $product = $conn->query("SELECT * FROM products WHERE id=".$item['product_id'])->fetch_assoc();
        $price = $product['price'];
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price)
                      VALUES ($order_id, ".$item['product_id'].", ".$item['quantity'].", $price)");
    }

    $conn->query("DELETE FROM cart WHERE user_id=$user_id");
    header("Location: menu.php?msg=checkout");
    exit();
}

// Fetch cart items
$cart_items = $conn->query("
    SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.image, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");
?>