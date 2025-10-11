# Project | Coffee Shop | Information System Design Lab

## Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Setup](#setup)
- [Project Artifacts](#project-artifacts)
- [License](#license)

## Overview

This Information Systems Lab project delivers a PHP/MySQL portal that models the daily workflow of a modern café. Customers can browse the menu, maintain a cart, reserve seats, track orders, and request support. Administrators operate a dedicated dashboard to manage catalog data, discounts, reservations, and transaction history while supervising service quality.

## Key Features

- User registration, login, and role-based access control.
- Menu browsing with cart management and checkout flow.
- Seat reservation module that prevents double booking.
- Order placement with payment logging and fulfillment tracking.
- Admin dashboard for products, discounts, orders, reservations, and transactions.
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
3. Update credentials in `config.php` to match your environment.
4. Serve through Apache (XAMPP/LAMP) and visit `http://localhost/CofeeShop_Project1`.
5. Log in with an existing admin user or register a new customer account.

- Run `php -l <file>` to lint PHP scripts.

## Project Artifacts

- System documentation is summarized in `ProjectReport.md`.
- PlantUML models (`diagrams/`) include Level 0 and Level 1 DFDs, use case, sequence, activity, class, and Gantt chart diagrams. Generate images with your preferred PlantUML tool before embedding in reports.

## License

Released under the MIT License. See `LICENSE` for details.
