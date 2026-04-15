-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2026 at 11:09 AM
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
-- Database: `bus-reservation-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'jagdish', '1234@Admin');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `seat_number` varchar(255) NOT NULL,
  `status` enum('available','pending','booked') DEFAULT 'available',
  `payment_status` enum('unpaid','pending','paid') DEFAULT 'unpaid',
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `bus_number` varchar(50) NOT NULL,
  `route` varchar(255) NOT NULL,
  `trip_date` date DEFAULT NULL,
  `passengers` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `boarding_point` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `stripe_session_id` varchar(255) DEFAULT NULL,
  `stripe_payment_intent` varchar(255) DEFAULT NULL,
  `payment_method` enum('stripe','cash') DEFAULT 'stripe'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `bus_id`, `seat_number`, `status`, `payment_status`, `user_id`, `created_at`, `updated_at`, `bus_number`, `route`, `trip_date`, `passengers`, `price`, `boarding_point`, `full_name`, `mobile_number`, `email`, `stripe_session_id`, `stripe_payment_intent`, `payment_method`) VALUES
(3, 3, '1, 6', 'booked', 'paid', NULL, '2026-03-08 06:08:23', '2026-03-08 06:10:28', 'B SA 4536', 'Attariya - kathmandu', '2026-03-08', 2, 5000.00, 'Attariya Buspark (2:00 PM)', 'Jagdish Bhatta', '9821698155', 'bhattajagdish48@gmail.com', 'cs_test_a1EU4IC7uA2zDmiXcCPd1ePLQ1w4xAPlfVTmsOLUeKVxbjGwoDHq69eHHW', 'pi_3T8aXsDEigauR4N81ztSMKtN', 'stripe');

-- --------------------------------------------------------

--
-- Table structure for table `bus`
--

CREATE TABLE `bus` (
  `id` int(11) NOT NULL,
  `Bus_Name` varchar(255) NOT NULL,
  `Tel` varchar(20) NOT NULL,
  `Bus_Number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus`
--

INSERT INTO `bus` (`id`, `Bus_Name`, `Tel`, `Bus_Number`) VALUES
(1, 'Pawan Yatayat', '9821698155', 'B SA 4536'),
(2, 'Pawan Yatayat', '9834568722', 'B SA 8753'),
(3, 'Pawan Yatayat', '9856744456', 'B SA 3242'),
(4, 'Mahakali', '9821698158', 'B SA 9054'),
(5, 'Surya', '9854763429', 'B SA 3472'),
(7, 'Tara', '9834256712', 'B DA 6232');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `name`, `email`, `phone`, `message`) VALUES
(1, 'jagdish', 'bhattajagdish48@gmail.com', '9821698155', 'Super smooth booking process. Took less than a minute 🚍✨'),
(2, 'shiv raj bhatta', 'shivraj@gmail.com', '9744437623', 'Booking seats was easy and stress-free. Great job!'),
(3, 'Gita Devi Bhatta', 'gitabhatta@gmail.com', '9843678219', 'Clean design, fast loading, no confusion. Love it 🔥'),
(4, 'Yogendra', 'yogendra@gmail.com', '9853762541', 'Wow'),
(5, 'Ramesh Bhat', 'ramesh@gmail.com', '9825637456', 'Finally a bus booking site that actually works properly!'),
(6, 'Rahul Shah', 'rahul@gmail.com', '9712324353', 'Wow! what a nice site .');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_otp`
--

CREATE TABLE `password_reset_otp` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expiry_time` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `route`
--

CREATE TABLE `route` (
  `id` int(100) NOT NULL,
  `Source` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `bus_name` varchar(255) NOT NULL,
  `bus_number` varchar(20) NOT NULL,
  `departure_date` date NOT NULL,
  `departure_time` time(6) NOT NULL,
  `total_seats` int(11) NOT NULL,
  `available_seats` int(11) NOT NULL,
  `cost` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route`
--

