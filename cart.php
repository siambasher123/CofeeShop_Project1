<?php
session_start();
include_once 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Update quantity or remove
if(isset($_POST['update'])){
    foreach($_POST['quantities'] as $cart_id => $qty){
        $qty = intval($qty);
        if($qty > 0){
            $conn->query("UPDATE cart SET quantity=$qty WHERE id=$cart_id AND user_id=$user_id");
        } else {
            $conn->query("DELETE FROM cart WHERE id=$cart_id AND user_id=$user_id");
        }
    }
}

// Handle checkout
if(isset($_POST['checkout'])){
    $cart_items = $conn->query("
        SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.image, c.quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $user_id
    ");

    $total = 0;
    while($item = $cart_items->fetch_assoc()){
        $total += $item['price'] * $item['quantity'];
    }

    $conn->query("INSERT INTO orders (user_id, total) VALUES ($user_id, $total)");
    $order_id = $conn->insert_id;

    $cart_items = $conn->query("SELECT * FROM cart WHERE user_id=$user_id");
    while($item = $cart_items->fetch_assoc()){
        $product = $conn->query("SELECT * FROM products WHERE id=".$item['product_id'])->fetch_assoc();
        $price = $product['price'];
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price)
                      VALUES ($order_id, ".$item['product_id'].", ".$item['quantity'].", $price)");
    }

    $conn->query("DELETE FROM cart WHERE user_id=$user_id");
    header("Location: menu.php?msg=checkout");
    exit();
}

// Fetch cart items
$cart_items = $conn->query("
    SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.image, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Cart - Coffee Bliss</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Roboto', sans-serif; background-color: #f8f9fa; }
.navbar-custom { background-color: #6f4e37; }
.btn-warning { background-color: #f0ad4e; border-color: #f0ad4e; }
.btn-warning:hover { background-color: #ec971f; border-color: #d58512; }
.card img { height: 80px; object-fit: cover; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
<div class="container">
    <a class="navbar-brand" href="index.php">Coffee Bliss</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
            <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            <li class="nav-item"><a class="nav-link active" href="cart.php">Cart</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</div>
</nav>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Your Cart</h2>

    <?php if($cart_items->num_rows > 0): ?>
    <form method="post">
        <table class="table table-bordered table-striped align-middle text-center">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                while($item = $cart_items->fetch_assoc()): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td><img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>"></td>
                    <td>$<?php echo $item['price']; ?></td>
                    <td>
                        <input type="number" name="quantities[<?php echo $item['cart_id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0" class="form-control" style="width:80px;">
                    </td>
                    <td>$<?php echo $subtotal; ?></td>
                </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                    <td><strong>$<?php echo $total; ?></strong></td>
                </tr>
            </tbody>
        </table>

        <div class="d-flex justify-content-between">
            <a href="menu.php" class="btn btn-secondary">Continue Shopping</a>
            <div>
                <button type="submit" name="update" class="btn btn-warning">Update Cart</button>
                <button type="submit" name="checkout" class="btn btn-success">Checkout</button>
            </div>
        </div>
    </form>
    <?php else: ?>
        <div class="alert alert-info text-center">Your cart is empty. <a href="menu.php">Go to Menu</a></div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
