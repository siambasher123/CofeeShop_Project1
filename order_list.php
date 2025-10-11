<?php
session_start();
include_once 'config.php';

// Restrict to admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Yes/No action via POST for safety against accidental triggers
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    $action = $_POST['action'];

    if ($action === 'yes') {
        $orderStmt = $conn->prepare("SELECT id, user_id, total FROM orders WHERE id = ? LIMIT 1");
        if ($orderStmt) {
            $orderStmt->bind_param("i", $order_id);
            $orderStmt->execute();
            $result = $orderStmt->get_result();
            $order = $result ? $result->fetch_assoc() : null;
            $orderStmt->close();

            if ($order) {
                $conn->begin_transaction();
                try {
                    $insertStmt = $conn->prepare("INSERT INTO transaction1 (order_id, user_id, total, created_at) VALUES (?, ?, ?, NOW())");
                    $updateStmt = $conn->prepare("UPDATE orders SET status = 'processed' WHERE id = ?");

                    if (!$insertStmt || !$updateStmt) {
                        throw new \Exception("Failed to prepare statements.");
                    }

                    $orderId = (int) $order['id'];
                    $userId = (int) $order['user_id'];
                    $total = (float) $order['total'];

                    $insertStmt->bind_param("iid", $orderId, $userId, $total);
                    $updateStmt->bind_param("i", $orderId);

                    if (!$insertStmt->execute() || !$updateStmt->execute()) {
                        throw new \Exception("Failed to update order.");
                    }

                    $conn->commit();
                    $_SESSION['message'] = "Order recorded successfully.";
                    $_SESSION['message_type'] = "success";
                } catch (\Exception $e) {
                    $conn->rollback();
                    $_SESSION['message'] = "Unable to record order. Please try again.";
                    $_SESSION['message_type'] = "danger";
                } finally {
                    if (isset($insertStmt) && $insertStmt instanceof \mysqli_stmt) {
                        $insertStmt->close();
                    }
                    if (isset($updateStmt) && $updateStmt instanceof \mysqli_stmt) {
                        $updateStmt->close();
                    }
                }
            } else {
                $_SESSION['message'] = "Order not found or already processed.";
                $_SESSION['message_type'] = "warning";
            }
        }
    } elseif ($action === 'no') {
        $deleteStmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        if ($deleteStmt) {
            $deleteStmt->bind_param("i", $order_id);
            if ($deleteStmt->execute()) {
                $_SESSION['message'] = "Order removed.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Unable to remove order.";
                $_SESSION['message_type'] = "danger";
            }
            $deleteStmt->close();
        }
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
                <?php $messageType = $_SESSION['message_type'] ?? 'info'; ?>
                <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-custom">
                    <?php echo htmlspecialchars($_SESSION['message']); ?>
                </div>
                <?php
                unset($_SESSION['message'], $_SESSION['message_type']);
                ?>
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
                                <td><?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                <td>$<?php echo number_format((float) $order['total'], 2); ?></td>
                                <td class="d-flex gap-2">
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                        <input type="hidden" name="action" value="yes">
                                        <button type="submit" class="btn btn-success btn-sm">Yes</button>
                                    </form>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this order?');">
                                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                        <input type="hidden" name="action" value="no">
                                        <button type="submit" class="btn btn-danger btn-sm">No</button>
                                    </form>
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