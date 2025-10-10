# CoffeeShop PHP — README

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

<!-- TODO: 15–30 lines. What problem this CoffeeShop app solves; quick summary of features; who it’s for. -->

## Live Demo / Screens

<!-- TODO: Add links or describe screenshots; list 5–10 screens and what to look for. -->

## Tech Stack

<!-- TODO: PHP, MySQL, HTML/CSS/JS, Bootstrap/Tailwind?, XAMPP; mention PHP version. 10–20 lines. -->

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
