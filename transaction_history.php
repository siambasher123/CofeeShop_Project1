<?php
session_start();
include_once 'config.php';

// Restrict to admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Handle updating details
if(isset($_POST['update_details'])){
    $transaction_id = intval($_POST['transaction_id']);
    $details = $conn->real_escape_string($_POST['details']);
    $conn->query("UPDATE transaction1 SET details='$details' WHERE id=$transaction_id");
    header("Location: transaction_history.php");
    exit();
}

// Fetch transactions
$transactions = $conn->query("
    SELECT t.*, u.first_name, u.last_name
    FROM transaction1 t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History - Admin</title>
</head>
<body>

<!-- Sidebar -->
    <div class="sidebar p-3">
        <h3 class="text-white text-center mb-4">Admin Panel</h3>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="add_products.php">Add Products</a>
        <a href="inventory_list.php">Inventory List</a>
        <a href="order_list.php">Order List</a>
        <a href="contact_list.php">Contact List</a>
        <a href="logout.php">Logout</a>
        <a href="index.php" class="mt-3 btn btn-warning w-100 text-center">Back to Homepage</a>
    </div>