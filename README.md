# CoffeeShop— README

> This README is designed to be grown **gradually** in many small, meaningful commits (docs-only). Each subsection below is a bite‑sized chunk you can complete and commit separately.

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

<!-- TODO: Bullet list (10–30 items): menu browsing, cart, checkout, seat reservation, admin approvals, discounts, etc. -->

## Architecture

<!-- TODO: Sketch the high-level architecture in prose: client → PHP controllers → DB. Where sessions/cookies fit. 15–25 lines. -->

## Folder & File Map

**Important app files:**

* `index.php`
* `login.php`, `signup.php`, `logout.php`
* `menu.php`, `cart.php`, `order_list.php`
* `seat_reservation.php`, `seats_to_reserve.php`
* `admin_dashboard.php`, `add_products.php`, `give_discount.php`
* `transaction_history.php`, `contact.php`, `contact_list.php`, `about.php`
* `config.php`

<!-- TODO: For each file, add 2–5 bullets explaining role, inputs, outputs, session vars used. Commit one file’s notes per commit. -->

## Setup — Windows (XAMPP)

<!-- TODO: Step-by-step: clone → put under htdocs → create DB → import schema → set config.php → run at http://localhost/coffeeshop. 15–30 lines. -->

## Setup — Linux (LAMP)

<!-- TODO: apt install steps; Apache vhost; permissions; php.ini tips. 15–30 lines. -->

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
