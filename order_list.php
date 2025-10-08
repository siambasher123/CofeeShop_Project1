<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order List - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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