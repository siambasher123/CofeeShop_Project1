<?php
session_start();
include_once 'config.php';

// Check if user is logged in
$user_logged_in = isset($_SESSION['user_id']);
$user_name = '';
$user_id = $user_logged_in ? $_SESSION['user_id'] : 0;
if ($user_logged_in) {
    $user_result = $conn->query("SELECT first_name FROM users WHERE id='$user_id'");
    if ($user_result->num_rows > 0) {
        $user_name = $user_result->fetch_assoc()['first_name'];
    }
}

// Handle Add to Cart
if(isset($_GET['add_to_cart']) && $user_logged_in){
    $product_id = intval($_GET['add_to_cart']);
    $check = $conn->query("SELECT * FROM cart WHERE user_id=$user_id AND product_id=$product_id");
    if($check->num_rows > 0){
        $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE user_id=$user_id AND product_id=$product_id");
    } else {
        $conn->query("INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id,$product_id,1)");
    }
    header("Location: menu.php?msg=added");
    exit();
}

// Fetch products
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>

