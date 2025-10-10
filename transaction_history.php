<?php
session_start();
include_once 'config.php';

// Restrict to admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}
// Fetch transactions
$transactions = $conn->query("
    SELECT t.*, u.first_name, u.last_name
    FROM transaction1 t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
");
?>