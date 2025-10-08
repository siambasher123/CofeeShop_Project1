<?php
include_once 'config.php';

if (isset($_POST['signup'])) {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone      = $_POST['phone'];
    $address    = $_POST['address'];
    $role       = $_POST['role'];

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($check->num_rows > 0) {
        $error = "Email already registered. Please login.";
    } else {
        $sql = "INSERT INTO users (first_name,last_name,email,password,phone,address,role)
                VALUES ('$first_name','$last_name','$email','$password','$phone','$address','$role')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Signup successful! Please login.'); window.location='login.php';</script>";
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 bg-white p-4 shadow rounded">
                <h3 class="text-center mb-4">Signup</h3>
                <?php if (isset($error)) echo '<div class="alert alert-danger">' . $error . '</div>'; ?>
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col">
                            <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                        </div>
                        <div class="col">
                            <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="phone" class="form-control" placeholder="Phone">
                    </div>
                    <div class="mb-3">
                        <input type="text" name="address" class="form-control" placeholder="Address">
                    </div>
                    <div class="mb-3">
                        <select name="role" class="form-select" required>
                            <option value="">Select Role</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="signup" class="btn btn-warning w-100">Signup</button>
                    <p class="mt-3 text-center">Already have an account? <a href="login.php">Login</a></p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>