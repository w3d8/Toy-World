-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2025 at 10:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toyworld_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category`, `stock`, `created_at`, `updated_at`) VALUES
(1, 'LEGO City Police Station', 'Build and play with this amazing police station set', 59.99, 'images/toys/lego-police.jpg', 'Building Toys', 50, '2025-04-22 08:12:52', '2025-04-22 08:12:52'),
(2, 'Barbie Dreamhouse', 'Three-story dollhouse with furniture and accessories', 179.99, 'images/toys/barbie-house.jpg', 'Dolls', 25, '2025-04-22 08:12:52', '2025-04-22 08:12:52'),
(3, 'Hot Wheels 20-Car Pack', 'Collection of die-cast cars with various designs', 19.99, 'images/toys/hotwheels-pack.jpg', 'Vehicles', 100, '2025-04-22 08:12:52', '2025-04-22 08:12:52'),
(4, 'Nerf Ultra One Blaster', 'High-performance dart blaster with drum magazine', 49.99, 'images/toys/nerf-blaster.jpg', 'Action Toys', 75, '2025-04-22 08:12:52', '2025-04-22 08:12:52'),
(5, 'Play-Doh Super Color Pack', '20 different colors of modeling compound', 24.99, 'images/toys/playdoh-pack.jpg', 'Creative Toys', 150, '2025-04-22 08:12:52', '2025-04-22 08:12:52'),
(6, 'Monopoly Board Game', 'Classic property trading board game', 29.99, 'images/toys/monopoly.jpg', 'Board Games', 60, '2025-04-22 08:12:52', '2025-04-22 08:12:52'),
(7, 'RC Monster Truck', 'Remote-controlled off-road vehicle', 89.99, 'images/toys/rc-truck.jpg', 'Vehicles', 40, '2025-04-22 08:12:52', '2025-04-22 08:12:52'),
(8, 'Science Lab Kit', 'Educational experiments for young scientists', 39.99, 'images/toys/science-kit.jpg', 'Educational', 45, '2025-04-22 08:12:52', '2025-04-22 08:12:52'),
(9, 'Baby Doll Set', 'Soft baby doll with accessories', 34.99, 'images/toys/baby-doll.jpg', 'Dolls', 55, '2025-04-22 08:12:52', '2025-04-22 08:12:52'),
(10, 'Building Blocks Set', '100 colorful blocks for creative building', 29.99, 'images/toys/blocks.jpg', 'Building Toys', 80, '2025-04-22 08:12:52', '2025-04-22 08:12:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `phone`, `created_at`) VALUES
(1, 'youssef', 'y@y.com', '$2y$10$GS2NT4Iiwi7g5cL6QRRnaucXfQWeq7XRRgMGzb4BuDClfaTARhugW', NULL, '2025-04-22 09:27:06'),
(2, '2i3dady', 'yy@y.com', '$2y$10$cYrZN1HHkMbVF7szKRwTtOOFdqoakBjo97FzXC2SO7El4qExoObqa', NULL, '2025-04-22 10:01:26'),
(3, '1sanawy', 'yyy@y.com', '$2y$10$pGBRlz4k6B5ZburtPPrkz.xsw.WTdrVI4sMvWAAZfaUxw/XGt0hue', NULL, '2025-04-22 10:02:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
