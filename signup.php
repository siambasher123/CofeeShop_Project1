<?php
include_once 'config.php';

if (isset($_POST['signup'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $phone      = trim($_POST['phone'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $role       = 'user';

    if ($first_name === '' || $last_name === '' || $email === '' || $password === '') {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        if (!$checkStmt) {
            $error = "Unexpected error. Please try again later.";
        } else {
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                $error = "Email already registered. Please login.";
                $checkStmt->close();
            } else {
                $checkStmt->close();
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $insertStmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, ?, ?)");

                if (!$insertStmt) {
                    $error = "Unexpected error. Please try again later.";
                } else {
                    $insertStmt->bind_param("sssssss", $first_name, $last_name, $email, $hashedPassword, $phone, $address, $role);
                    if ($insertStmt->execute()) {
                        echo "<script>alert('Signup successful! Please login.'); window.location='login.php';</script>";
                        $insertStmt->close();
                        exit();
                    }
                    $error = "Unable to complete signup. Please try again.";
                    $insertStmt->close();
                }
            }
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
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
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
                        <small class="text-muted">Accounts are created with customer access. Contact support for administrative roles.</small>
                    </div>
                    <button type="submit" name="signup" class="btn btn-warning w-100">Signup</button>
                    <p class="mt-3 text-center">Already have an account? <a href="login.php">Login</a></p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
