<?php
session_start();
include_once 'config.php';

// Restrict to admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Yes/No action
if (isset($_GET['id']) && isset($_GET['action'])) {
    $order_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'yes') {
        // Move order to transaction1
        $order = $conn->query("SELECT * FROM orders WHERE id=$order_id")->fetch_assoc();
        if ($order) {
            $conn->query("INSERT INTO transaction1 (order_id, user_id, total, created_at) VALUES (
                " . $order['id'] . ",
                " . $order['user_id'] . ",
                " . $order['total'] . ",
                NOW()
            )");

            // Mark order as processed
            $conn->query("UPDATE orders SET status='processed' WHERE id=$order_id");

            $_SESSION['message'] = "Order taken!";
        }
    } elseif ($action == 'no') {
        // Safe to delete completely
        $conn->query("DELETE FROM orders WHERE id=$order_id");
        $_SESSION['message'] = "Order removed!";
    }

    header("Location: order_list.php");
    exit();
}

// Fetch orders + user info
$orders = $conn->query("
    SELECT o.id, o.user_id, o.total, o.status, u.first_name, u.last_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.status IS NULL OR o.status != 'processed'
    ORDER BY o.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order List - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar .active {
            background-color: #6f4e37;
        }

        .content {
            padding: 20px;
            flex-grow: 1;
        }

        .alert-custom {
            margin-top: 15px;
        }
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
            <a href="order_list.php" class="active">Order List</a>
            <a href="transaction_history.php">Transaction History</a>
            <a href="contact_list.php">Contact List</a>
            <a href="logout.php">Logout</a>
            <a href="index.php" class="mt-3 btn btn-warning w-100 text-center">Back to Homepage</a>
        </div>

        <!-- Main content -->
        <div class="content">
            <h2>Order List</h2>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-custom"><?php echo $_SESSION['message'];
                                                                unset($_SESSION['message']); ?></div>
            <?php endif; ?>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders->num_rows > 0): ?>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
                                <td>$<?php echo $order['total']; ?></td>
                                <td>
                                    <a href="order_list.php?id=<?php echo $order['id']; ?>&action=yes" class="btn btn-success btn-sm">Yes</a>
                                    <a href="order_list.php?id=<?php echo $order['id']; ?>&action=no" class="btn btn-danger btn-sm">No</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No orders available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>