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
* orders(
* id INT PK AI,
* order_code VARCHAR(16) UNIQUE,
* user_id INT NULL,
* customer_name VARCHAR(100),
* customer_email VARCHAR(160),
* customer_phone VARCHAR(40),
* total DECIMAL(10,2) DEFAULT 0,
* status ENUM('PLACED','CONFIRMED','BREWING','READY','COMPLETED','CANCELLED') DEFAULT 'PLACED',
* created_at DATETIME,
* updated_at DATETIME
* )
* order_items(
* id INT PK AI,
* order_id INT,
* product_name VARCHAR(120),
* quantity INT,
* price DECIMAL(10,2)
* )
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