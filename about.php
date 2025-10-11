<?php
session_start();
include_once 'config.php';

// Check if user is logged in
$user_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar-custom {
            background-color: #6f4e37;
        }

        .btn-warning {
            background-color: #f0ad4e;
            border-color: #f0ad4e;
        }

        .btn-warning:hover {
            background-color: #ec971f;
            border-color: #d58512;
        }

        .hero-section {
            background-color: #d9c7b8;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: bold;
            color: #4b2e2e;
        }

        .team-card {
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: white;
            transition: transform 0.3s;
        }

        .team-card:hover {
            transform: translateY(-5px);
        }

        .faq-card {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">Coffee Shop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link active" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <?php if ($user_logged_in): ?>
                        <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section mb-5">
        <h1>About Coffee Shop</h1>
    </div>

    <div class="container mb-5">
        <!-- Company Info -->
        <section class="mb-5">
            <h2 class="mb-3 text-center">Our Story</h2>
            <p class="lead text-center">Coffee Shop started with a simple idea: bringing the finest coffee to your doorstep. Our passion is brewing high-quality coffee and creating a cozy experience for every customer. We believe in sustainability, community, and exceptional taste.</p>
        </section>

        <!-- Team Section -->
        <section class="mb-5">
            <h2 class="mb-4 text-center">Meet Our Team</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="team-card text-center p-4">
                        <i class="bi bi-person-circle" style="font-size: 3rem; color:#6f4e37;"></i>
                        <h5 class="mt-3">Siam Bashar</h5>
                        <p class="text-muted">Founder & CEO</p>
                        <p>Passionate about coffee and customer happiness. Leading Coffee Shop with vision and dedication.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-card text-center p-4">
                        <i class="bi bi-person-circle" style="font-size: 3rem; color:#6f4e37;"></i>
                        <h5 class="mt-3">Abhijeet Deb Nath</h5>
                        <p class="text-muted">Head Barista</p>
                        <p>Mastering the art of coffee brewing, creating unique flavors and ensuring top-quality beverages every day.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-card text-center p-4">
                        <i class="bi bi-person-circle" style="font-size: 3rem; color:#6f4e37;"></i>
                        <h5 class="mt-3">Md Shifat</h5>
                        <p class="text-muted">Operations Manager</p>
                        <p>Ensuring smooth operations, managing logistics, and making sure every order reaches our customers perfectly.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section>
            <h2 class="mb-4 text-center">Frequently Asked Questions</h2>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item faq-card">
                    <h2 class="accordion-header" id="faq1">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                            Do you offer delivery services?
                        </button>
                    </h2>
                    <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes! We deliver fresh coffee directly to your doorstep in select areas. Check our menu for delivery options.
                        </div>
                    </div>
                </div>

                <div class="accordion-item faq-card">
                    <h2 class="accordion-header" id="faq2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                            Can I customize my coffee order?
                        </button>
                    </h2>
                    <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Absolutely! Choose your milk, sweeteners, and flavor options when placing your order.
                        </div>
                    </div>
                </div>

                <div class="accordion-item faq-card">
                    <h2 class="accordion-header" id="faq3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                            Do you have a loyalty program?
                        </button>
                    </h2>
                    <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes, we reward our regular customers with discounts and special offers. Sign up for updates!
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>