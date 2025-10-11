<?php
/**
 * Coffee Shop - Order Tracking Page
 * -----------------------------------------------------
 * This file implements a complete, self-contained order tracking
 * experience for a Bootstrap-styled coffee shop site.
 *
 * Features
 * - Public tracking form: search by order code / email / phone
 * - Logged-in view: list of your orders with live-ish polling
 * - Order timeline with statuses (Placed → Confirmed → Brewing → Ready → Completed / Cancelled)
 * - Accessible markup and keyboard navigation
 * - Styled to match the provided landing page (fonts, colors, buttons)
 * - Defensive SQL (prepared statements), XSS-safe output helpers
 * - Optional demo data seeding (for local testing)
 * - Admin-lite actions behind a simple token (advance status)
 *
 * Notes
 * - Requires an orders table and order_items table; auto-creates if missing
 * - Compatible with the provided config.php (expects $conn = mysqli connection)
 * - Keep this file in project root alongside config.php and session setup
 * - You can safely trim sections if you don’t need them; comments are long on purpose
 *
 * Table Schemas (created automatically if missing)
 *   orders(
 *       id INT PK AI,
 *       order_code VARCHAR(16) UNIQUE,
 *       user_id INT NULL,
 *       customer_name VARCHAR(100),
 *       customer_email VARCHAR(160),
 *       customer_phone VARCHAR(40),
 *       total DECIMAL(10,2) DEFAULT 0,
 *       status ENUM('PLACED','CONFIRMED','BREWING','READY','COMPLETED','CANCELLED') DEFAULT 'PLACED',
 *       created_at DATETIME,
 *       updated_at DATETIME
 *   )
 *   order_items(
 *       id INT PK AI,
 *       order_id INT,
 *       product_name VARCHAR(120),
 *       quantity INT,
 *       price DECIMAL(10,2)
 *   )
 */

include 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

//---------------------------------------------
// Utilities
//---------------------------------------------
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function now(){ return date('Y-m-d H:i:s'); }
function rand_code($length=8){
    $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $code='';
    for($i=0;$i<$length;$i++) $code .= $alphabet[random_int(0, strlen($alphabet)-1)];
    return $code;
}

//---------------------------------------------
// DB Helpers (mysqli)
//---------------------------------------------
function db_exec($conn, $sql, $types='', $params=[]){
    $stmt = $conn->prepare($sql);
    if(!$stmt){ throw new Exception('SQL prepare failed: '.$conn->error); }
    if($types && $params){ $stmt->bind_param($types, ...$params); }
    if(!$stmt->execute()){ throw new Exception('SQL execute failed: '.$stmt->error); }
    return $stmt;
}

function table_exists($conn, $table){
    $stmt = db_exec($conn, "SHOW TABLES LIKE ?", 's', [$table]);
    $res = $stmt->get_result();
    return $res && $res->num_rows > 0;
}

function ensure_schema($conn){
    // orders table
    if(!table_exists($conn, 'orders')){
        $sql = "CREATE TABLE orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_code VARCHAR(16) NOT NULL UNIQUE,
            user_id INT NULL,
            customer_name VARCHAR(100) NOT NULL,
            customer_email VARCHAR(160) NOT NULL,
            customer_phone VARCHAR(40) NOT NULL,
            total DECIMAL(10,2) NOT NULL DEFAULT 0,
            status ENUM('PLACED','CONFIRMED','BREWING','READY','COMPLETED','CANCELLED') NOT NULL DEFAULT 'PLACED',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX(order_code),
            INDEX(user_id),
            INDEX(status),
            INDEX(created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $conn->query($sql);
    }
    // order_items table
    if(!table_exists($conn, 'order_items')){
        $sql = "CREATE TABLE order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_name VARCHAR(120) NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            INDEX(order_id),
            CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $conn->query($sql);
    }
}

ensure_schema($conn);

//---------------------------------------------
// Logged-in user helper
//---------------------------------------------
$user_name = '';
$user_id = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = db_exec($conn, "SELECT first_name FROM users WHERE id=?", 'i', [$user_id]);
    $r = $stmt->get_result();
    if($r && $r->num_rows){ $user_name = (string)$r->fetch_assoc()['first_name']; }
}

