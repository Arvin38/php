-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2024 at 10:09 AM
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
-- Database: `barcode1_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `product_id` varchar(50) DEFAULT NULL,
  `barcode_id` varchar(50) DEFAULT NULL,
  `price` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `product_id`, `barcode_id`, `price`) VALUES
(0, 'Tumbler', 'Tumbler', '213121', '120.50'),
(0, 'Nova', 'NOVA-214415', '121451', '15.00'),
(0, 'Piattos', 'PIA-312131', '213121', '15.00'),
(0, 'C2', 'C2-121312', NULL, '20.00'),
(0, 'Sneakers', 'SNE-23141214', NULL, '70.00'),
(0, 'Toblerone', 'TOB-21415231', NULL, '177.99'),
(0, 'benh', '123456789', NULL, '50'),
(0, 'wireless keyboard', '21587152143', NULL, '250'),
(0, 'bag', '213121', NULL, '20'),
(0, 'kwek', '2131213', NULL, '20'),
(0, 'tempura', '213121312', NULL, '20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD UNIQUE KEY `product_id` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
