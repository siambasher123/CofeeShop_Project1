-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Oct 05, 2025 at 07:10 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;
--
-- Database: `mycoffeeshop`
--

-- --------------------------------------------------------
--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (
    `id`,
    `user_id`,
    `product_id`,
    `quantity`,
    `created_at`
  )
VALUES (7, 4, 1, 1, '2025-10-05 17:10:19');
-- --------------------------------------------------------
--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `message`, `created_at`)
VALUES (
    1,
    'siam basher',
    's@gmail.com',
    'sss',
    '2025-10-05 12:41:46'
  );
-- --------------------------------------------------------
--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10, 2) NOT NULL,
  `status` enum('Pending', 'Completed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `created_at`)
VALUES (1, 4, 1.00, '', '2025-10-05 12:46:25'),
  (3, 3, 1.00, '', '2025-10-05 14:33:03'),
  (4, 3, 1.00, 'Pending', '2025-10-05 17:06:05');
-- --------------------------------------------------------
--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10, 2) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (
    `id`,
    `order_id`,
    `product_id`,
    `quantity`,
    `price`
  )
VALUES (3, 3, 1, 1, 1.00),
  (4, 4, 1, 1, 1.00);
-- --------------------------------------------------------
--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` decimal(10, 2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_price` decimal(10, 2) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `products`
--

INSERT INTO `products` (
    `id`,
    `name`,
    `image`,
    `price`,
    `description`,
    `created_at`,
    `discount_price`
  )
VALUES (
    1,
    'espresso',
    'uploads/images (1).jpg',
    1.00,
    'good for health',
    '2025-10-05 12:16:35',
    0.50
  );
-- --------------------------------------------------------
--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `seat_number` varchar(5) NOT NULL,
  `reservation_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (
    `id`,
    `user_id`,
    `seat_number`,
    `reservation_date`,
    `created_at`
  )
VALUES (
    1,
    3,
    'A3,A4',
    '2025-10-05',
    '2025-10-05 13:47:52'
  ),
  (2, 3, '', '2025-10-05', '2025-10-05 13:50:46');
-- --------------------------------------------------------
--
-- Table structure for table `seat_reservations`
--

CREATE TABLE `seat_reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `seat_row` int(11) NOT NULL,
  `seat_col` int(11) NOT NULL,
  `reserved_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `seat_reservations`
--

INSERT INTO `seat_reservations` (
    `id`,
    `user_id`,
    `seat_row`,
    `seat_col`,
    `reserved_at`
  )
VALUES (1, 4, 1, 9, '2025-10-05 22:48:01'),
  (2, 4, 1, 10, '2025-10-05 22:48:01'),
  (3, 4, 1, 11, '2025-10-05 22:48:01');
-- --------------------------------------------------------
--
-- Table structure for table `transaction1`
--

CREATE TABLE `transaction1` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10, 2) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `transaction1`
--

INSERT INTO `transaction1` (
    `id`,
    `order_id`,
    `user_id`,
    `total`,
    `details`,
    `created_at`
  )
VALUES (1, 1, 4, 1.00, NULL, '2025-10-05 13:06:25'),
  (2, 1, 4, 1.00, NULL, '2025-10-05 13:11:42'),
  (3, 1, 4, 1.00, NULL, '2025-10-05 13:11:47'),
  (4, 1, 4, 1.00, NULL, '2025-10-05 13:17:15'),
  (5, 1, 4, 1.00, NULL, '2025-10-05 13:17:25'),
  (6, 1, 4, 1.00, NULL, '2025-10-05 13:17:27'),
  (7, 1, 4, 1.00, NULL, '2025-10-05 13:17:37'),
  (8, 1, 4, 1.00, NULL, '2025-10-05 13:17:39'),
  (9, 1, 4, 1.00, NULL, '2025-10-05 13:20:05'),
  (10, 1, 4, 1.00, NULL, '2025-10-05 13:20:18'),
  (11, 1, 4, 1.00, NULL, '2025-10-05 13:22:04'),
  (12, 1, 4, 1.00, NULL, '2025-10-05 13:23:53'),
  (13, 1, 4, 1.00, NULL, '2025-10-05 13:24:44'),
  (14, 3, 3, 1.00, NULL, '2025-10-05 14:34:35');
-- --------------------------------------------------------
--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10, 2) NOT NULL,
  `taken_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'Processed',
  `payment_details` text DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (
    `id`,
    `order_id`,
    `user_id`,
    `total`,
    `taken_at`,
    `status`,
    `payment_details`
  )
