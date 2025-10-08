<?php
session_start();
include_once 'config.php';

// Restrict access to admins
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch dashboard stats
$product_count = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$order_count = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$contact_count = $conn->query("SELECT COUNT(*) as count FROM contacts")->fetch_assoc()['count'];
$transaction_count = $conn->query("SELECT COUNT(*) as count FROM transaction1")->fetch_assoc()['count'];

// Fetch sales data
$sales_result = $conn->query("
    SELECT DATE(created_at) as sale_date, SUM(total) as total_sales
    FROM transaction1
    GROUP BY DATE(created_at)
    ORDER BY sale_date ASC
");
$sales_dates = [];
$sales_totals = [];
while ($row = $sales_result->fetch_assoc()) {
    $sales_dates[] = $row['sale_date'];
    $sales_totals[] = $row['total_sales'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Coffee Bliss</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3">
            <h3 class="text-white text-center mb-4">Admin Panel</h3>
            <a href="admin_dashboard.php" class="bg-warning text-dark">Dashboard</a>
            <a href="add_products.php">Add Products</a>
            <a href="inventory_list.php">Inventory List</a>
            <a href="order_list.php">Order List</a>
            <a href="seats_to_reserve.php">Reservation</a> <!-- Added Reservation menu item -->
            <a href="transaction_history.php">Transaction History</a>
            <a href="contact_list.php">Contact List</a>
            <a href="give_discount.php">Give Discount</a>
            <a href="logout.php">Logout</a>
            <a href="index.php" class="mt-3 btn btn-warning w-100 text-center">Back to Homepage</a>
        </div>

        <!-- Main content -->
        <div class="content flex-grow-1">
            <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1">Welcome, Admin</span>
                </div>
            </nav>

            <div class="container">
                <h2>Dashboard Overview</h2>
                <p>Use the sidebar to manage products, inventory, orders, transactions, contacts, and discounts.</p>

                <!-- Dashboard cards -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center p-3">
                            <h5>Add Products</h5>
                            <p>Total: <?php echo $product_count; ?></p>
                            <a href="add_products.php" class="btn btn-warning btn-sm">Go</a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center p-3">
                            <h5>Order List</h5>
                            <p>Total: <?php echo $order_count; ?></p>
                            <a href="order_list.php" class="btn btn-warning btn-sm">Go</a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center p-3">
                            <h5>Transaction History</h5>
                            <p>Total: <?php echo $transaction_count; ?></p>
                            <a href="transaction_history.php" class="btn btn-warning btn-sm">Go</a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center p-3">
                            <h5>Contact List</h5>
                            <p>Total: <?php echo $contact_count; ?></p>
                            <a href="contact_list.php" class="btn btn-warning btn-sm">Go</a>
                        </div>
                    </div>
                </div>

                <!-- Sales Chart -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card shadow-sm p-4">
                            <h4 class="mb-3">Sales Graph (Interactive)</h4>
                            <canvas id="salesChart" height="100"></canvas>
                            <small class="text-muted">Use mouse scroll to zoom and drag to pan.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($sales_dates); ?>,
                datasets: [{
                    label: 'Total Sales ($)',
                    data: <?php echo json_encode($sales_totals); ?>,
                    backgroundColor: 'rgba(111,78,55,0.2)',
                    borderColor: 'rgba(111,78,55,1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: 'rgba(111,78,55,1)',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return `$${context.raw.toFixed(2)}`;
                            }
                        }
                    },
                    zoom: {
                        pan: {
                            enabled: true,
                            mode: 'x'
                        },
                        zoom: {
                            wheel: {
                                enabled: true
                            },
                            mode: 'x'
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Sales ($)'
                        },
                        beginAtZero: true
                    }
                }
            },
            plugins: [ChartZoom]
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>