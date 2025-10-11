# CoffeeShop— README

---

## Table of Contents

* [Project Overview](#project-overview)
* [Live Demo / Screens](#live-demo--screens)
* [Tech Stack](#tech-stack)
* [Key Features](#key-features)
* [Architecture](#architecture)
* [Folder & File Map](#folder--file-map)
* [Setup — Windows (XAMPP)](#setup--windows-xampp)
* [Setup — Linux (LAMP)](#setup--linux-lamp)
* [Environment & Secrets](#environment--secrets)
* [Database Schema](#database-schema)
* [ER Diagram (text description)](#er-diagram-text-description)
* [Sample Data](#sample-data)
* [User Roles & Permissions](#user-roles--permissions)
* [Authentication Flow](#authentication-flow)
* [Business Flows](#business-flows)
* [Endpoints & Routes](#endpoints--routes)
* [Pages & What They Do](#pages--what-they-do)
* [Seat Reservation Module](#seat-reservation-module)
* [Cart & Ordering](#cart--ordering)
* [Admin Dashboard](#admin-dashboard)
* [Discount Rules](#discount-rules)
* [Validation Rules](#validation-rules)
* [Security Notes](#security-notes)
* [Testing Guide](#testing-guide)
* [Release Notes / Changelog](#release-notes--changelog)
* [FAQ](#faq)
* [Troubleshooting](#troubleshooting)
* [Roadmap](#roadmap)
* [Contributing](#contributing)
* [License](#license)
* [Acknowledgments](#acknowledgments)

---

## Project Overview

The **CoffeeShop** application is a small-but-complete web app that demonstrates how to run a menu-driven ordering system with login, cart, checkout, seat reservation, and a lightweight admin back office. It is intentionally written with straightforward PHP + MySQL so learners can trace every request from page load → database query → HTML output without framework abstractions.

**What problems it solves**

* Lets customers **browse the menu**, add items to a **session-backed cart**, and place orders.
* Provides a simple **seat reservation** workflow for in‑store dining.
* Gives admins a clear **dashboard** to manage products, view orders, and **apply discounts**.
* Records **transaction history** and basic contact messages for later review.
* Demonstrates **secure defaults** (prepared statements, basic input validation, session handling) suitable for coursework and small demos.

**Who it’s for**

* Students learning end‑to‑end web app structure with PHP, HTML/CSS/JS, and MySQL.
* Instructors who want a concrete codebase to discuss sessions, cookies, forms, state, and persistence.
* Hobbyists prototyping a coffee shop / small F&B ordering flow.

**High‑level capabilities**

* **Authentication**: login, signup, logout; session-based user identity; basic role separation (customer vs admin).
* **Catalog**: menu listing, product details (name, price, availability), image placeholders.
* **Cart**: add/remove/update quantities; price calculation with subtotal and discount application.
* **Ordering**: place order, store to DB, view order list; simple status updates by admin.
* **Seat Reservation**: choose seats, avoid conflicts, record reservations.
* **Discounts**: admin can define flat/percent discounts and apply constraints.
* **Admin Tools**: add/edit products, monitor orders, review contacts, basic KPIs.
* **Contact/Info Pages**: contact form for messages; about page for branding copy.

**Non‑goals (by design, to keep it teachable)**

* No heavy frameworks or ORMs; raw PHP + mysqli/PDO so logic is transparent.
* No payment gateway integration (can be added later).
* Minimal JS; server‑rendered pages first, then progressive enhancements as exercises.

**Learning highlights**

* How **sessions and cookies** track user state between requests.
* How to structure **form handling** (POST) and sanitize inputs.
* How to layer **validation, business rules, and DB writes**.
* How to design a **schema** for items, orders, order_items, users, and reservations.
* How to build a maintainable **README** and developer docs incrementally.

## Live Demo / Screens

> **Demo URL:** http://localhost/coffeeshop

1. **Home / Landing (`index.php`)**

   * Highlights featured drinks and entry points to **Menu** and **Login**.
   * Shows current user state (Guest vs Logged‑in) in the navbar.
2. **Login (`login.php`)**

   * Email/username + password form, server‑side error messages, redirect behavior.
   * Note how failed attempts are handled and what session keys are set on success.
3. **Signup (`signup.php`)**

   * Required fields, password rules, duplicate email handling.
   * Post‑signup redirect to dashboard or menu.
4. **Menu (`menu.php`)**

   * Product cards (name, price); **Add to Cart** buttons.
   * Empty‑state behavior when no items exist.
5. **Cart (`cart.php`)**

   * Line items with quantities, remove/update controls, subtotal.
   * Discount indicator (if any) and proceed‑to‑checkout button.
6. **Order List / Summary (`order_list.php`)**

   * Orders placed by the current user; basic statuses (e.g., pending/served).
   * Link to **Transaction History** when available.
7. **Seat Reservation (`seat_reservation.php` & `seats_to_reserve.php`)**

   * Seat map or list; selection, conflict messages, and confirmation state.
   * Show a successful reservation record and how it appears to admin.
8. **Admin Dashboard (`admin_dashboard.php`)**

   * Quick stats (orders today, revenue placeholder, active reservations).
   * Navigation to product CRUD, discounts, contacts.
9. **Add Products (`add_products.php`)**

   * Create/edit form; validation failures; success messages.
   * Example image or placeholder handling.
10. **Give Discount (`give_discount.php`)**

    * Flat vs percent toggle; scope/eligibility; preview of final price effect.
11. **Transaction History (`transaction_history.php`)**

    * Chronological list of transactions with totals and discount notes.
12. **Contact & About (`contact.php`, `contact_list.php`, `about.php`)**

    * User contact form; admin inbox listing; static about text with branding.

## Tech Stack

### Languages & Runtime

* **PHP**: 8.1–8.3 recommended (works on 7.4+, but password/APIs and typing are better on 8.x).
* **MySQL / MariaDB**: MySQL 8.0+ (or MariaDB 10.5+). Default collation: `utf8mb4_unicode_ci`.
* **Web Server**: Apache 2.4 (via XAMPP on Windows; LAMP on Linux).
* **Frontend**: HTML5, CSS3, vanilla JavaScript (no heavy framework).
* **CLI tools**: `php`, `mysql`, `git`.

### PHP Extensions / Functions Used

* **mysqli** (or **PDO**): database access with prepared statements.
* **session**: session-based authentication/state (`session_start()`).
* **password_hash / password_verify**: secure password storage (bcrypt).
* **filter_input / filter_var**: input sanitization.
* **intl** (optional): formatting/collations if needed.
* **openssl** (optional): stronger random bytes / tokens.

### Dev Environments

* **Windows (XAMPP)**: Apache + PHP + MySQL in one bundle; phpMyAdmin included.
* **Linux (LAMP)**: `apache2`, `php`, `php-mysqli`/`php-mysql`, `mysql-server`.
* **phpMyAdmin**: convenient DB admin in dev; optional in prod.

### Directory Conventions (selected)

* `/` — public document root (all `.php` in this project are public for simplicity).
* `config.php` — DB credentials and shared configuration.
* Future-friendly: move non-public helpers into a separate folder and include via PHP `require`.

### Configuration (via `config.php`)

Expected keys:

```php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');          // set a password in real deployments
define('DB_NAME', 'coffeeshop');
define('APP_ENV', 'local');      // local|production
date_default_timezone_set('Asia/Dhaka'); // adjust to your locale
```

* **Charset**: all DB connections use `utf8mb4` to support full Unicode (emojis, symbols).
* **Env flag**: `APP_ENV` toggles dev-friendly error output vs production-safe display.

### Error Handling Defaults

* **Development**:

  ```php
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  ```
* **Production**:

  ```php
  error_reporting(E_ALL);
  ini_set('display_errors', '0'); // log errors instead of displaying
  ```
* Consider writing errors to an `error_log` file (Apache or custom).

### Sessions & Cookies

* **Session ID**: default cookie `PHPSESSID`.
* **Lifetime**: defaults are fine for dev; can be tuned with `session.cookie_lifetime`.
* **Security**: set `httponly`, `secure` (when using HTTPS), and regenerate on login:

  ```php
  session_start();
  // after successful login:
  session_regenerate_id(true);
  $_SESSION['user_id'] = $user['id'];
  $_SESSION['role']    = $user['role']; // 'customer' | 'admin'
  ```

### Database Access (mysqli example)

Connection + prepared statements:

```php
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die('DB connection failed: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

// sample: read one product
$stmt = $mysqli->prepare('SELECT id, name, price FROM products WHERE id = ?');
$stmt->bind_param('i', $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();
```

### Password Storage

* Use `password_hash()` (bcrypt by default) and `password_verify()`:

```php
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);
// later:
if (password_verify($loginPassword, $hashFromDb)) {
    // ok
}
```

### Frontend Notes

* CSS is intentionally simple; you can plug in **Bootstrap 5** via CDN if desired:

  ```html
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" rel="stylesheet">
  ```
* Minimal JS (form validation hints, small interactions) to keep server-rendered flow teachable.

### Browser / Platform Support

* Recent Chrome/Edge/Firefox (desktop). Works on mobile, but layout is basic by design.

### Why This Stack

* **Transparent learning**: raw PHP + MySQL makes control flow and security concerns visible.
* **Easy setup**: XAMPP/LAMP are widely available; no build step required.
* **Portable**: runs on modest hardware and common hosting.




## Key Features

### Customer Experience
- **Home / Landing**: clear entry points to Menu, Reservation, Cart, About, Contact, and Login.
- **Menu browsing** (`menu.php`): list of items with name, price, short description/notes.
- **Add to Cart**: one-click add; quantity defaults to 1 with ability to update later.
- **Order history** (`order_list.php`): see previously placed orders and basic status.
- **Contact form** (`contact.php`): submit a message; admins can review in inbox.
- **About** (`about.php`): simple static info page for brand/story.

### Authentication & Session
- **Signup** (`signup.php`): create account with server-side validation and hashed passwords.
- **Login** (`login.php`): email/username + password using `password_verify()`.
- **Logout** (`logout.php`): clears session and regenerates the ID.
- **Session keys**: `$_SESSION['user_id']`, `$_SESSION['role']` (`customer`/`admin`), and minimal profile info.
- **Session hygiene**: `session_regenerate_id(true)` on successful login to mitigate fixation.

### Cart & Ordering
- **Cart page** (`cart.php`): view items, update quantities, remove items.
- **Pricing summary**: shows item totals and cart **subtotal**; discount display if applicable.
- **Checkout flow**: confirm cart, write order + order items to the database, show confirmation in `order_list.php`.
- **Transaction log** (`transaction_history.php`): basic record of completed transactions.

### Seat Reservation
- **Seat selection** (`seat_reservation.php`, `seats_to_reserve.php`): choose seat(s) from available list/map.
- **Conflict prevention**: server-side checks to avoid double booking.
- **Confirmation state**: successful reservation persisted to DB and visible to admin.
- **Edge cases**: already reserved seats, invalid seat IDs, stale selections handled on the server.

### Admin & Back Office
- **Dashboard** (`admin_dashboard.php`): quick stats and navigation to product, discount, order, and contact modules.
- **Products** (`add_products.php`): create/edit basic product fields (name, price, availability/notes).
- **Discounts** (`give_discount.php`): define **flat** or **percent** discounts with simple rules.
- **Order monitoring**: review orders and update basic status fields.
- **Contact inbox** (`contact_list.php`): list and triage messages from users.

### Validation & Security
- **Prepared statements**: all DB reads/writes use `mysqli` prepared queries to reduce SQL injection risk.
- **Output escaping**: HTML-escape user-supplied values before rendering.
- **Password hashing**: `password_hash()` + `password_verify()` (bcrypt).
- **Error modes**: dev vs prod (`APP_ENV`) toggles display/logging; production avoids leaking stack traces.
- **Cookie flags**: encourage `httponly` and `secure` (when HTTPS) for session cookies.

### Configuration & Environment
- **Single point of config** (`config.php`): DB host, name, user, pass; `APP_ENV`; timezone.
- **UTF-8 everywhere**: sets `utf8mb4` on connections for full Unicode support (emoji, symbols).
- **Portable setup**: runs on XAMPP (Windows) or LAMP (Linux) without extra build tooling.

### Extensibility (future-friendly)
- **Optional modules**: payment gateway integration, search/filtering for the menu, responsive UI polish.
- **Docs-first approach**: each page/module has a dedicated README subsection (see “Pages & What They Do” and “Endpoints & Routes”).





## Architecture

### High-Level Components
- **Client (Browser)**: Renders HTML/CSS/JS. Sends form POSTs and simple GET requests.
- **Web Server**: Apache 2.4 serving `.php` files.
- **Application Runtime**: PHP (procedural pages) executing per-request. Uses `mysqli`/PDO for DB access.
- **Database**: MySQL 8+/MariaDB for persistent data (users, products, orders, order_items, reservations, contacts, discounts).
- **Session Store**: PHP default (file-based) keyed by `PHPSESSID` cookie.
- **Static Assets**: Served by Apache (images/CSS/JS). No build pipeline required.

### Request Lifecycle (typical page)
1. **HTTP request** arrives at `*.php` (e.g., `menu.php`).
2. PHP **loads config** (`config.php`), sets timezone/charset, connects to DB.
3. PHP **starts session** (`session_start()`), reads `$_SESSION` (auth, role).
4. If the page accepts **form input** (POST/GET), PHP reads, validates, and sanitizes data.
5. Application performs **DB queries** using prepared statements.
6. Business rules run (e.g., pricing, seat-availability checks).
7. PHP renders **HTML** with safely escaped dynamic content.
8. On mutations (login, add-to-cart, checkout), PHP may **set session** keys and **redirect**.
9. Response is sent. Browser updates DOM (minimal JS) or navigates to next page.
10. Errors in dev show as messages; in prod they are hidden/logged.

### Data Flow (common)
- **Read-mostly pages** (e.g., `menu.php`) → `SELECT ... FROM products`.
- **Mutations** (e.g., `cart.php` updates) → write to session or DB, then redirect (POST/Redirect/GET).
- **Checkout** → create `orders` + `order_items` rows in a single logical unit of work.
- **Seat reservation** → validate seat availability, insert reservation row, return confirmation.

### State & Identity
- **Auth cookie**: `PHPSESSID` only (no JWT). Identity lives in `$_SESSION`.
- **Session keys**:
  - `$_SESSION['user_id']`: numeric user primary key.
  - `$_SESSION['role']`: `'customer' | 'admin'`.
  - optional flash: `$_SESSION['flash_success']`, `$_SESSION['flash_error']`.
- **Login flow**: verify password hash → set session → `session_regenerate_id(true)` → redirect.
- **Logout**: unset keys and/or destroy session → redirect to `index.php`.

### Pages & Responsibilities (runtime graph)
```

index.php
├─ menu.php ──┬─ cart.php ── checkout (writes orders, order_items) ──► order_list.php
│             └─ give_discount.php (admin only; affects pricing rules)
├─ seat_reservation.php ──► seats_to_reserve.php (validate & persist reservation)
├─ login.php / signup.php ──► (set session) ──► index.php / menu.php
├─ contact.php ──► (store message) ──► contact_list.php (admin only)
└─ admin_dashboard.php ──► add_products.php / transaction_history.php / contact_list.php

```

### Security Boundaries
- **Input validation**: `filter_input()` / explicit checks on all POST/GET fields.
- **SQL injection defense**: prepared statements for **every** query.
- **XSS defense**: escape on output (HTML context) for user-supplied values.
- **Session hardening**:
  - `session_regenerate_id(true)` on login.
  - Use `httponly` cookie; set `secure` under HTTPS.
- **Authorization**:
  - Guard admin pages (`admin_dashboard.php`, `give_discount.php`, etc.) by checking `$_SESSION['role'] === 'admin'`.
  - Guard customer-only flows by checking `$_SESSION['user_id']`.
- **Error exposure**:
  - Dev: show errors (`display_errors=1`).
  - Prod: hide errors, log to file; never leak stack traces or SQL.

### Persistence Model (overview)
- **users**: id, name, email (unique), password_hash, role, created_at.
- **products**: id, name, price (decimal), status/stock, created_at.
- **orders**: id, user_id (FK), total, status (`pending|served|cancelled`), created_at.
- **order_items**: id, order_id (FK), product_id (FK), qty, unit_price.
- **reservations**: id, user_id (FK), seat_id/label, reserved_at, status.
- **contacts**: id, user_id (nullable), subject, message, created_at.
- **discounts** (optional): id, type (`flat|percent`), value, active_from/to.

> Full column types and constraints will be detailed in **Database Schema**.

### Error Handling & Redirects
- On validation fail: set `$_SESSION['flash_error']` → redirect back to originating page.
- On success: set `$_SESSION['flash_success']` → redirect to canonical view (e.g., cart/order list).
- Use consistent **PRG (Post/Redirect/Get)** to avoid double submissions.

### Performance & UX
- Queries are simple and indexed; ensure indexes on `users.email`, `orders.user_id`, `order_items.order_id`, `reservations.user_id`.
- Keep pages small and server-rendered; optional JS enhancements only where needed.
- Pagination can be added for large lists (orders, contacts).

### Extensibility
- Payment gateway can be integrated at checkout with minimal changes to the order pipeline.
- Switch to **PDO** or a lightweight DAO later without breaking page contracts.
- Move common layout/header/footer into `require`-able partials to reduce duplication.
- Optional CSRF tokens can be added to forms for stronger protection.


## Folder & File Map

**Important app files:**

* `index.php`
* `login.php`, `signup.php`, `logout.php`
* `menu.php`, `cart.php`, `order_list.php`
* `seat_reservation.php`, `seats_to_reserve.php`
* `admin_dashboard.php`, `add_products.php`, `give_discount.php`
* `transaction_history.php`, `contact.php`, `contact_list.php`, `about.php`
* `config.php`


### `index.php` — Landing / Entry

**Purpose**
- Public entry point for guests and logged-in users.
- Presents primary navigation to **Menu**, **Reservation**, **Cart**, **About**, **Contact**, and **Login/Logout**.
- Reflects authentication state in the navbar (guest vs customer vs admin).

**Primary Responsibilities**
- Render hero/intro content with a clear call-to-action (e.g., **Explore Menu**).
- Show conditional nav items based on `$_SESSION['role']`.
- Avoid mutations; this page is read-only (no DB writes).

**Inputs**
- **GET:** none required.
- **POST:** not used here.
- **Session (read-only):**
  - `$_SESSION['user_id']` — numeric user id when authenticated.
  - `$_SESSION['role']` — `'customer' | 'admin'`.
  - Optional flash messages: `$_SESSION['flash_success']`, `$_SESSION['flash_error']`.

**Outputs**
- HTML layout with navbar, hero text, and quick links.
- Conditional links: **Admin** appears only for `role === 'admin'`.
- Optional flash banners if present in session (then cleared).

**Dependencies**
- `config.php` — timezone, DB constants, environment flags.
- Session must be started before reading `$_SESSION`.

**Minimal control flow**
1. `require 'config.php'`.
2. `session_start()`.
3. Read `$_SESSION['user_id']` and `$_SESSION['role']` (if any).
4. Render navbar:
   - Guest → show **Login**.
   - Authenticated → show **Logout** (+ **Admin** link for admins).
5. Render hero section with CTA to `menu.php`.
6. If flash messages exist, show once and clear.

**Example skeleton**
```php
<?php
require_once __DIR__ . '/config.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;
$role   = $_SESSION['role']    ?? null;

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error']   ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
```
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Coffee Shop — Welcome</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<nav>
  <a href="index.php">Home</a>
  <a href="menu.php">Menu</a>
  <a href="seat_reservation.php">Reservation</a>
  <a href="cart.php">Cart</a>
  <a href="about.php">About Us</a>
  <a href="contact.php">Contact Us</a>
  <?php if ($userId): ?>
    <?php if ($role === 'admin'): ?><a href="admin_dashboard.php">Admin</a><?php endif; ?>
    <a href="logout.php">Logout</a>
  <?php else: ?>
    <a href="login.php">Login</a>
  <?php endif; ?>
</nav>
```
<?php if ($flashSuccess): ?>
  <div class="alert success"><?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if ($flashError): ?>
  <div class="alert error"><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<main>
  <h1>Welcome to Coffee Shop</h1>
  <p>Freshly brewed beverages and snacks — order online or reserve a seat.</p>
  <p><a href="menu.php">Explore Menu →</a></p>
</main>

</body>
</html>
```

**Security & robustness notes**

* Escape any dynamic text with `htmlspecialchars(...)` before output.
* Do not assume non-null session keys; default to guest when missing.
* Do not auto-redirect authenticated users away from the landing page (let them choose).
* Keep this page free from sensitive queries; DB connection is not required here.

**Edge cases**

* Session present but user record deleted → treat as guest; links remain safe (downstream pages enforce auth).
* Empty database (no products) → `menu.php` must handle empty state gracefully.
* Flash message set on previous page → show once here, then clear.

**Testing checklist**

* Guest sees **Login**; no **Admin** link; all public links resolve (200).
* Customer sees **Logout**, **Cart**, **Reservation**; still no **Admin** link.
* Admin sees **Admin** link; click-through reaches `admin_dashboard.php` and enforces role check.
* Flash banners render when set, then disappear on refresh (PRG-friendly).
* Page loads without PHP warnings in dev (`display_errors=1`) and without leaking details in prod.

**Related pages**

* `menu.php` — primary CTA target.
* `login.php` / `signup.php` — auth entry.
* `admin_dashboard.php` — admin-only.

## Setup — Windows (XAMPP)

### Prerequisites
- **Windows 10/11**
- **XAMPP 8.2+** with at least: Apache, MySQL, phpMyAdmin
- Optional: Git for Windows (to clone the repo)

### 1) Install XAMPP & start services
1. Download XAMPP from apachefriends.org and install to `C:\xampp\`.
2. Open **XAMPP Control Panel** → click **Start** for **Apache** and **MySQL**.
3. Verify:
   - Apache running on **http://localhost/**
   - MySQL running on port **3306** (default)

> If Apache won’t start (port 80/443 in use), stop IIS/Skype/VMware listeners or change Apache ports via **Config → Service and Port Settings**.  
> If MySQL fails, ensure no other MySQL service (“MySQL80”) is already bound to 3306.

### 2) Place the project under `htdocs`
- Path should be: `C:\xampp\htdocs\coffeeshop\`
- If using Git:
  ```bash
  cd C:\xampp\htdocs
  git clone <your-repo-url> coffeeshop
````

### 3) Create the database (phpMyAdmin or CLI)

Open **http://localhost/phpmyadmin** and run:

```sql
CREATE DATABASE coffeeshop
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

Optionally create a dedicated dev user (recommended):

```sql
CREATE USER 'coffee_user'@'localhost' IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON coffeeshop.* TO 'coffee_user'@'localhost';
FLUSH PRIVILEGES;
```

(For quick local dev you can also use the default `root` user with empty password.)

### 4) Configure `config.php`

Edit `C:\xampp\htdocs\coffeeshop\config.php`:

```php
<?php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'coffee_user');   // or 'root'
define('DB_PASS', 'secret');        // or '' for default root (not for production)
define('DB_NAME', 'coffeeshop');

define('APP_ENV', 'local');         // local | production
date_default_timezone_set('Asia/Dhaka');
```

> All DB connections should set charset to `utf8mb4` to support full Unicode.

### 5) (Optional) Import schema/data

If you already have a schema dump (e.g., `docs/schema.sql`), import via phpMyAdmin → **Import**.
If not, you can proceed; the README’s **Database Schema** section will define tables step-by-step.

### 6) Verify pages load

* Home: **[http://localhost/coffeeshop/index.php](http://localhost/coffeeshop/index.php)**
* Menu: **[http://localhost/coffeeshop/menu.php](http://localhost/coffeeshop/menu.php)**
* Login: **[http://localhost/coffeeshop/login.php](http://localhost/coffeeshop/login.php)**
* Admin dashboard (will require an admin user later):
  **[http://localhost/coffeeshop/admin_dashboard.php](http://localhost/coffeeshop/admin_dashboard.php)**

You should see the navbar and basic pages render; some features will be limited until tables exist.

### 7) Development error mode (local only)

In `config.php` (when `APP_ENV === 'local'`) enable verbose errors:

```php
error_reporting(E_ALL);
ini_set('display_errors', '1');
```

For production, disable display and log errors instead:

```php
error_reporting(E_ALL);
ini_set('display_errors', '0');
```

### 8) Common pitfalls & fixes

* **Blank page / 500 error:** check `htdocs\coffeeshop\config.php` path and PHP syntax; enable dev errors.
* **“Access denied for user” (1045):** confirm DB_USER/DB_PASS; if using `root`, try empty password; otherwise (recommended) grant privileges to `coffee_user`.
* **“Unknown database ‘coffeeshop’”:** database wasn’t created—rerun the `CREATE DATABASE` step.
* **Garbled characters/emoji:** ensure connection uses `utf8mb4` and tables are `utf8mb4_unicode_ci`.
* **Session doesn’t persist:** confirm cookies enabled; verify `session.save_path` is writable (XAMPP default is fine).

### 9) Optional: pretty local URL (VirtualHost)

If you want `http://coffeeshop.local/`:

1. Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf` and add:

   ```
   <VirtualHost *:80>
     ServerName coffeeshop.local
     DocumentRoot "C:/xampp/htdocs/coffeeshop"
     <Directory "C:/xampp/htdocs/coffeeshop">
       Require all granted
       AllowOverride All
     </Directory>
   </VirtualHost>
   ```
2. Edit hosts file as Administrator: `C:\Windows\System32\drivers\etc\hosts` → add:

   ```
   127.0.0.1  coffeeshop.local
   ```
3. Restart Apache; visit **[http://coffeeshop.local/](http://coffeeshop.local/)**.



## Setup — Linux (LAMP)
Add this under **“Setup — Linux (LAMP)”** (replace the TODO there). It’s a single, text-heavy commit.

````md
## Setup — Linux (LAMP)

### Prerequisites
- **Ubuntu 22.04/24.04** or **Debian 12** (others OK with equivalent package names)
- sudo access
- Optional: Git

### 1) Install Apache, PHP, MySQL
```bash
sudo apt update
sudo apt install -y apache2 mysql-server \
  php php-cli php-mysql php-mysqli php-xml php-mbstring php-intl php-zip php-curl
````

Verify:

```bash
apache2 -v
php -v
mysql --version
```

### 2) Start & enable services

```bash
sudo systemctl enable --now apache2
sudo systemctl enable --now mysql
sudo systemctl status apache2 mysql
```

### 3) Secure MySQL (recommended)

```bash
sudo mysql_secure_installation
```

Accept strong password policy, remove anonymous users, disallow remote root, remove test DB.

### 4) Create database and user

```bash
sudo mysql
```

Then in the MySQL shell:

```sql
CREATE DATABASE coffeeshop
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE USER 'coffee_user'@'localhost' IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON coffeeshop.* TO 'coffee_user'@'localhost';
FLUSH PRIVILEGES;
```

### 5) Place the project

**Option A — VirtualHost (recommended):**

```bash
sudo mkdir -p /var/www/coffeeshop
sudo chown -R $USER:www-data /var/www/coffeeshop
# If using git:
git clone <your-repo-url> /var/www/coffeeshop
```

**Option B — Simple path (no vhost):**

```bash
sudo mkdir -p /var/www/html/coffeeshop
sudo chown -R $USER:www-data /var/www/html/coffeeshop
git clone <your-repo-url> /var/www/html/coffeeshop
```

Recommended permissions (safe defaults):

```bash
find /var/www/coffeeshop -type d -exec chmod 755 {} \;
find /var/www/coffeeshop -type f -exec chmod 644 {} \;
```

### 6) Configure Apache (VirtualHost)

Create `/etc/apache2/sites-available/coffeeshop.conf`:

```
<VirtualHost *:80>
    ServerName coffeeshop.local
    DocumentRoot /var/www/coffeeshop

    <Directory /var/www/coffeeshop>
        Require all granted
        AllowOverride All
        Options -Indexes
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/coffeeshop_error.log
    CustomLog ${APACHE_LOG_DIR}/coffeeshop_access.log combined
</VirtualHost>
```

Enable site and modules:

```bash
sudo a2ensite coffeeshop
sudo a2enmod rewrite
# (optional) sudo a2dissite 000-default
echo "127.0.0.1  coffeeshop.local" | sudo tee -a /etc/hosts
sudo systemctl reload apache2
```

### 7) Configure `config.php`

Edit `/var/www/coffeeshop/config.php`:

```php
<?php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'coffee_user');   // or 'root'
define('DB_PASS', 'secret');        // or '' for local root (not for production)
define('DB_NAME', 'coffeeshop');

define('APP_ENV', 'local');         // local | production
date_default_timezone_set('Asia/Dhaka');
```

All DB connections should use `utf8mb4`.

### 8) PHP settings (dev vs prod)

Find your `php.ini`:

```bash
php --ini
```

Local/dev:

```ini
error_reporting = E_ALL
display_errors = On
```

Production:

```ini
display_errors = Off
log_errors = On
```

Optional tweaks (if needed):

```ini
memory_limit = 256M
post_max_size = 16M
upload_max_filesize = 16M
```

### 9) Verify pages load

* With vhost: `http://coffeeshop.local/`
* Without vhost (Option B): `http://localhost/coffeeshop/`

Check:

* `index.php` renders navbar and hero
* `menu.php`, `login.php` respond 200
* Admin page exists at `/admin_dashboard.php` (will enforce auth later)

### 10) Firewall (if enabled)

```bash
sudo ufw allow 'Apache Full'
sudo ufw status
```

### 11) Common pitfalls & fixes

* **403 Forbidden**: wrong `DocumentRoot` or directory permissions; ensure `www-data` can read.
* **Downloading PHP instead of executing**: missing PHP module—ensure `libapache2-mod-php` is part of `php` meta-package on your distro; restart Apache.
* **“Access denied for user 'coffee_user'@'localhost'”**: recheck credentials and privileges; confirm `php-mysql` is installed.
* **Emoji/Unicode shows as `???`**: ensure DB/table/connection all use `utf8mb4` + `utf8mb4_unicode_ci`.
* **Session not persisting**: verify cookies enabled; check `session.save_path` writable by web server.
* **404 after enabling vhost**: confirm `ServerName` in `/etc/hosts` and correct `DocumentRoot`; run `sudo apachectl -t` to validate config.

### 12) Quick no-vhost alternative (dev only)

If you want zero Apache config, place the project under `/var/www/html/coffeeshop` and browse:
`http://localhost/coffeeshop/`. You can add a proper VirtualHost later.



## Environment & Secrets

<!-- TODO: Document `config.php` keys (DB_HOST, DB_USER, DB_PASS, DB_NAME); how to store safely in dev vs prod. 10–20 lines. -->

## Database Schema

<!-- TODO: List tables, columns, types, constraints. Prefer a table-per-commit approach. 1 table (10–30 lines) each commit to grow insertions meaningfully. -->

## ER Diagram (text description)

<!-- TODO: Describe relationships in plain text: users(1)–(n)orders, orders(1)–(n)order_items, etc. 15–30 lines. -->

## Sample Data

<!-- TODO: Provide INSERT examples for users, products, seats, orders. 20–50 lines (SQL fenced block). -->

## User Roles & Permissions

<!-- TODO: Customer vs Admin capabilities; who can access which page. 15–25 lines. -->

## Authentication Flow

<!-- TODO: Cookie vs session; session keys; remember-me (if any); logout invalidation. 15–30 lines. -->

## Business Flows

<!-- TODO: Write out flows: "Browse → add to cart → checkout"; "Reserve seats → confirm"; with bullet steps. 20–40 lines. -->

## Endpoints & Routes

<!-- TODO: For each public-facing page/script, list query params, form fields, and expected responses. 5–15 lines per endpoint. -->

## Pages & What They Do

<!-- TODO: Expand each of the files listed earlier with deeper details, edge cases, and links to flows. 10–30 lines per page. -->

## Seat Reservation Module

<!-- TODO: Explain capacity rules, seat selection, conflicts, hold time, DB design. 20–40 lines. -->

## Cart & Ordering

<!-- TODO: Cart structure (session vs DB), price calc, discounts, tax, transaction history. 20–40 lines. -->

## Admin Dashboard

<!-- TODO: KPIs visible, CRUD for products, discount management, user moderation. 15–30 lines. -->

## Discount Rules

<!-- TODO: Explain `give_discount.php`: percent vs flat; eligibility; stacking rules; examples. 15–30 lines. -->

## Validation Rules

<!-- TODO: Server-side validation for forms; error messages; anti-CSRF notes (if any). 15–25 lines. -->

## Security Notes

<!-- TODO: SQL injection prevention, prepared statements; XSS sanitization; session fixation; password hashing. 20–40 lines. -->

## Testing Guide

<!-- TODO: Manual test checklist; sample POST bodies; happy-path + edge cases. 20–40 lines. -->

## Release Notes / Changelog

<!-- TODO: Keep a running list: date, version, highlights. Add an entry with every docs commit. 1–5 lines per release. -->

## FAQ

<!-- TODO: 10–20 Q&As (short). Add 2–3 per commit. -->

## Troubleshooting

<!-- TODO: Common errors (DB conn, 500s, sessions), fixes, logs location. 15–30 lines. -->

## Roadmap

<!-- TODO: Next features: payments, real-time seat map, responsive UI, unit tests. 10–20 lines. -->

## Contributing

<!-- TODO: Branching model, commit message style, code review rules. 10–20 lines. -->

## License

<!-- TODO: Choose and paste license notice. -->

## Acknowledgments

<!-- TODO: Credits, assets, libraries. 5–15 lines. -->
