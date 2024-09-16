-- phpMyAdmin SQL Dump
-- version 5.2.1deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 18, 2023 at 12:18 AM
-- Server version: 10.11.2-MariaDB-1
-- PHP Version: 8.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e-ticketing`
--

-- --------------------------------------------------------
-- Drop the database if it exists
-- --------------------------------------------------------
DROP DATABASE IF EXISTS `e-ticketing`;

-- --------------------------------------------------------
-- Create the database
-- --------------------------------------------------------
CREATE DATABASE `e-ticketing` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Use the newly created database
USE `e-ticketing`;


--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(50) NOT NULL,
  `date` date DEFAULT NULL,
  `venue` varchar(50) NOT NULL,
  `time` time DEFAULT NULL,
  `ticket_price` int(11) NOT NULL,
  `tickets_capacity` int(11) NOT NULL,
  `banner` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `event_type_name` enum('Entertainment','Conferencing','Movies & Theatre','Sports','Free events') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `event_name`, `date`, `venue`, `time`, `ticket_price`, `tickets_capacity`, `banner`, `created_at`, `updated_at`, `from_date`, `to_date`, `event_type_name`) VALUES
(1, 'Blankets n Wine', '2023-07-29', 'Jamhuri Grounds', '13:29:00', 1200, 100, '../assets/images/banners/64b5b1536537b.jpg', '2023-07-17 21:23:31', '0000-00-00 00:00:00', NULL, NULL, 'Entertainment'),
(4, 'Mental Health', NULL, 'Village Market', '10:00:00', 1000, 195, '../assets/images/banners/64b5c74c30314.png', '2023-07-17 22:57:16', '0000-00-00 00:00:00', '2023-07-21', '2023-07-22', 'Entertainment');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `number_of_tickets` int(5) NOT NULL,
  `total_amount` int(20) NOT NULL,
  `users_email` varchar(50) NOT NULL,
  `events_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `number_of_tickets`, `total_amount`, `users_email`, `events_id`, `created_at`) VALUES
(25, 2, 2000, 'baha.karanja@gmail.com', 4, '2023-07-18 00:15:05');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `redeemed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `reservation_id`, `token`, `redeemed`) VALUES
(25, 25, 'af5c1526871b6a78bac9dd5188786574', 0),
(26, 25, '2393be0775b19ff48645b1e316c22cea', 0);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `transaction_code` varchar(255) NOT NULL,
  `users_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `transaction_code`, `users_id`, `created_at`) VALUES
(14, '5b05bb901353b3a8cfe89aeab13cac4c', 5, '2023-07-18 00:15:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `contact`, `password`, `is_admin`) VALUES
(1, 'admin', 'admin.ticketingapp@gmail.com', '707294699', '$2y$10$/229dX4ZqIfTKe9TO4RTVOe/ISqOJ/jKG7fXeW1Q39osPQ25OEQ/2', 1),
(5, 'john', 'john.doe@test.com', '0712354788', '$2y$10$cuQl.hdCkOsb85362wYwROIEbW3pWu8Z6QV2TACfpvrbuJaSKJoGa', 0),
(7, 'stephh', 'me@stephiiee.dev', '071234578', '$2y$10$va2UYPzxuusccIF7HU8gau6pa340xMHror.U.fmLW5NI/ItAq30zy', 0),
(8, 'andy', 'andy.kimani@gmail.com', '071234578', '$2y$10$ovRI57LqSIWkdbZHbeHyUOnrcMADneyG8A3BZl32mubih5UcRB4f6', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`users_email`,`events_id`),
  ADD KEY `events_id` (`events_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_unique` (`token`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`) USING BTREE,
  ADD KEY `users_id` (`users_id`);

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
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_reservations_users` FOREIGN KEY (`users_email`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`events_id`) REFERENCES `events` (`id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `users_id` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
