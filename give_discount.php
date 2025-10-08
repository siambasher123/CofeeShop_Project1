<?php
session_start();
include 'config.php';

// Restrict to admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Handle discount submission
if(isset($_POST['apply_discount'])){
    $product_id = intval($_POST['product_id']);
    $discount_price = floatval($_POST['discount_price']);
    
    $conn->query("UPDATE products SET discount_price=$discount_price WHERE id=$product_id");
    $success = "Discount applied successfully!";
}

// Fetch products
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Give Discount - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .sidebar { min-height: 100vh; background-color: #343a40; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 12px; }
        .sidebar a:hover { background-color: #495057; }
        .content { padding: 20px; }
        .old-price { text-decoration: line-through; color: red; }
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
        <a href="transaction_history.php">Transaction History</a>
        <a href="contact_list.php">Contact List</a>
        <a href="give_discount.php" class="active">Give Discount</a>
        <a href="logout.php">Logout</a>
    </div>

                <?php while($p = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td><?php echo $p['name']; ?></td>
                        <td>
                            <?php if($p['discount_price']): ?>
                                <span class="old-price">$<?php echo $p['price']; ?></span>
                            <?php else: ?>
                                $<?php echo $p['price']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>