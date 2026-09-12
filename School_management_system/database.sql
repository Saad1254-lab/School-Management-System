-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 12, 2026 at 08:59 PM
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
-- Database: `bkacademy`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_records`
--

CREATE TABLE `attendance_records` (
  `id` int(11) NOT NULL,
  `teacher_id` varchar(50) NOT NULL,
  `check_in_time` datetime DEFAULT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_records`
--

INSERT INTO `attendance_records` (`id`, `teacher_id`, `check_in_time`, `check_out_time`, `date`) VALUES
(15, '10107', '2025-01-17 08:36:24', '2025-01-17 18:55:43', '2025-01-17'),
(16, '10143', '2025-01-17 08:39:19', '2025-01-17 20:14:21', '2025-01-17'),
(17, '10144', '2025-01-17 08:41:03', NULL, '2025-01-17'),
(18, '10094', '2025-01-17 08:43:30', NULL, '2025-01-17'),
(19, '10063', '2025-01-17 09:10:25', NULL, '2025-01-17'),
(20, '10003', '2025-01-17 09:51:33', '2025-01-17 09:53:23', '2025-01-17'),
(21, '10144', '2025-01-18 09:12:46', NULL, '2025-01-18'),
(22, '10107', '2025-01-18 09:12:59', NULL, '2025-01-18'),
(23, '10143', '2025-01-18 12:46:50', NULL, '2025-01-18'),
(24, '10144', '2025-01-20 08:01:44', NULL, '2025-01-20'),
(25, '10144', '2025-01-21 08:06:53', NULL, '2025-01-21'),
(26, '10107', '2025-01-22 22:54:20', '2025-01-22 22:55:31', '2025-01-22'),
(27, '10144', '2025-01-27 08:34:41', NULL, '2025-01-27');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`) VALUES
(3, 'Grade-1'),
(11, 'Grade-10'),
(2, 'Grade-2'),
(4, 'Grade-3'),
(5, 'Grade-4'),
(6, 'Grade-5'),
(7, 'Grade-6'),
(8, 'Grade-7'),
(9, 'Grade-8'),
(10, 'Grade-9');

-- --------------------------------------------------------

--
-- Table structure for table `class_products`
--

CREATE TABLE `class_products` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_products`
--

