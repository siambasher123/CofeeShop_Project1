<?php
session_start();
include_once 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$checkout_error = '';

// Update quantity or remove
if(isset($_POST['update']) && isset($_POST['quantities']) && is_array($_POST['quantities'])){
    $updateStmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    $deleteStmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");

    foreach($_POST['quantities'] as $cart_id => $qty){
        $cartId = (int) $cart_id;
        $qty = (int) $qty;
        if($cartId <= 0){
            continue;
        }

        if($qty > 0 && $updateStmt){
            $updateStmt->bind_param("iii", $qty, $cartId, $user_id);
            $updateStmt->execute();
        } elseif ($qty <= 0 && $deleteStmt){
            $deleteStmt->bind_param("ii", $cartId, $user_id);
            $deleteStmt->execute();
        }
    }

    if($updateStmt) $updateStmt->close();
    if($deleteStmt) $deleteStmt->close();
}

// Handle checkout
if(isset($_POST['checkout'])){
    $cartQuery = $conn->prepare("
        SELECT c.id AS cart_id, p.id AS product_id, p.name, p.price, c.quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");

    if($cartQuery){
        $conn->begin_transaction();
        try{
            $cartQuery->bind_param("i", $user_id);
            $cartQuery->execute();
            $result = $cartQuery->get_result();

            $items = [];
            $total = 0;
            while($row = $result->fetch_assoc()){
                $lineTotal = (float)$row['price'] * (int)$row['quantity'];
                $total += $lineTotal;
                $items[] = $row;
            }

            if(empty($items)){
                $conn->rollback();
                $checkout_error = "Your cart is empty.";
            } else {
                $orderStmt = $conn->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
                if(!$orderStmt){
                    throw new Exception("Unable to create order.");
                }
                $orderStmt->bind_param("id", $user_id, $total);
                if(!$orderStmt->execute()){
                    throw new Exception("Unable to create order.");
                }
                $order_id = $orderStmt->insert_id;
                $orderStmt->close();

                $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                if(!$itemStmt){
                    throw new Exception("Unable to create order items.");
                }
                foreach($items as $item){
                    $price = (float) $item['price'];
                    $qty = (int) $item['quantity'];
                    $productId = (int) $item['product_id'];
                    $itemStmt->bind_param("iiid", $order_id, $productId, $qty, $price);
                    if(!$itemStmt->execute()){
                        throw new Exception("Unable to create order items.");
                    }
                }
                $itemStmt->close();

                $deleteCart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
                if(!$deleteCart){
                    throw new Exception("Unable to clear cart.");
                }
                $deleteCart->bind_param("i", $user_id);
                $deleteCart->execute();
                $deleteCart->close();

                $conn->commit();
                header("Location: menu.php?msg=checkout");
                exit();
            }
        } catch (Exception $ex){
            $conn->rollback();
            $checkout_error = "Unable to process checkout right now. Please try again.";
        } finally {
            $cartQuery->close();
        }
    }
}

// Fetch cart items
$cart_items = [];
$cartTotals = [
    'count' => 0,
    'total' => 0
];
$cartQuery = $conn->prepare("
    SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.image, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
if($cartQuery){
    $cartQuery->bind_param("i", $user_id);
    $cartQuery->execute();
    $result = $cartQuery->get_result();
    if($result){
        while($row = $result->fetch_assoc()){
            $cart_items[] = $row;
            $cartTotals['count'] += (int)$row['quantity'];
            $cartTotals['total'] += (float)$row['price'] * (int)$row['quantity'];
        }
    }
    $cartQuery->close();
}
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

    <?php if($checkout_error): ?>
        <div class="alert alert-danger text-center"><?php echo htmlspecialchars($checkout_error, ENT_QUOTES); ?></div>
    <?php endif; ?>

    <?php if(count($cart_items) > 0): ?>
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
                <?php foreach($cart_items as $item): 
                    $subtotal = (float)$item['price'] * (int)$item['quantity'];
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?></td>
                    <td><img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?>"></td>
                    <td>$<?php echo number_format((float)$item['price'], 2); ?></td>
                    <td>
                        <input type="number" name="quantities[<?php echo (int)$item['cart_id']; ?>]" value="<?php echo (int)$item['quantity']; ?>" min="0" class="form-control" style="width:80px;">
                    </td>
                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                    <td><strong>$<?php echo number_format($cartTotals['total'], 2); ?></strong></td>
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
