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
    $details = trim($_POST['details'] ?? '');

    if($transaction_id > 0){
        $stmt = $conn->prepare("UPDATE transaction1 SET details = ? WHERE id = ?");
        if($stmt){
            $stmt->bind_param("si", $details, $transaction_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    header("Location: transaction_history.php");
    exit();
}

$transactions = [];
$stmt = $conn->prepare("
    SELECT t.id, t.total, t.details, t.created_at, u.first_name, u.last_name
    FROM transaction1 t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
");
if($stmt){
    $stmt->execute();
    $result = $stmt->get_result();
    if($result){
        while($row = $result->fetch_assoc()){
            $transactions[] = $row;
        }
    }
    $stmt->close();
}
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
                <?php if(count($transactions) > 0): ?>
                    <?php foreach($transactions as $t): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($t['id'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($t['first_name'].' '.$t['last_name'], ENT_QUOTES); ?></td>
                        <td>$<?php echo number_format((float)$t['total'], 2); ?></td>
                        <td><?php echo $t['details'] !== null && $t['details'] !== '' ? htmlspecialchars($t['details'], ENT_QUOTES) : '-'; ?></td>
                        <td>
                            <!-- Update details form -->
                            <form method="post" class="d-flex">
                                <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($t['id'], ENT_QUOTES); ?>">
                                <input type="text" name="details" class="form-control form-control-sm me-2" placeholder="Write details" value="<?php echo htmlspecialchars($t['details'] ?? '', ENT_QUOTES); ?>">
                                <button type="submit" name="update_details" class="btn btn-warning btn-sm">Save</button>
                            </form>
                        </td>
                        <td><?php echo htmlspecialchars($t['created_at'], ENT_QUOTES); ?></td>
                    </tr>
                    <?php endforeach; ?>
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
