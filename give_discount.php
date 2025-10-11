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

    if($product_id > 0 && $discount_price >= 0){
        $stmt = $conn->prepare("UPDATE products SET discount_price = ? WHERE id = ?");
        if($stmt){
            $stmt->bind_param("di", $discount_price, $product_id);
            if($stmt->execute()){
                $success = "Discount applied successfully!";
            } else {
                $error = "Unable to apply discount right now.";
            }
            $stmt->close();
        } else {
            $error = "Unable to prepare discount update.";
        }
    } else {
        $error = "Please provide a valid discount amount.";
    }
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
        <a href="order_list.php">Order List</a>
        <a href="seats_to_reserve.php">Reservation</a> <!-- Added Reservation menu item -->
        <a href="transaction_history.php">Transaction History</a>
        <a href="contact_list.php" >Contact List</a>
        <a href="give_discount.php" class="active">Give Discount</a>
        <a href="logout.php">Logout</a>
        <a href="index.php" class="mt-3 btn btn-warning w-100 text-center">Back to Homepage</a>
    </div>

    <div class="content flex-grow-1">
        <h2>Give Discount to Products</h2>

        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES); ?></div>
        <?php elseif(isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
        <?php endif; ?>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Product ID</th>

                    <th>Name</th>
                    <th>Original Price</th>
                    <th>Discount Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($p = $products->fetch_assoc()): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($p['id'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($p['name'], ENT_QUOTES); ?></td>
                        <td>
                            <?php if($p['discount_price']): ?>
                                <span class="old-price">$<?php echo htmlspecialchars($p['price'], ENT_QUOTES); ?></span>
                            <?php else: ?>
                                $<?php echo htmlspecialchars($p['price'], ENT_QUOTES); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post" class="d-flex">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($p['id'], ENT_QUOTES); ?>">
                                <input type="number" step="0.01" min="0" name="discount_price" class="form-control me-2" 
                                       value="<?php echo htmlspecialchars($p['discount_price'] ?: $p['price'], ENT_QUOTES); ?>" required>
                                <button type="submit" name="apply_discount" class="btn btn-warning">Apply</button>
                            </form>
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
