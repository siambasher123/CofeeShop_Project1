<?php
session_start();
include_once 'config.php';

// Restrict to admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch contacts
$contacts = $conn->query("SELECT * FROM contacts ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact List - Admin</title>
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

        .content {
            padding: 20px;
        }

        .navbar-custom {
            background-color: #6f4e37;
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
        <a href="order_list.php">Order List</a>
        <a href="seats_to_reserve.php">Reservation</a> <!-- Added Reservation menu item -->
        <a href="transaction_history.php">Transaction History</a>
        <a href="contact_list.php" class="active">Contact List</a>
        <a href="give_discount.php">Give Discount</a>
        <a href="logout.php">Logout</a>
        <a href="index.php" class="mt-3 btn btn-warning w-100 text-center">Back to Homepage</a>
        </div>

        <!-- Main content -->
        <div class="content flex-grow-1">
            <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1">Contact List</span>
                </div>
            </nav>

            <div class="container">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($contacts->num_rows > 0): ?>
                            <?php $i = 1;
                            while ($c = $contacts->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo htmlspecialchars($c['name']); ?></td>
                                    <td><?php echo htmlspecialchars($c['email']); ?></td>
                                    <td><?php echo substr(htmlspecialchars($c['message']), 0, 50) . '...'; ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#contactModal<?php echo $c['id']; ?>">View</button>
                                    </td>
                                </tr>

                                <!-- Modal -->
                                <div class="modal fade" id="contactModal<?php echo $c['id']; ?>" tabindex="-1" aria-labelledby="contactModalLabel<?php echo $c['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="contactModalLabel<?php echo $c['id']; ?>">Message from <?php echo htmlspecialchars($c['name']); ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Email:</strong> <?php echo htmlspecialchars($c['email']); ?></p>
                                                <p><strong>Message:</strong></p>
                                                <p><?php echo nl2br(htmlspecialchars($c['message'])); ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No contact messages found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>