VALUES (
    7,
    1,
    4,
    1.00,
    '2025-10-05 12:59:46',
    'Processed',
    'Payment processed'
  ),
  (
    8,
    1,
    4,
    1.00,
    '2025-10-05 12:59:48',
    'Processed',
    'Payment processed'
  ),
  (
    9,
    1,
    4,
    1.00,
    '2025-10-05 12:59:55',
    'Processed',
    'Payment processed'
  ),
  (
    10,
    1,
    4,
    1.00,
    '2025-10-05 13:01:37',
    'Processed',
    'Payment processed'
  ),
  (
    11,
    1,
    4,
    1.00,
    '2025-10-05 13:01:40',
    'Processed',
    'Payment processed'
  );
-- --------------------------------------------------------
--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `role` enum('user', 'admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (
    `id`,
    `first_name`,
    `last_name`,
    `email`,
    `password`,
    `phone`,
    `address`,
    `role`,
    `created_at`
  )
VALUES (
    1,
    'siam',
    'basher',
    's@gmail.com',
    '$2y$10$ZYDNIr5PI1aQ8BU9g9HyjeITAPOjbaCCGwrGMNceCYuGc2D5EraJq',
    '01879296759',
    'khulna',
    'admin',
    '2025-10-05 11:56:59'
  ),
  (
    2,
    'siam',
    'basher',
    'siambasher78@gmail.com',
    '$2y$10$YBdqlN53ohAbhUVxxxxb3.otwvh8DvJCexRpJNZxdXwBnl1cJ00YO',
    '01879296759',
    'khulna',
    'admin',
    '2025-10-05 11:57:26'
  ),
  (
    3,
    'siam',
    'basher',
    's12@gmail.com',
    '$2y$10$sNDh4tm5x6siFNoV7jsJKuEefiAeIWDUVTCXHpOwJ8dd2xWnHjr46',
    '01879296759',
    'khulna',
    'admin',
    '2025-10-05 12:02:58'
  ),
  (
    4,
    'siam',
    'basher',
    's11@gmail.com',
    '$2y$10$3kcsUlSMGeaG9hfBbDaTQue5MgaVKG6xKpvj7.dbQc7id3G5hQywu',
    '01879296759',
    'khulna',
    'user',
    '2025-10-05 12:45:50'
  );
--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);
--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
ADD PRIMARY KEY (`id`);
--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);
--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);
--
-- Indexes for table `products`
--
ALTER TABLE `products`
ADD PRIMARY KEY (`id`);
--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);
--
-- Indexes for table `seat_reservations`
--
ALTER TABLE `seat_reservations`
ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);
--
-- Indexes for table `transaction1`
--
ALTER TABLE `transaction1`
ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);
--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);
--
-- Indexes for table `users`
--
ALTER TABLE `users`
ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);
--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 8;
--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 2;
--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 5;
--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 5;
--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 2;
--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 3;
--
-- AUTO_INCREMENT for table `seat_reservations`
--
ALTER TABLE `seat_reservations`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 4;
--
-- AUTO_INCREMENT for table `transaction1`
--
ALTER TABLE `transaction1`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 15;
--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 12;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
--
-- Constraints for table `seat_reservations`
--
ALTER TABLE `seat_reservations`
ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
--
-- Constraints for table `transaction1`
--
ALTER TABLE `transaction1`
ADD CONSTRAINT `transaction1_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction1_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;