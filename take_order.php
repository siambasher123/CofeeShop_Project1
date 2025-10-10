<?php
session_start();
include_once 'config.php';

// Redirect to login if not logged in
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user'){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for this user
$cart_query = $conn->query("
    SELECT c.id AS cart_id, p.id AS product_id, p.name, p.price, p.discount_price, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");

if($cart_query->num_rows == 0){
    echo "<div style='text-align:center; margin-top:50px;'>
            <h3>Your cart is empty.</h3>
            <a href='menu.php' class='btn btn-primary mt-3'>Go to Menu</a>
          </div>";
    exit();
}

// Handle order submission
if(isset($_POST['place_order'])){
    $conn->begin_transaction();

    try {
        // Insert new order
        $conn->query("INSERT INTO orders (user_id, order_date, status) VALUES ($user_id, NOW(), 'Pending')");
        $order_id = $conn->insert_id;

        $total_amount = 0;

        // Move items from cart to order_items
        while($item = $cart_query->fetch_assoc()){
            $price = !empty($item['discount_price']) ? $item['discount_price'] : $item['price'];
            $subtotal = $price * $item['quantity'];
            $total_amount += $subtotal;

            $conn->query("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, $price)
            ");
        }

        // Add transaction record
        $conn->query("
            INSERT INTO transaction1 (user_id, order_id, total, created_at)
            VALUES ($user_id, $order_id, $total_amount, NOW())
        ");

        // Clear user's cart
        $conn->query("DELETE FROM cart WHERE user_id = $user_id");

        $conn->commit();

        header("Location: menu.php?msg=checkout");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='alert alert-danger text-center'>Error processing order. Please try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Take Order - Coffee Bliss</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Coffee Bliss</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="menu.php" class="nav-link">Menu</a></li>
        <li class="nav-item"><a href="cart.php" class="nav-link">Cart</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-5">
  <h2 class="text-center mb-4">Confirm Your Order</h2>

  <table class="table table-bordered text-center bg-white shadow-sm">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Item</th>
        <th>Price (USD)</th>
        <th>Quantity</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $i = 1; $total = 0;
      $cart_query->data_seek(0); // Reset pointer to reuse
      while($row = $cart_query->fetch_assoc()):
          $price = !empty($row['discount_price']) ? $row['discount_price'] : $row['price'];
          $subtotal = $price * $row['quantity'];
          $total += $subtotal;
      ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td>$<?= number_format($price, 2) ?></td>
        <td><?= $row['quantity'] ?></td>
        <td>$<?= number_format($subtotal, 2) ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <div class="text-end mb-4">
    <h4>Total: <strong>$<?= number_format($total, 2) ?></strong></h4>
  </div>

  <form method="POST" class="text-center">
    <button type="submit" name="place_order" class="btn btn-success btn-lg px-5">Place Order</button>
    <a href="cart.php" class="btn btn-secondary btn-lg ms-2">Back to Cart</a>
  </form>
</div>

</body>
</html>