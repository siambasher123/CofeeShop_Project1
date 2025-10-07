<?php
session_start();
include_once 'config.php';

// Handle form submission
$success_msg = "";
if(isset($_POST['submit'])){
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    

    $sql = "INSERT INTO contacts (name, email, message) VALUES ('$name', '$email', '$message')";
    if($conn->query($sql)){
        $success_msg = "Your message has been sent successfully!";
    } else {
        $success_msg = "Error sending message. Please try again.";
    }
}
?>


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
                <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>


<!-- Hero Section -->
<div class="contact-hero mb-5">
    <h1 class="display-4">We’d Love to Hear From You</h1>
</div>

<div class="container">
    <div class="row mb-5">
        <!-- Contact Info -->
        <div class="col-md-4">
            <div class="info-box">
                <i class="bi bi-geo-alt-fill"></i>
                <h5>Our Location</h5>
                <p>123 Coffee St, Brewtown, USA</p>
            </div>
            <div class="info-box">
                <i class="bi bi-envelope-fill"></i>
                <h5>Email Us</h5>
                <p>info@coffeebliss.com</p>
            </div>
            <div class="info-box">
                <i class="bi bi-telephone-fill"></i>
                <h5>Call Us</h5>
                <p>+1 234 567 890</p>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-md-8">
            <div class="contact-card">
                <h3 class="mb-4 text-center">Send a Message</h3>

                <?php if($success_msg): ?>
                    <div class="alert alert-success text-center"><?php echo $success_msg; ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="Your Name">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="your@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Write your message here..."></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-warning btn-lg w-50">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>