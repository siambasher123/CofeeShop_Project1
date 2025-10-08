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