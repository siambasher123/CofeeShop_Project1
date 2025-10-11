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
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seat Reservation - Coffee Bliss</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Roboto', sans-serif; background-color: #f9f7f1; }
.navbar-custom { background-color: #6f4e37; }
.logo { font-family: 'Pacifico', cursive; font-size: 2rem; color: white; }

.seat { width: 40px; height: 40px; margin: 5px; background-color: #6f4e37; border-radius: 5px; cursor: pointer; display: inline-block; }
.seat.selected { background-color: #f5b041; }
.seat.reserved { background-color: #d3d3d3; cursor: not-allowed; }
.seat-row { display: flex; justify-content: flex-start; flex-wrap: wrap; margin-bottom: 10px; }
.gap { width: 40px; height: 40px; margin: 5px; background: transparent; }

.reservation-section { background: #fff8f0; padding: 30px; border-radius: 15px; margin:50px auto; max-width:900px; text-align:center; position: relative; }
.back-btn { position: absolute; top: 20px; left: 20px; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
<div class="container">
    <a class="navbar-brand logo" href="index.php">☕ Coffee Bliss</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" >
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>

            <li class="nav-item"><a class="nav-link active" href="seat_reservation.php">Reservation</a></li>
            <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</div>
</nav>

<section class="container reservation-section">
    <button class="btn btn-secondary back-btn" onclick="window.history.back()">← Back</button>
    <h2 class="fw-bold mb-4">Reserve Your Seat</h2>

    <div class="mb-4">
        <label>Select Group Size:</label>
        <select id="groupSize" class="form-select w-auto d-inline-block">
            <option value="1">1 Person</option>

            <option value="2">2 (Couple)</option>
            <option value="3">3 Persons</option>
            <option value="4">4 (Family)</option>
            <option value="5">5+ (Friends)</option>
        </select>
        <button class="btn btn-custom ms-2" onclick="showSeats()">Show Seats</button>
    </div>

    <div id="seatContainer"></div>
    <button class="btn btn-primary mt-3" onclick="reserveSeats()">Reserve Seats</button>
</section>

<script>
const seatMatrix = [
    [1,1,0,1,1,1,0,1,1,1,1,0,1,1,1,0,1,1,1,1],

    [1,0,1,1,1,0,1,1,0,1,1,1,0,1,1,1,0,1,1,1],

    [1,1,1,0,1,1,1,0,1,1,1,0,1,1,1,0,1,1,1,1],

    [1,1,0,1,1,1,0,1,1,0,1,1,1,0,1,1,1,0,1,1],

    [1,1,1,0,1,1,1,0,1,1,1,0,1,1,1,0,1,1,1,1],

    [1,0,1,1,1,0,1,1,0,1,1,1,0,1,1,1,0,1,1,1],

    [1,1,1,0,1,1,1,0,1,1,1,0,1,1,1,0,1,1,1,1]
];

let reservedSeats = <?= $reserved_json ?>;

function showSeats() {
    const container = document.getElementById('seatContainer');
    container.innerHTML = '';

    seatMatrix.forEach((row, r) => {
        const rowDiv = document.createElement('div');
        rowDiv.classList.add('seat-row');
        row.forEach((seat, c) => {

            const seatDiv = document.createElement('div');
            if(seat === 1){
                seatDiv.classList.add('seat');

                if(reservedSeats.some(s => s.row===r && s.col===c)){
                    seatDiv.classList.add('reserved');
                } else {
                    seatDiv.onclick = () => seatDiv.classList.toggle('selected');
                }
            } else {
                seatDiv.classList.add('gap');
            }
            rowDiv.appendChild(seatDiv);
        });
        container.appendChild(rowDiv);
    });
}

function reserveSeats() {
    const selectedSeats = [];
    document.querySelectorAll('.seat.selected').forEach(seatDiv => {

        const rowDiv = seatDiv.parentElement;
        const r = Array.from(rowDiv.parentElement.children).indexOf(rowDiv);
        const c = Array.from(rowDiv.children).indexOf(seatDiv);
        selectedSeats.push({row: r, col: c});
    });

    if(selectedSeats.length === 0){
        alert("Please select at least one seat.");
        return;
    }

    fetch('seat_reservation.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=reserve&seats=' + encodeURIComponent(JSON.stringify(selectedSeats))
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.status==='success'){
            alert("Seats reserved successfully!");
            reservedSeats = reservedSeats.concat(selectedSeats);

            showSeats();
        } else {
            alert("Error reserving seats.");
        }
    });
}

window.onload = showSeats;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>