INSERT INTO `route` (`id`, `Source`, `destination`, `bus_name`, `bus_number`, `departure_date`, `departure_time`, `total_seats`, `available_seats`, `cost`) VALUES
(1, 'Kathmandu', 'Attariya', 'Pawan Yatayat', 'B SA 4536', '2026-03-10', '15:56:00.000000', 40, 40, '2500'),
(3, 'Attariya', 'kathmandu', 'Pawan Yatayat', 'B SA 4536', '2026-03-08', '14:00:00.000000', 40, 38, '2500'),
(4, 'pokhara', 'kathmandu', 'Surya', 'B SA 3472', '2026-01-08', '23:08:00.000000', 40, 40, '1250'),
(5, 'Kathmandu', 'Pokhara', 'Pawan Yatayat', 'B SA 8753', '2026-01-07', '22:47:00.000000', 40, 40, '1250'),
(6, 'Dhangadhi', 'kathmandu', 'Pawan Yatayat', 'B SA 8753', '2026-01-07', '07:13:00.000000', 40, 40, '2700'),
(8, 'Attariya', 'kathmandu', 'Pawan Yatayat', 'B SA 8753', '2026-01-28', '23:00:00.000000', 36, 36, '2600'),
(9, 'Kathmandu', 'Pokhara', 'Pawan Yatayat', 'B SA 3242', '2026-01-08', '16:07:00.000000', 36, 36, '1200'),
(10, 'pokhara', 'Attariya', 'Surya', 'B SA 3472', '2026-01-09', '20:57:00.000000', 40, 40, '2400'),
(11, 'Kathmandu', 'Attariya', 'Mahakali', 'B SA 9054', '2026-01-28', '17:37:00.000000', 36, 36, '2400'),
(12, 'Kathmandu', 'Jhapa', 'Tara', 'B DA 6232', '2026-01-09', '12:40:00.000000', 40, 40, '2200'),
(13, 'Kathmandu', 'Attariya', 'Surya', 'B SA 3472', '2026-01-24', '02:20:00.000000', 40, 40, '2300'),
(14, 'Kathmandu', 'Attariya', 'Mahakali', 'B SA 9054', '2026-01-23', '20:20:00.000000', 40, 40, '2400');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `First_Name` varchar(255) NOT NULL,
  `Last_Name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `First_Name`, `Last_Name`, `username`, `email`, `password`, `profile_image`) VALUES
(1, 'Jagdish', 'Bhatta', 'jagdish bhatta', 'bhattajagdish48@gmail.com', '$2y$10$Tfi2SfY.Je8a9S.Ys8/Ro.oHgSu7u.DvySmcw4YXMM0iZoZz3gWN.', 'uploads/1772949391_1743081319540.jpg'),
(2, 'Gita', 'Bhatta', 'gita', 'gitabhatta@gmail.com', '$2y$10$/.QTr.tT7verLBoYBQGZ6u3qyQqlpUFqczlncJnieKFqJCkbIiWHC', NULL),
(3, 'shiv', 'Bhatta', 'shivrajbhatta', 'bhattajagdish606@gmail.com', '$2y$10$xHbrffxdxJvxvnztcJCPMe9reNiJRaZiHMIgFf2XGmAb6JI3w8jAe', NULL),
(4, 'Jagdish', 'Bhatta', 'jack', 'bhatta56@gmail.com', '$2y$10$4oZAhKhrhwnsUDgKlacpj.OxnYddlsqy0WTVwPd3iGk3T21IM0Nk.', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bus`
--
ALTER TABLE `bus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Bus_Number` (`Bus_Number`),
  ADD UNIQUE KEY `unique_bus_number` (`Bus_Number`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_otp`
--
ALTER TABLE `password_reset_otp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `email` (`email`),
  ADD KEY `expiry_time` (`expiry_time`);

--
-- Indexes for table `route`
--
ALTER TABLE `route`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bus`
--
ALTER TABLE `bus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `password_reset_otp`
--
ALTER TABLE `password_reset_otp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `route`
--
ALTER TABLE `route`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
