
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
  <div class="container">
    <a class="navbar-brand" href="index.php">Coffee Bliss</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="menu.php" class="nav-link">Menu</a></li>
        <li class="nav-item"><a href="cart.php" class="nav-link">Cart</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>