//---------------------------------------------
// Status helpers
//---------------------------------------------
const STATUS_FLOW = [
    'PLACED'     => 0,
    'CONFIRMED'  => 1,
    'BREWING'    => 2,
    'READY'      => 3,
    'COMPLETED'  => 4,
    'CANCELLED'  => 99
];

function status_label_class($status){
    switch($status){
        case 'PLACED': return 'secondary';
        case 'CONFIRMED': return 'info';
        case 'BREWING': return 'warning';
        case 'READY': return 'primary';
        case 'COMPLETED': return 'success';
        case 'CANCELLED': return 'danger';
        default: return 'secondary';
    }
}

function status_readable($status){
    return ucwords(strtolower(str_replace('_',' ', $status)));
}

//---------------------------------------------
// Query helpers
//---------------------------------------------
function get_order_by_code($conn, $code){
    $stmt = db_exec($conn, "SELECT * FROM orders WHERE order_code=?", 's', [$code]);
    $res = $stmt->get_result();
    return $res && $res->num_rows ? $res->fetch_assoc() : null;
}

function get_order_items($conn, $order_id){
    $stmt = db_exec($conn, "SELECT * FROM order_items WHERE order_id=? ORDER BY id ASC", 'i', [$order_id]);
    $res = $stmt->get_result();
    $items = [];
    while($row = $res->fetch_assoc()) $items[] = $row;
    return $items;
}

function get_orders_for_user($conn, $user_id, $limit=20){
    $stmt = db_exec($conn, "SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC LIMIT ?", 'ii', [$user_id, $limit]);
    $res = $stmt->get_result();
    $orders = [];
    while($row = $res->fetch_assoc()) $orders[] = $row;
    return $orders;
}

function insert_order($conn, $payload){
    $sql = "INSERT INTO orders (order_code, user_id, customer_name, customer_email, customer_phone, total, status, created_at, updated_at)
            VALUES (?,?,?,?,?,?,?,?,?)";
    $stmt = db_exec($conn, $sql, 'sisss dsss', [
        $payload['order_code'],
        $payload['user_id'],
        $payload['customer_name'],
        $payload['customer_email'],
        $payload['customer_phone'],
        $payload['total'],
        $payload['status'],
        $payload['created_at'],
        $payload['updated_at'],
    ]);
    return $conn->insert_id;
}

function insert_item($conn, $order_id, $name, $qty, $price){
    $stmt = db_exec($conn, "INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?,?,?,?)", 'isid', [
        $order_id, $name, $qty, $price
    ]);
}

function advance_status($conn, $order_id){
    $stmt = db_exec($conn, "SELECT status FROM orders WHERE id=?", 'i', [$order_id]);
    $res = $stmt->get_result();
    if(!$res || !$res->num_rows) return false;
    $row = $res->fetch_assoc();
    $status = $row['status'];
    $flow = ['PLACED','CONFIRMED','BREWING','READY','COMPLETED'];
    $next = 'COMPLETED';
    foreach($flow as $i => $st){
        if($st === $status){ $next = $flow[min($i+1, count($flow)-1)]; break; }
    }
    if($status === 'CANCELLED' || $status === 'COMPLETED') return $status;
    db_exec($conn, "UPDATE orders SET status=?, updated_at=? WHERE id=?", 'ssi', [$next, now(), $order_id]);
    return $next;
}

