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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #343a40; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 12px; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; }
        .content { padding: 20px; width: 100%; }
        .navbar-custom { background-color: #6f4e37; }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h3 class="text-white text-center mb-4">Admin Panel</h3>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="add_products.php">Add Products</a>
        <a href="inventory_list.php">Inventory List</a>
        <a href="order_list.php">Order List</a>
        <a href="transaction_history.php" class="active">Transaction History</a>
        <a href="contact_list.php">Contact List</a>
        <a href="logout.php">Logout</a>
        <a href="index.php" class="mt-3 btn btn-warning w-100 text-center">Back to Homepage</a>
    </div>

    <!-- Main content -->
    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Transaction History</span>
            </div>
        </nav>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>User</th>
                    <th>Total</th>
                    <th>Details</th>
                    <th>Action</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if($transactions->num_rows > 0): ?>
                    <?php while($t = $transactions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $t['id']; ?></td>
                        <td><?php echo $t['first_name'].' '.$t['last_name']; ?></td>
                        <td>$<?php echo $t['total']; ?></td>
                        <td><?php echo $t['details'] ?: '-'; ?></td>
                        <td>
                            <!-- Update details form -->
                            <form method="post" class="d-flex">
                                <input type="hidden" name="transaction_id" value="<?php echo $t['id']; ?>">
                                <input type="text" name="details" class="form-control form-control-sm me-2" placeholder="Write details" value="<?php echo $t['details']; ?>">
                                <button type="submit" name="update_details" class="btn btn-warning btn-sm">Save</button>
                            </form>
                        </td>
                        <td><?php echo $t['created_at']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No transactions yet</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
