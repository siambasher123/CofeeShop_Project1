<?php 
include 'config.php'; 

// Safe session start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle AJAX reservation
if(isset($_POST['action']) && $_POST['action'] == 'reserve'){
    $seats = json_decode($_POST['seats'], true);

    // Make sure table exists
    $conn->query("CREATE TABLE IF NOT EXISTS `seat_reservations` (
      `id` INT NOT NULL AUTO_INCREMENT,
      `user_id` INT NOT NULL,
      `seat_row` INT NOT NULL,
      `seat_col` INT NOT NULL,
      `reserved_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      FOREIGN KEY (`user_id`) REFERENCES `users1`(`id`) ON DELETE CASCADE
    )");

    foreach($seats as $seat){
        $row = intval($seat['row']);
        $col = intval($seat['col']);
        // Check if seat is already reserved
        $check = $conn->prepare("SELECT id FROM seat_reservations WHERE seat_row=? AND seat_col=?");
        $check->bind_param("ii",$row,$col);
        $check->execute();
        $check->store_result();
        if($check->num_rows == 0){
            $stmt = $conn->prepare("INSERT INTO seat_reservations (user_id, seat_row, seat_col) VALUES (?, ?, ?)");
            $stmt->bind_param("iii",$user_id,$row,$col);
            $stmt->execute();
        }
    }
    echo json_encode(['status'=>'success']);
    exit;
}

// Fetch reserved seats safely
$reserved = [];
$table_check = $conn->query("SHOW TABLES LIKE 'seat_reservations'");
if($table_check->num_rows > 0){
    $result = $conn->query("SELECT seat_row, seat_col FROM seat_reservations");
    while($row = $result->fetch_assoc()){
        $reserved[] = ['row'=>$row['seat_row'],'col'=>$row['seat_col']];
    }
}
$reserved_json = json_encode($reserved);
?>