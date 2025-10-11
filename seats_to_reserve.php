<?php
include 'config.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role']!='admin') header("Location: login.php");

// Fetch all reserved seats

$reserved = [];
$result = $conn->query("
    SELECT r.seat_row, r.seat_col, r.reserved_at, u.first_name AS user_name
    FROM seat_reservations r
    JOIN users u ON r.user_id = u.id
");
while($row = $result->fetch_assoc()){
    $reserved[] = [
        'row'=>$row['seat_row'],
        'col'=>$row['seat_col'],
        'user'=>$row['user_name'],
        'time'=>$row['reserved_at']
    ];
}
$reserved_json = json_encode($reserved);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Seat Reservations</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>

.seat { width:40px; height:40px; margin:5px; border-radius:5px; display:inline-block; text-align:center; line-height:40px; cursor:default; background:#6f4e37; color:white; }
.seat.reserved { background:#d9534f; }
.seat-row { display:flex; flex-wrap:wrap; margin-bottom:10px; }
.gap { width:40px; height:40px; margin:5px; background:transparent; }
.dashboard-section { max-width:900px; margin:50px auto; padding:30px; background:#fff8f0; border-radius:15px; text-align:center; position:relative; }
.back-btn { position:absolute; top:20px; left:20px; }
</style>
</head>
<body>
<section class="dashboard-section">
    <!-- Back Button -->
    <button class="btn btn-secondary back-btn" onclick="window.location.href='admin_dashboard.php'">← Back</button>

    <h2>Seat Reservations</h2>
    <div id="seatContainer"></div>

    <h4 class="mt-4 mb-3">Reservation Details</h4>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>User Name</th>
                <th>Seat Row</th>
                <th>Seat Column</th>
                <th>Reserved At</th>
            </tr>
        </thead>
        <tbody>
            <?php $c=1; foreach($reserved as $r){
                echo "<tr>
                    <td>{$c}</td>
                    <td>{$r['user']}</td>
                    <td>".($r['row']+1)."</td>

                    <td>".($r['col']+1)."</td>
                    <td>{$r['time']}</td>
                </tr>";
                $c++;
            } ?>
        </tbody>
    </table>
</section>

<script>
// Define full seat matrix (adjust rows/cols as per your coffee shop layout)
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

function showSeats(){
    const container = document.getElementById('seatContainer');
    container.innerHTML = '';
    seatMatrix.forEach((row,r)=>{
        const rowDiv = document.createElement('div');
        rowDiv.classList.add('seat-row');
        row.forEach((seat,c)=>{
            const seatDiv = document.createElement('div');
            if(seat===1){
                seatDiv.classList.add('seat');
                const res = reservedSeats.find(s=>s.row==r && s.col==c);
                if(res){
                    seatDiv.classList.add('reserved');
                    seatDiv.title = `${res.user} at ${res.time}`;
                    seatDiv.innerHTML = res.user.charAt(0);
                }
            } else seatDiv.classList.add('gap');
            rowDiv.appendChild(seatDiv);
        });
        container.appendChild(rowDiv);
    });
}
window.onload = showSeats;
</script>
</body>
</html>
