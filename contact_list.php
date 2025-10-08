<?php
session_start();
include_once 'config.php';

// Restrict to admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Fetch contacts
$contacts = $conn->query("SELECT * FROM contacts ORDER BY id DESC");
?>



<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h3 class="text-white text-center mb-4">Admin Panel</h3>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="add_products.php">Add Products</a>
        <a href="inventory_list.php">Inventory List</a>
        <a href="order_list.php">Order List</a>
        <a href="transaction_history.php">Transaction History</a>
        <a href="contact_list.php" class="active">Contact List</a>
        <a href="logout.php">Logout</a>
        <a href="index.php" class="mt-3 btn btn-warning w-100 text-center">Back to Homepage</a>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>