//---------------------------------------------
// Demo seeding (optional, dev only)
//---------------------------------------------
$seed_message = '';
if(isset($_GET['seed']) && $_GET['seed'] === '1'){
    try{
        $code = rand_code(8);
        $payload = [
            'order_code' => $code,
            'user_id' => $user_id ?: null,
            'customer_name' => $user_name ?: 'Guest',
            'customer_email' => $user_id ? ($user_name.'@example.com') : 'guest@example.com',
            'customer_phone' => '+0000000000',
            'total' => 0.00,
            'status' => 'PLACED',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $order_id = insert_order($conn, $payload);
        // add some items
        $menu = [
            ['Espresso', 1, 2.50],
            ['Cappuccino', 2, 3.75],
            ['Blueberry Muffin', 1, 2.10],
        ];
        $total = 0;
        foreach($menu as $m){ insert_item($conn, $order_id, $m[0], $m[1], $m[2]); $total += $m[1]*$m[2]; }
        db_exec($conn, "UPDATE orders SET total=? WHERE id=?", 'di', [$total, $order_id]);
        $seed_message = "Seeded demo order: ".$code;
    }catch(Exception $ex){ $seed_message = "Seed failed: ".$ex->getMessage(); }
}

//---------------------------------------------
// Handle actions (advance / cancel)
//---------------------------------------------
$admin_flash = '';
$ADMIN_TOKEN = getenv('ORDER_ADMIN_TOKEN') ?: 'dev-token-change-me'; // set in env for safety
if(isset($_POST['action']) && isset($_POST['order_id'])){
    $token = $_POST['token'] ?? '';
    if(hash_equals($ADMIN_TOKEN, $token)){
        $oid = (int)$_POST['order_id'];
        if($_POST['action'] === 'advance'){
            $new = advance_status($conn, $oid);
            $admin_flash = 'Advanced to: '.status_readable($new);
        }elseif($_POST['action'] === 'cancel'){
            db_exec($conn, "UPDATE orders SET status='CANCELLED', updated_at=? WHERE id=?", 'si', [now(), $oid]);
            $admin_flash = 'Order cancelled.';
        }
    } else {
        $admin_flash = 'Invalid admin token.';
    }
}

//---------------------------------------------
// Search handling
//---------------------------------------------
$search_result = null; $search_error='';
if(isset($_GET['track'])){
    $code = trim($_GET['code'] ?? '');
    $email = trim($_GET['email'] ?? '');
    $phone = trim($_GET['phone'] ?? '');

    $clauses=[]; $types=''; $params=[];
    if($code){ $clauses[] = 'order_code = ?'; $types.='s'; $params[]=$code; }
    if($email){ $clauses[] = 'customer_email = ?'; $types.='s'; $params[]=$email; }
    if($phone){ $clauses[] = 'customer_phone = ?'; $types.='s'; $params[]=$phone; }

    if(!$clauses){ $search_error = 'Please enter at least one field to search.'; }
    else{
        $sql = 'SELECT * FROM orders WHERE '.implode(' AND ', $clauses).' ORDER BY created_at DESC LIMIT 1';
        try{
            $stmt = db_exec($conn, $sql, $types, $params);
            $res = $stmt->get_result();
            if($res && $res->num_rows){ $search_result = $res->fetch_assoc(); }
            else { $search_error = 'No order found for the given details.'; }
        }catch(Exception $ex){ $search_error = 'Search failed: '.$ex->getMessage(); }
    }
}

//---------------------------------------------
// Component Renderers
//---------------------------------------------
function render_status_steps($order){
    $flow = ['PLACED','CONFIRMED','BREWING','READY','COMPLETED'];
    $current = $order['status'];
    echo '<div class="d-flex flex-column gap-3">';
    echo '<div class="progress" style="height: 10px;">';
    $percent = 0;
    if($current === 'CANCELLED'){ $percent = 100; }
    else{
        $idx = array_search($current, $flow);
        if($idx === false) $idx = 0;
        $percent = min(100, ($idx / (count($flow)-1)) * 100);
    }
    echo '<div class="progress-bar bg-'.e(status_label_class($current)).'" role="progressbar" style="width: '.(int)$percent.'%"></div>';
    echo '</div>';
    echo '<div class="d-flex justify-content-between">';
    foreach($flow as $st){
        $active = STATUS_FLOW[$st] <= (STATUS_FLOW[$current] ?? 0);
        $badge = $active ? 'bg-'.status_label_class($current) : 'bg-secondary-subtle text-secondary';
        echo '<span class="badge '.$badge.'">'.e(status_readable($st)).'</span>';
    }
    echo '</div>';
    if($current === 'CANCELLED'){
        echo '<div class="alert alert-danger mt-3"><i class="bi bi-x-octagon"></i> This order was cancelled.</div>';
    }elseif($current === 'COMPLETED'){
        echo '<div class="alert alert-success mt-3"><i class="bi bi-check-circle"></i> Order completed. Enjoy your coffee!</div>';
    }
    echo '</div>';
}

function render_items_table($conn, $order){
    $items = get_order_items($conn, (int)$order['id']);
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped align-middle">';
    echo '<thead><tr><th>#</th><th>Item</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Subtotal</th></tr></thead>';
    echo '<tbody>';
    $n=1; $total=0;
    foreach($items as $it){
        $sub = $it['quantity'] * $it['price'];
        $total += $sub;
        echo '<tr>';
        echo '<td>'.($n++).'</td>';
        echo '<td>'.e($it['product_name']).'</td>';
        echo '<td class="text-center">'.(int)$it['quantity'].'</td>';
        echo '<td class="text-end">$'.number_format($it['price'],2).'</td>';
        echo '<td class="text-end">$'.number_format($sub,2).'</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '<tfoot><tr><th colspan="4" class="text-end">Total</th><th class="text-end">$'.number_format($total,2).'</th></tr></tfoot>';
    echo '</table>';
    echo '</div>';
}

function render_order_card($conn, $order, $admin=false){
    $status = $order['status'];
    $badgeClass = status_label_class($status);
    echo '<div class="card shadow-sm mb-4">';
    echo '<div class="card-header d-flex justify-content-between align-items-center">';
    echo '<div><strong>Order #'.e($order['order_code']).'</strong> <span class="badge bg-'.e($badgeClass).'">'.e(status_readable($status)).'</span></div>';
    echo '<div class="text-muted small">Placed '.e(date('M j, Y g:i A', strtotime($order['created_at']))).'</div>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="row g-4">';
    echo '<div class="col-lg-7">';
    render_status_steps($order);
    echo '<hr/>';
    render_items_table($conn, $order);
    echo '</div>';
    echo '<div class="col-lg-5">';
    echo '<div class="bg-white p-3 rounded-3 border">';
    echo '<h6 class="mb-3">Customer</h6>';
    echo '<div class="d-flex flex-column small">';
    echo '<span><i class="bi bi-person"></i> '.e($order['customer_name']).'</span>';
    echo '<span><i class="bi bi-envelope"></i> '.e($order['customer_email']).'</span>';
    echo '<span><i class="bi bi-telephone"></i> '.e($order['customer_phone']).'</span>';
    echo '</div>';
    echo '<hr/>';
    echo '<h6 class="mb-3">Order Meta</h6>';
    echo '<div class="d-flex flex-column small">';
    echo '<span><i class="bi bi-cash-coin"></i> Total: $'.number_format($order['total'],2).'</span>';
    echo '<span><i class="bi bi-clock-history"></i> Updated: '.e(date('M j, Y g:i A', strtotime($order['updated_at']))).'</span>';
    echo '</div>';
    if($admin){
        echo '<hr/>';
        echo '<form method="post" class="d-flex gap-2">';
        echo '<input type="hidden" name="order_id" value="'.(int)$order['id'].'"/>';
        echo '<input type="hidden" name="token" value="'.e($_GET['token'] ?? '').'"/>';
        if(!in_array($status,['COMPLETED','CANCELLED'])){
            echo '<button name="action" value="advance" class="btn btn-sm btn-primary">Advance</button>';
            echo '<button name="action" value="cancel" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Cancel this order?\')">Cancel</button>';
        } else {
            echo '<div class="text-muted">No further actions available.</div>';
        }
        echo '</form>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

//---------------------------------------------
// HTML
//---------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking — Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body{ font-family: 'Roboto', sans-serif; background-color:#f9f7f1; }
        .logo{ font-family:'Pacifico',cursive; font-size:1.6rem; color:#6f4e37; }
        .btn-custom{ background:linear-gradient(90deg,#f5b041,#d68910); color:#fff; font-weight:600; transition: transform .3s, box-shadow .3s; }
        .btn-custom:hover{ transform: translateY(-3px); box-shadow:0 8px 20px rgba(0,0,0,.3); }
        .page-hero{ background: linear-gradient(rgba(0,0,0,.5), rgba(0,0,0,.5)), url('https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=1470&q=80') center/cover no-repeat; color:#fff; padding:60px 0; text-align:center; text-shadow:2px 2px 8px rgba(0,0,0,.8); }
        .feature-card{ background-color:#fff3e0; border-radius:15px; padding:20px; }
        .card{ border-radius: 16px; }
        .search-card{ background:#fff3e06b; border:1px solid #ffe6c3; }
        .order-chip{ background:#fff; border:1px dashed #d68910; padding:.25rem .5rem; border-radius:999px; font-size:.85rem; }
        .loader { width: 22px; height: 22px; border-radius:50%; border: 3px solid #d9d9d9; border-top-color:#d68910; animation: spin 1s linear infinite; }
        @keyframes spin{ to { transform: rotate(360deg);} }

        /* accessible focus outlines */
        a:focus, button:focus, input:focus, textarea:focus, select:focus { outline: 3px dashed #d68910 !important; outline-offset: .15rem; }

        /* timeline dots */
        .timeline-dot{ width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:6px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container">
        <a class="navbar-brand logo" href="index.php">☕ Coffee Shop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="seat_reservation.php">Reservation</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                <li class="nav-item"><a class="nav-link active" href="#">Track Order</a></li>
                <?php if (!empty($user_name)): ?>
                    <li class="nav-item"><span class="nav-link text-warning fw-bold">Hello, <?= e($user_name) ?></span></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<section class="page-hero">
    <div class="container">
        <h1 class="fw-bold">Track Your Coffee Order</h1>
        <p class="lead">Enter your order code or sign in to see your recent orders.</p>
        <?php if($seed_message): ?>
            <div class="alert alert-warning d-inline-block mt-3 py-2 px-3"><?= e($seed_message) ?></div>
        <?php endif; ?>
    </div>
</section>

<main class="container my-5">
    <?php if(isset($_GET['token'])): ?>
        <div class="alert alert-info"><i class="bi bi-shield-lock"></i> Admin-lite controls enabled for this view.</div>
    <?php endif; ?>

    <?php if($admin_flash): ?>
        <div class="alert alert-success"><i class="bi bi-check2-circle"></i> <?= e($admin_flash) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card search-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Find Your Order</h5>
                    <form class="needs-validation" novalidate>
                        <input type="hidden" name="track" value="1"/>
                        <div class="mb-3">
                            <label class="form-label" for="code">Order Code</label>
                            <input class="form-control" id="code" name="code" placeholder="e.g. 7H3K9A2B" value="<?= e($_GET['code'] ?? '') ?>">
                            <div class="form-text">Use the code shown after checkout or in your email/SMS receipt.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control" id="email" name="email" type="email" placeholder="you@example.com" value="<?= e($_GET['email'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="phone">Phone</label>
                            <input class="form-control" id="phone" name="phone" placeholder="+123..." value="<?= e($_GET['phone'] ?? '') ?>">
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-custom" type="submit"><i class="bi bi-search"></i> Track</button>
                            <a class="btn btn-outline-secondary" href="order_tracking.php"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                            <a class="btn btn-outline-warning" href="?seed=1"><i class="bi bi-egg-fried"></i> Seed Demo</a>
                        </div>
                    </form>
                    <?php if($search_error): ?>
                        <div class="alert alert-danger mt-3"><?= e($search_error) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($user_id): ?>
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Quick Tip</h5>
                    <p class="mb-0 small text-muted">Since you’re logged in, you can also scroll to <strong>Your Recent Orders</strong> to review all orders tied to your account.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-7">
            <?php if($search_result): ?>
                <?php render_order_card($conn, $search_result, isset($_GET['token'])); ?>
            <?php else: ?>
                <div class="feature-card shadow-sm h-100 d-flex flex-column justify-content-center align-items-center text-center">
                    <i class="bi bi-cup-straw mb-2" style="font-size:3rem;color:#d68910"></i>
                    <h5 class="fw-bold">Track an Order</h5>
                    <p class="mb-0">Enter details on the left to see status and items.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($user_id): ?>
    <section class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Your Recent Orders</h4>
            <button class="btn btn-outline-dark btn-sm" id="refreshOrders"><i class="bi bi-arrow-repeat"></i> Refresh</button>
        </div>
        <div id="ordersContainer">
            <?php
            $orders = get_orders_for_user($conn, $user_id, 10);
            if(!$orders){ echo '<div class="alert alert-secondary">No orders yet. Visit the <a href="menu.php">menu</a> to place one!</div>'; }
            foreach($orders as $o){ render_order_card($conn, $o, isset($_GET['token'])); }
            ?>
        </div>
    </section>
    <?php endif; ?>
</main>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p>&copy; <?= date('Y'); ?> Coffee Shop. All Rights Reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Client-side helpers for small UX boosts
(function(){
  const form = document.querySelector('form.needs-validation');
  if(form){
    form.addEventListener('submit', function(e){
      // Simple client validation: at least one field present
      const code = form.querySelector('[name="code"]').value.trim();
      const email = form.querySelector('[name="email"]').value.trim();
      const phone = form.querySelector('[name="phone"]').value.trim();
      if(!code && !email && !phone){
        e.preventDefault();
        alert('Please enter at least one field to search.');
      }
    });
  }

  // Polling refresh for logged-in order list ("live" updates)
  const refreshBtn = document.getElementById('refreshOrders');
  const container = document.getElementById('ordersContainer');
  let timer = null;

  if(refreshBtn && container){
    const fetchOrders = () => {
      refreshBtn.disabled = true;
      const oldHtml = refreshBtn.innerHTML; 
      refreshBtn.innerHTML = '<span class="loader"></span>';
      fetch('order_tracking_refresh.php', { credentials: 'same-origin' })
        .then(r => r.text())
        .then(html => { container.innerHTML = html; })
        .catch(() => {})
        .finally(() => { refreshBtn.disabled = false; refreshBtn.innerHTML = oldHtml; });
    };
    refreshBtn.addEventListener('click', fetchOrders);

    // Light polling every 25s
    timer = setInterval(fetchOrders, 25000);
    window.addEventListener('beforeunload', ()=>{ if(timer) clearInterval(timer); });
  }
})();
</script>

</body>
</html>

<?php
//---------------------------------------------
// Optional: lightweight endpoint output (server-rendered snippet for refresh)
//---------------------------------------------
// Create a companion file named order_tracking_refresh.php with the following content
// if you want the auto-refresh section to work. Placing it here as reference:
/*
<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
include 'order_tracking_lib.php'; // optional: move PHP helpers there if you split files

// Minimal inline for demo; in production, reuse helpers
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function status_label_class($s){ switch($s){case 'PLACED':return 'secondary';case 'CONFIRMED':return 'info';case 'BREWING':return 'warning';case 'READY':return 'primary';case 'COMPLETED':return 'success';case 'CANCELLED':return 'danger';default:return 'secondary';} }
function status_readable($s){ return ucwords(strtolower(str_replace('_',' ',$s))); }

$conn = $conn; // from config
$user_id = $_SESSION['user_id'] ?? null;
if(!$user_id){ http_response_code(403); exit('Login required'); }

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();

while($o = $res->fetch_assoc()){
  echo '<div class="card shadow-sm mb-4">';
  echo '<div class="card-header d-flex justify-content-between align-items-center">';
  echo '<div><strong>Order #'.e($o['order_code']).'</strong> <span class="badge bg-'.e(status_label_class($o['status'])).'">'.e(status_readable($o['status'])).'</span></div>';
  echo '<div class="text-muted small">Placed '.e(date('M j, Y g:i A', strtotime($o['created_at']))).'</div>';
  echo '</div>';
  echo '<div class="card-body"><em>Items omitted in refresh for brevity</em></div>';
  echo '</div>';
}
?>
*/

// End of file
?>