INSERT INTO `class_products` (`id`, `class_id`, `product_id`, `quantity`) VALUES
(3, 3, 1, 1),
(4, 3, 7, 3),
(5, 3, 6, 1),
(6, 3, 5, 1),
(7, 3, 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `fee_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `payment_status` enum('Paid','Pending') DEFAULT 'Pending',
  `due_month` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `stock`, `price`) VALUES
(1, 'Art Book', 135, 250.00),
(2, 'Urdu Note Book', 13, 250.00),
(3, 'ID Card', 993, 100.00),
(4, 'Left Margin Register', 100, 250.00),
(5, 'Maths Copy', 92, 250.00),
(6, 'Home Work Diary', 92, 250.00),
(7, 'English Copy', 976, 250.00),
(8, 'Geometry Copy', 100, 250.00),
(9, 'English Interleaf Copy', 100, 250.00),
(10, 'Picking Card', 999, 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sale_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `class_id`, `invoice_no`, `total`, `sale_date`) VALUES
(24, NULL, 'INV-20260830-0001', 100.00, '2026-08-30 16:23:31'),
(25, 3, 'INV-20260830-0002', 2500.00, '2026-08-30 16:23:47'),
(26, NULL, 'INV-20260830-0003', 50.00, '2026-08-30 16:24:30');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `total`) VALUES
(55, 24, 3, 1, 100.00, 100.00),
(56, 25, 1, 1, 250.00, 250.00),
(57, 25, 7, 3, 250.00, 750.00),
(58, 25, 6, 1, 250.00, 250.00),
(59, 25, 5, 1, 250.00, 250.00),
(60, 25, 2, 4, 250.00, 1000.00),
(61, 26, 10, 1, 50.00, 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `gr_no` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `father_name` varchar(100) NOT NULL,
  `dob` varchar(20) NOT NULL,
  `date_of_admission` varchar(20) NOT NULL,
  `fees_applied` decimal(10,2) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `father_cnic` varchar(255) DEFAULT NULL,
  `address` text NOT NULL,
  `class` varchar(50) DEFAULT NULL,
  `status` enum('Active','Withdrawn','InActive') DEFAULT 'Active',
  `withdraw_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `status` enum('Pending','Completed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `task`, `status`, `created_at`) VALUES
(73, 'Ground Floor Hall board', 'Completed', '2025-10-30 07:13:12'),
(70, 'Sliding Door oiling', 'Pending', '2025-10-30 07:09:53'),
(71, 'Medam Office Computer Issue', 'Pending', '2025-10-30 07:10:19'),
(72, '1 st Floor Class Bulb', 'Pending', '2025-10-30 07:11:50'),
(66, 'Kitchen Cupboard Repairing', 'Pending', '2025-10-07 05:37:10'),
(65, 'Office Benches Color', 'Pending', '2025-10-04 03:57:17');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `teacher_id` varchar(50) NOT NULL,
  `joining_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `teacher_id`, `joining_date`) VALUES
(7, 'Miss Afsheen Mujeeb', '10003', '2025-01-16'),
(8, 'Miss Hina Shaukat', '10004', '2025-01-16'),
(9, 'Miss Mehjabeen Tabassum', '10011', '2025-01-16'),
(11, 'Miss Nazish Lodhi', '10062', '2025-01-16'),
(12, 'Miss Afeefa Tayyab', '10063', '2025-01-16'),
(13, 'Miss Uzma Sohaib', '10075', '2025-01-16'),
(14, 'Miss Shabana jalal', '10084', '2025-01-16'),
(15, 'Miss Wajiha Sajjad ', '10089', '2025-01-16'),
(16, 'Miss Aqsa', '10091', '2025-01-16'),
(17, 'Mrs Sarwat Sahiba', '10097', '2025-01-16'),
(18, 'Miss Javeria Waheed', '10104', '2025-01-16'),
(19, 'Miss Tooba Mansoor ', '10113', '2025-01-16'),
(20, 'Miss Saima Afzal ', '10114', '2025-01-16'),
(21, 'Miss Ayesha Javed', '10120', '2025-01-16'),
(22, 'Miss Nida Zainab', '10121', '2025-01-16'),
(23, 'Miss Kishwar', '10132', '2025-01-16'),
(24, 'Miss Asma ', '10135', '2025-01-16'),
(25, 'Miss Kanwal Gul', '10137', '2025-01-16'),
(26, 'Miss Sabiha Aftab', '10138', '2025-01-16'),
(27, 'Miss kainat Tahir ', '10145', '2025-01-16'),
(28, 'Miss Sadaf Aman', '10146', '2025-01-16'),
(29, 'Miss Nimra Ejaz', '10150', '2025-01-16'),
(30, 'Miss Rabia Faizan', '10154', '2025-01-16'),
(31, 'Mrs Nasreen Shahid', '10001', '2025-01-16'),
(32, 'Miss Rida Liaquat', '10161', '2025-01-16'),
(33, 'Miss Shizra', '10162', '2025-01-16'),
(34, 'Mrs Asma Atif', '10163', '2025-01-16'),
(35, 'Miss Misbah Fareed', '10164', '2025-01-16'),
(36, 'Miss Shiza', '10165', '2025-01-16'),
(37, 'Miss Sharmeen ', '10166', '2025-01-16'),
(38, 'Miss Rabia Ovais', '10168', '2025-01-16'),
(39, 'Miss Rimsha Ashfaque', '10167', '2025-01-16'),
(40, 'Miss Maryam Shaheen', '10169', '2025-01-16'),
(41, 'Miss Maryam Noor Ullah', '10170', '2025-01-16'),
(42, 'Miss Haya Zafar', '10171', '2025-01-16'),
(43, 'Miss Sehrish Hassan ', '10172', '2025-01-16'),
(44, 'Miss Bisma ', '10178', '2025-01-16'),
(45, 'Miss Yusra Khalil', '10182', '2025-01-16'),
(46, 'Miss Fatima Zayad ', '10183', '2025-01-16'),
(47, 'Miss Nazneen Arif', '10056', '2025-01-16'),
(48, 'Abdul Rasheed', '10014', '2025-01-16'),
(49, 'Mr Saeed Ahmed', '10094', '2025-01-16'),
(50, 'Saad Jafri ', '10107', '2025-01-16'),
(51, 'Abdul Jabbar', '10143', '2025-01-16'),
(52, 'Muhammad Hassan Khan', '10144', '2025-01-16'),
(53, 'Sir Shahid Siddiqui', '10160', '2025-01-16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `class_products`
--
ALTER TABLE `class_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `class_product` (`class_id`,`product_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`fee_id`),
  ADD UNIQUE KEY `unique_student_month` (`student_id`,`due_month`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_invoice_no` (`invoice_no`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sale_id` (`sale_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_id` (`teacher_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_records`
--
ALTER TABLE `attendance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `class_products`
--
ALTER TABLE `class_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `fee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD CONSTRAINT `attendance_records_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`);

--
-- Constraints for table `class_products`
--
ALTER TABLE `class_products`
  ADD CONSTRAINT `fk_cp_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cp_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_sales_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `fk_sale_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sale_items_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
