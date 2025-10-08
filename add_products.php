<?php
include_once 'config.php';

// Restrict access to admins
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Handle form submission
if(isset($_POST['add_product'])){
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle file upload
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_folder = "uploads/" . basename($image_name);

    // Move uploaded image
    if(move_uploaded_file($image_tmp, $image_folder)){
        // Insert into database
        $sql = "INSERT INTO products (name, image, price, description) 
                VALUES ('$name', '$image_folder', '$price', '$description')";
        if($conn->query($sql) === TRUE){
            $success = "Product added successfully!";
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = "Failed to upload image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Roboto', sans-serif; }
        .container { margin-top: 50px; }
        .btn-warning { background-color: #f0ad4e; border-color: #f0ad4e; }
        .btn-warning:hover { background-color: #ec971f; border-color: #d58512; }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Add New Product</h2>

    <?php 
    if(isset($error)) echo '<div class="alert alert-danger">'.$error.'</div>';
    if(isset($success)) echo '<div class="alert alert-success">'.$success.'</div>';
    ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Product Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label>Product Image</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>
        <button type="submit" name="add_product" class="btn btn-warning">Add Product</button>
        <a href="admin_dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
