<?php
session_start();

include_once 'config.php';


// Check if user is logged in
$user_logged_in = isset($_SESSION['user_id']);
$user_name = '';
$user_id = $user_logged_in ? $_SESSION['user_id'] : 0;

if ($user_logged_in) {

    $user_result = $conn->query("SELECT first_name FROM users WHERE id='$user_id'");
    if ($user_result->num_rows > 0) {

        $user_name = $user_result->fetch_assoc()['first_name'];
    }
}


// Handle Add to Cart
if(isset($_GET['add_to_cart']) && $user_logged_in){


    $product_id = intval($_GET['add_to_cart']);

    $check = $conn->query("SELECT * FROM cart WHERE user_id=$user_id AND product_id=$product_id");
    if($check->num_rows > 0){
        $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE user_id=$user_id AND product_id=$product_id");
    } else {
        $conn->query("INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id,$product_id,1)");
    }
    header("Location: menu.php?msg=added");
    exit();
}

// Fetch products
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menu - Coffee Bliss</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Roboto', sans-serif; background-color: #f8f9fa; }
.card img { height: 200px; object-fit: cover; }
.navbar-custom { background-color: #6f4e37; }
.btn-warning { background-color: #f0ad4e; border-color: #f0ad4e; }
.btn-warning:hover { background-color: #ec971f; border-color: #d58512; }
.old-price { text-decoration: line-through; color: #888; margin-right: 8px; }
.card-title { font-weight: 600; }
.card-text { margin-bottom: 0.5rem; }
</style>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
<div class="container">
    <a class="navbar-brand" href="index.php">☕ Coffee Bliss</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" >
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link active" href="menu.php">Menu</a></li>
            <li class="nav-item"><a class="nav-link" href="seat_reservation.php">Reservation</a></li>
            <?php if($user_logged_in): ?>
                <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
            <?php if($user_logged_in): ?>
                <li class="nav-item"><span class="nav-link text-warning fw-bold">Hello, <?= htmlspecialchars($user_name) ?></span></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
</nav>

<div class="container mt-5">
    <?php
    if(isset($_GET['msg'])){
        if($_GET['msg'] == 'added'){
            echo '<div class="alert alert-success text-center">Added to cart successfully!</div>';
        } elseif($_GET['msg'] == 'checkout'){
            echo '<div class="alert alert-success text-center">Order processed successfully!</div>';
        }
    }
    ?>
    <h2 class="mb-4 text-center">Our Coffee Menu</h2>
    <div class="row">
        <?php if($result->num_rows > 0): ?>
            <?php while($product = $result->fetch_assoc()): ?>
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm h-100">
                        <img src="<?= $product['image'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= $product['name'] ?></h5>
                            <p class="card-text"><?= $product['description'] ?></p>
                            <p class="card-text">
                                <?php if(!empty($product['discount_price'])): ?>
                                    <span class="old-price">$<?= $product['price'] ?></span>
                                    <span class="text-success fw-bold">$<?= $product['discount_price'] ?></span>
                                <?php else: ?>
                                    <strong>$<?= $product['price'] ?></strong>
                                <?php endif; ?>
                            </p>
                            <?php if($user_logged_in): ?>
                                <a href="menu.php?add_to_cart=<?= $product['id'] ?>" class="btn btn-warning mt-auto w-100">Add to Cart</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-warning mt-auto w-100">Login to Add</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12"><div class="alert alert-info text-center">No products available yet.</div></div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
