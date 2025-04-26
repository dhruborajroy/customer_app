-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2025 at 08:01 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `customer_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `note`, `created_at`) VALUES
(1, 'Dhrubo', '01705927257', NULL, '2025-04-16 11:28:59'),
(2, 'Dollar', '01755323116', NULL, '2025-04-16 12:07:23'),
(3, 'Nazmul', '01882874194', NULL, '2025-04-16 12:53:49'),
(4, 'Rocky', '01756123996', NULL, '2025-04-16 13:02:15'),
(5, 'Dhrubo', '01705927257', NULL, '2025-04-20 05:38:19'),
(6, 'Dhrubo', '01705927257', NULL, '2025-04-20 05:39:10'),
(7, 'Dhrubo', '01705927257', NULL, '2025-04-20 05:40:22');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `note` text DEFAULT NULL,
  `expense_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `title`, `amount`, `note`, `expense_date`) VALUES
(1, 'Ink', 720.00, 'Ink Refill for L130- 360 tk\r\nBrother Toner Powder - 350\r\n', '2025-04-21 16:40:41'),
(2, 'Paper', 350.00, 'Basundhara 65 GSM\r\n\r\n', '2025-04-21 16:43:33'),
(3, 'Paper', 380.00, 'Bashundhara 70 GSM', '2025-04-21 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `type` enum('debit','credit') DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `customer_id`, `type`, `amount`, `description`, `date`) VALUES
(1, 1, 'credit', 20.00, '', '2025-04-20 09:31:24'),
(2, 1, 'debit', 50.00, '', '2025-04-20 09:31:24'),
(3, 2, 'credit', 40.00, '', '2025-04-20 10:56:16'),
(4, 2, 'debit', 50.00, '', '2025-04-20 10:56:16'),
(5, 4, 'credit', 20.00, '', '2025-04-20 11:07:32'),
(6, 4, 'debit', 50.00, '', '2025-04-20 11:07:32'),
(7, 4, 'credit', 10.00, '', '2025-04-20 11:13:39'),
(8, 4, 'debit', 20.00, '', '2025-04-20 11:13:39'),
(9, 4, 'credit', 10.00, '', '2025-04-20 11:13:56'),
(10, 4, 'credit', 10.00, '', '2025-04-20 11:14:15'),
(11, 4, 'credit', 10.00, '', '2025-04-20 11:14:27'),
(12, 4, 'credit', 10.00, '', '2025-04-20 11:14:30'),
(13, 4, 'debit', 20.00, '', '2025-04-20 11:14:30'),
(14, 4, 'credit', 10.00, '', '2025-04-20 11:15:40'),
(15, 4, 'debit', 20.00, '', '2025-04-20 11:15:40'),
(16, 4, 'credit', 10.00, '', '2025-04-20 11:15:54'),
(17, 4, 'credit', 20.00, '', '2025-04-20 11:16:59'),
(18, 4, 'debit', 20.00, '', '2025-04-20 11:16:59'),
(19, 4, 'credit', 20.00, '', '2025-04-20 11:23:56'),
(20, 4, 'debit', 25.00, '', '2025-04-20 11:23:56'),
(21, 4, 'credit', 20.00, '', '2025-04-20 11:25:38'),
(22, 4, 'debit', 30.00, '', '2025-04-20 11:25:38'),
(23, 4, 'credit', 1000.00, '', '2025-04-20 11:25:55'),
(24, 7, 'credit', 62.50, '', '2025-04-20 23:15:50'),
(25, 7, 'credit', 62.50, '', '2025-04-20 23:20:36'),
(26, 5, 'credit', 62.50, '', '2025-04-20 23:20:56'),
(27, 5, 'credit', 62.50, '', '2025-04-20 23:23:42'),
(28, 5, 'credit', 62.50, '', '2025-04-20 23:31:14'),
(29, 5, 'credit', 42.50, '', '2025-04-20 23:33:51'),
(30, 5, 'debit', 50.00, '', '2025-04-20 23:33:51'),
(31, 5, 'credit', 62.50, 'Printing Credit', '2025-04-20 23:47:34'),
(32, 5, 'debit', 62.50, 'Payment Received', '2025-04-20 23:47:34'),
(33, 5, 'credit', 62.50, 'Printing Credit', '2025-04-20 23:48:22'),
(34, 5, 'debit', 100.00, 'Payment Received', '2025-04-20 23:48:22'),
(35, 3, 'credit', 40.00, 'Printing Credit', '2025-04-20 23:54:02'),
(36, 3, 'debit', 40.00, 'Payment Received', '2025-04-20 23:54:02'),
(37, 7, 'credit', 9.00, 'bw_single-1:bw_double-1:color_pages-1', '2025-04-24 13:22:19'),
(38, 7, 'credit', 2.00, 'bw_single-1@1:bw_double-1@1:color_pages-1@1', '2025-04-24 13:28:35'),
(39, 7, 'credit', 9.00, 'bw_single-1@0:bw_double-1@0:color_pages-1@0', '2025-04-24 13:29:11'),
(40, 7, 'credit', 2.00, 'bw_single-1@1:bw_double-1@1:color_pages-1@1', '2025-04-24 13:29:37'),
(41, 7, 'credit', 10.00, 'bw_single-1@2:pdf_bw_single-@0:bw_double-1@3:color_pages-1@5', '2025-04-24 14:26:06'),
(42, 5, 'credit', 1.00, 'bw_single-1@2:pdf_bw_single-@0:bw_double-1@3:color_pages-1@5', '2025-04-24 14:27:11'),
(43, 5, 'credit', 12.75, 'bw_single-1@2.5:pdf_bw_single-@0:bw_double-1@3.5:color_pages-1@5', '2025-04-24 14:28:27'),
(44, 5, 'credit', 12.50, 'bw_single-1@2.5:pdf_bw_single-@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-24 14:30:00'),
(45, 5, 'credit', 1.00, 'bw_single-1@2.5:pdf_bw_single-@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-24 14:30:13'),
(46, 5, 'credit', 1.00, 'bw_single-1@2.5:pdf_bw_double-1@:bw_double-1@3.5:color_pages-1@5', '2025-04-24 14:32:33'),
(47, 5, 'credit', 12.75, 'bw_single-1@2.5:pdf_bw_double-1@:bw_double-1@3.5:color_pages-1@5', '2025-04-24 14:35:22'),
(48, 5, 'credit', 12.75, 'bw_single-1@2.5:pdf_bw_double-1@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-24 14:36:55'),
(49, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-1@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:12:44'),
(50, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-1@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:14:25'),
(51, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-1@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:15:21'),
(52, 7, 'credit', 12.50, 'bw_single-1@2.5:pdf_bw_double-1@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:21:03'),
(53, 7, 'credit', 12.00, 'bw_single-1@2.5:pdf_bw_double-1@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:22:55'),
(54, 7, 'credit', 12.00, 'bw_single-1@2.5:pdf_bw_double-1@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:23:20'),
(55, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-0@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:29:56'),
(56, 7, 'credit', 12.00, 'bw_single-1@2.5:pdf_bw_double-1@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:31:43'),
(57, 7, 'credit', 12.00, 'bw_single-1@2.5:pdf_bw_double-1@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:32:26'),
(58, 7, 'credit', 12.00, 'bw_single-1@2.5:pdf_bw_double-1@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:34:48'),
(59, 7, 'credit', 12.00, 'bw_single-1@2.5:pdf_bw_double-1@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:35:20'),
(60, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-0@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:37:16'),
(61, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-0@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:37:58'),
(62, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-0@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:38:15'),
(63, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-0@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:39:12'),
(64, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-0@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:39:27'),
(65, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-0@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:39:40'),
(66, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-0@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:40:01'),
(67, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-0@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:40:41'),
(68, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-0@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:41:16'),
(69, 7, 'credit', 10.00, 'bw_single-1@2.5:pdf_bw_double-0@1.75:bw_double-1@3.5:color_pages-1@5', '2025-04-26 23:41:47'),
(70, 5, 'credit', 150.00, 'bw_single-0@2.5:pdf_bw_double-0@1.75:bw_double-100@1.75:color_pages-0@5', '2025-04-26 23:42:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
