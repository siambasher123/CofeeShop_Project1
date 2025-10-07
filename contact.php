<?php
session_start();
include_once 'config.php';

// Handle form submission
$success_msg = "";
if(isset($_POST['submit'])){
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO contacts (name, email, message) VALUES ('$name', '$email', '$message')";
    if($conn->query($sql)){
        $success_msg = "Your message has been sent successfully!";
    } else {
        $success_msg = "Error sending message. Please try again.";
    }
}
?>



</div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>