<?php
session_start();
include 'config.php';

// Restrict to admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Handle discount submission
if(isset($_POST['apply_discount'])){
    $product_id = intval($_POST['product_id']);
    $discount_price = floatval($_POST['discount_price']);
    
    $conn->query("UPDATE products SET discount_price=$discount_price WHERE id=$product_id");
    $success = "Discount applied successfully!";
}

// Fetch products
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>