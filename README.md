# CoffeeShop Project

## Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Setup](#setup)
- [Diagrams](#diagrams)
- [License](#license)

## Overview

CoffeeShop is a PHP/MySQL web app that models a small café’s online experience. Customers can browse the menu, maintain a cart, reserve seats, and place orders. Administrators manage catalog data, discounts, reservations, and transaction history from a dedicated dashboard.

## Key Features

- User registration, login, and role-based access control.
- Menu browsing with cart management and checkout flow.
- Seat reservation module that prevents double booking.
- Admin dashboard for products, discounts, orders, and transactions.
- Contact form and basic content pages for customer outreach.

## Tech Stack

- **Backend:** PHP 8+, MySQL/MariaDB (mysqli)
- **Frontend:** HTML5, CSS (Bootstrap 5), vanilla JavaScript
- **Server:** Apache (XAMPP/LAMP)
- **Tools:** PlantUML diagrams, PHPUnit (sample), Git

## Project Structure

```*
├── add_products.php        # Admin product management
├── cart.php                # Customer cart & checkout
├── config.php              # DB connection & session bootstrap
├── diagrams/               # PlantUML sources for architecture visuals
├── menu.php                # Menu listing with add-to-cart actions
├── seat_reservation.php    # Seat reservation workflow
├── src/SeatReservation.php # Reusable reservation helper class
├── transaction_history.php # Admin transaction log
└── ...                     # Additional pages (auth, dashboard, etc.)
```

## Setup

1. Clone the repository into your web server root (e.g., `htdocs` or `/var/www/html`).
2. Import `mycoffeshop.sql` into MySQL (`coffeeshop` database by default).
3. Update credentials in `config.php` if necessary.
4. Serve through Apache (XAMPP/LAMP) and visit `http://localhost/CoffeeShop_Project1`.
5. Log in with an existing admin user or register a new customer account.

- Run `php -l <file>` to lint PHP scripts.

## Diagrams

PlantUML source files are in the `diagrams/` directory.

## License

Released under the MIT License. See `LICENSE` for details.
