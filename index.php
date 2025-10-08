<?php
include 'config.php';
if (session_status() == PHP_SESSION_NONE) session_start();

// Get user info if logged in
$user_name = '';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_result = $conn->query("SELECT first_name FROM users WHERE id='$user_id'");
    if ($user_result->num_rows > 0) {
        $user_name = $user_result->fetch_assoc()['first_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Bliss</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f7f1;
        }

        .logo {
            font-family: 'Pacifico', cursive;
            font-size: 2rem;
            color: #6f4e37;
        }

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=1470&q=80') center/cover no-repeat;
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
            position: relative;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            animation: fadeInDown 1s ease forwards;
        }

        .hero p {
            font-size: 1.5rem;
            animation: fadeInUp 1s ease 0.5s forwards;
            opacity: 0;
        }

        .btn-custom {
            background: linear-gradient(90deg, #f5b041, #d68910);
            color: white;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Features section */
        .feature-card {
            background-color: #fff3e0;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .feature-card i {
            font-size: 3rem;
            color: #d68910;
            margin-bottom: 15px;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
        <div class="container">
            <a class="navbar-brand logo" href="index.php">☕ Coffee Bliss</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="seat_reservation.php">Reservation</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
                    <?php if ($user_name): ?>
                        <li class="nav-item"><span class="nav-link text-warning fw-bold">Hello, <?= htmlspecialchars($user_name) ?></span></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero text-center d-flex flex-column justify-content-center">
        <h1>Welcome to Coffee Bliss</h1>
        <p>Your perfect cup of coffee, brewed with love</p>
        <a href="menu.php" class="btn btn-custom btn-lg mt-4">Explore Menu</a>
    </section>

    <!-- Features Section -->
    <section class="container my-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose Coffee Bliss?</h2>
            <p class="text-muted">Experience quality, taste, and passion in every cup.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="bi bi-cup-straw"></i>
                    <h4 class="fw-bold">Premium Beans</h4>
                    <p>We source only the finest coffee beans for a rich, aromatic flavor.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="bi bi-people"></i>
                    <h4 class="fw-bold">Expert Baristas</h4>
                    <p>Our skilled baristas craft every cup with precision and care.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="bi bi-clock-history"></i>
                    <h4 class="fw-bold">Fast & Fresh</h4>
                    <p>Enjoy freshly brewed coffee served quickly without compromising quality.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="container my-5 text-center">
        <h2 class="fw-bold mb-4">Our Mission</h2>
        <p class="lead text-muted mx-auto" style="max-width:700px;">At Coffee Bliss, our goal is to provide a delightful coffee experience that uplifts your day. From bean to cup, we emphasize quality, sustainability, and passion in every step of our process.</p>
        <a href="about.php" class="btn btn-custom mt-3">Learn More About Us</a>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> Coffee Bliss. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>