-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Jun 17, 2025 at 08:07 PM
-- Server version: 8.0.42
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `buyitt`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(6, 1, 2, 1, '2025-05-25 18:47:58'),
(7, 1, 3, 1, '2025-05-26 08:56:30'),
(11, 5, 4, 1, '2025-06-01 13:53:19'),
(15, 5, 3, 1, '2025-06-16 12:53:30');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(2, 'Furniture', '2025-05-15 13:51:20'),
(3, 'Vehicles', '2025-05-15 13:51:59'),
(4, 'Menswear', '2025-05-15 14:00:34'),
(5, 'Womenswear', '2025-05-15 14:00:53'),
(6, 'Electronics', '2025-05-15 14:01:20'),
(7, 'Properties for rent', '2025-05-15 14:01:44');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `from_user` int NOT NULL,
  `to_user` int NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `product_id`, `from_user`, `to_user`, `message`, `created_at`, `is_read`) VALUES
(6, 2, 1, 5, 'Is this product still available?', '2025-05-25 18:37:09', 1),
(7, 3, 1, 5, 'Is this product still available?', '2025-05-26 08:56:57', 1),
(8, 3, 2, 5, 'Is this product still available?', '2025-05-30 17:37:07', 1),
(9, 3, 2, 5, 'Is this product still available?', '2025-05-30 17:37:35', 1),
(10, 3, 2, 5, 'Hi', '2025-05-30 17:37:35', 1),
(11, 3, 2, 5, 'Is this product still available?', '2025-05-30 17:37:37', 1),
(12, 4, 2, 5, 'Is this product still available?', '2025-05-30 17:38:31', 1),
(13, 3, 2, 5, 'Is this product still available?', '2025-06-01 10:06:16', 1),
(14, 3, 2, 5, 'Is this product still available?', '2025-06-01 10:06:20', 1),
(15, 3, 2, 5, 'hi', '2025-06-01 10:06:20', 1),
(16, 2, 5, 5, 'Is this product still available?', '2025-06-01 14:26:50', 1),
(17, 2, 5, 5, 'Is this product still available?', '2025-06-01 14:26:58', 1),
(18, 2, 5, 5, 'hi', '2025-06-01 14:26:58', 1),
(19, 4, 2, 5, 'Is this product still available?', '2025-06-02 12:52:04', 1),
(20, 4, 2, 5, 'Is this product still available?', '2025-06-02 14:02:18', 1),
(21, 2, 5, 1, 'Hi yes its still available', '2025-06-16 07:04:16', 1),
(22, 4, 5, 2, 'Hi Tlotlo Tau, please let us know your preferred delivery option.', '2025-06-16 16:34:29', 1),
(23, 4, 5, 2, 'Hi Tlotlo Tau, please let us know your preferred delivery option.<br> 1.Meet the seller at their location<br> 2.Collect at a nearby location<br> 3.Delivery to your address', '2025-06-16 16:36:03', 1),
(24, 4, 5, 2, 'Hi Tlotlo Tau, please let us know your preferred delivery option.<br> 1.Meet the seller at their location<br> 2.Collect at a nearby location<br> 3.Delivery to your address', '2025-06-16 16:37:45', 1),
(25, 4, 5, 2, 'Hi Tlotlo Tau, please let us know your preferred delivery option.\r\n                     1.Meet the seller at their location\r\n                     2.Seller come to your location\r\n                     3. Courier delivery to your address', '2025-06-16 16:37:59', 1),
(60, 4, 5, 2, 'Hi Tlotlo Tau, please let us know your preferred delivery option.\r\n                     1.Meet the seller at their location\r\n                     2.Seller come to your location\r\n                     3. Courier delivery to your address', '2025-06-16 17:55:06', 1),
(62, 4, 5, 2, 'Hi Tlotlo Tau, please let us know your preferred delivery option.\r\n                     1.Meet the seller at their location\r\n                     2.Seller come to your location\r\n                     3. Courier delivery to your address', '2025-06-16 17:55:37', 1),
(63, 2, 5, 2, 'Hi Tlotlo Tau, please let us know your preferred delivery option.\r\n                     1.Meet the seller at their location\r\n                     2.Seller come to your location\r\n                     3. Courier delivery to your address', '2025-06-16 17:56:00', 1),
(64, 2, 5, 2, 'hi, option 1', '2025-06-16 19:06:51', 1),
(71, 2, 5, 1, 'hi', '2025-06-16 19:37:10', 0),
(72, 2, 2, 5, '1', '2025-06-16 19:38:53', 1),
(73, 4, 5, 2, 'Your order #35 has been marked as delivered. Please confirm if the delivery was successful.', '2025-06-16 19:58:11', 1),
(74, 3, 5, 2, 'Your order #34 has been marked as delivered. Please confirm if the delivery was successful. By answering 1 for delivered and 2 for not delivered.', '2025-06-16 20:07:21', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `buyer_id` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `paystack_reference` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `buyer_id`, `total_amount`, `status`, `paystack_reference`, `created_at`) VALUES
(2, 2, 3500.00, 'pending', NULL, '2025-05-21 18:31:07'),
(3, 2, 7000.00, 'pending', NULL, '2025-05-21 18:36:57'),
(4, 2, 7000.00, 'pending', NULL, '2025-05-21 18:38:41'),
(5, 2, 7000.00, 'pending', NULL, '2025-05-21 18:38:44'),
(6, 2, 3500.00, 'pending', NULL, '2025-05-21 18:43:03'),
(7, 2, 3500.00, 'pending', NULL, '2025-05-21 19:04:59'),
(8, 2, 3500.00, 'pending', NULL, '2025-05-21 19:05:03'),
(9, 2, 3500.00, 'pending', NULL, '2025-05-21 19:05:26'),
(10, 2, 3500.00, 'pending', NULL, '2025-05-21 19:07:07'),
(11, 2, 3500.00, 'completed', NULL, '2025-05-22 20:20:22'),
(12, 2, 3500.00, 'completed', NULL, '2025-05-22 20:25:20'),
(13, 2, 3500.00, 'completed', NULL, '2025-05-22 20:31:29'),
(14, 1, 3500.00, 'completed', NULL, '2025-05-24 20:24:28'),
(15, 1, 3500.00, 'completed', NULL, '2025-05-24 20:25:37'),
(16, 1, 7500.00, 'completed', NULL, '2025-05-26 08:56:51'),
(17, 2, 3000.00, 'completed', NULL, '2025-05-28 11:45:11'),
(18, 5, 1200.00, 'completed', NULL, '2025-06-01 13:53:41'),
(19, 5, 1200.00, 'completed', NULL, '2025-06-01 14:24:57'),
(20, 2, 8200.00, 'completed', NULL, '2025-06-02 13:09:56'),
(21, 2, 9400.00, 'completed', NULL, '2025-06-16 12:38:04'),
(22, 2, 9400.00, 'completed', NULL, '2025-06-16 12:43:36'),
(23, 2, 9400.00, 'completed', NULL, '2025-06-16 12:44:28'),
(24, 2, 9400.00, 'completed', NULL, '2025-06-16 12:45:27'),
(25, 2, 9400.00, 'completed', NULL, '2025-06-16 12:46:36'),
(26, 2, 9400.00, 'completed', NULL, '2025-06-16 12:47:36'),
(27, 2, 9400.00, 'completed', NULL, '2025-06-16 14:46:31'),
(28, 2, 9400.00, 'completed', NULL, '2025-06-16 14:50:07'),
(29, 2, 9400.00, 'completed', NULL, '2025-06-16 14:50:58'),
(30, 2, 9400.00, 'completed', NULL, '2025-06-16 14:56:37'),
(31, 2, 9400.00, 'completed', NULL, '2025-06-16 14:58:52'),
(32, 2, 9400.00, 'completed', NULL, '2025-06-16 15:01:23'),
(33, 2, 9400.00, 'completed', 'txn_685030973d28b', '2025-06-16 15:06:55'),
(34, 2, 4500.00, 'Delivered', 'txn_6850335a40eef', '2025-06-16 15:08:24'),
(35, 2, 1200.00, 'Delivered', 'txn_68503425934a4', '2025-06-16 15:11:52'),
(36, 2, 4500.00, 'completed', 'txn_685081484d3c9', '2025-06-16 20:40:55');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `price_at_purchase`) VALUES
(7, 33, 2, 0.00),
(8, 33, 3, 0.00),
(9, 33, 4, 0.00),
(10, 33, 15, 0.00),
(11, 34, 3, 0.00),
(12, 35, 4, 1200.00),
(13, 36, 3, 4500.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text,
  `amount` decimal(10,2) NOT NULL,
  `category` int NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `name`, `description`, `amount`, `category`, `image_path`, `created_at`) VALUES
(2, 5, 'Silver Couch', 'Two Seater silver couch. 2m x 1m x 0.8m', 3000.00, 2, '6832ed88ce999_couch.png', '2025-05-25 10:14:32'),
(3, 5, 'TV Stand', '2m TV stand', 4500.00, 2, '68332dee1c16c_tvstand.png', '2025-05-25 14:49:18'),
(4, 5, 'TV Stand', '3m', 1200.00, 2, '68332e113d169_tvstand2.png', '2025-05-25 14:49:53'),
(5, 5, 'TV Stand', '4M', 5500.00, 2, '68332e2941ecb_tvstand3.png', '2025-05-25 14:50:17'),
(6, 5, 'TV Stand', '1,2M', 3500.00, 2, '68332e4386423_tvstand4.png', '2025-05-25 14:50:43'),
(7, 5, 'COUCH', '3 SEATER', 6500.00, 2, '68332e824e6e1_couch 2.png', '2025-05-25 14:51:46'),
(8, 5, 'COUCH', 'SLEEPER', 85000.00, 2, '68332e9ed74cc_couch3.png', '2025-05-25 14:52:14'),
(9, 5, 'COUCH', 'OLD COUCH', 85000.00, 2, '68332eb78fb9d_couch4.png', '2025-05-25 14:52:39'),
(10, 5, 'COUCH', '3M', 4500.00, 2, '68333035b1d93_couch4.png', '2025-05-25 14:59:01'),
(11, 5, 'Omoda', 'chineese', 1200000.00, 3, '683335672f4d3_veh1.png', '2025-05-25 15:21:11'),
(12, 5, 'Lambo', 'Italian', 3500000.00, 3, '68333590804eb_veh2.png', '2025-05-25 15:21:52'),
(13, 5, 'Chery', 'Chinesse', 150000.00, 3, '683335aef0179_veh3.png', '2025-05-25 15:22:22'),
(14, 5, 'Clothes', 'Mens', 12000.00, 4, '683336b96cd19_mens1.png', '2025-05-25 15:26:49'),
(15, 5, 'Clothes', 'Mens', 700.00, 4, '683336d0b6be2_men2.png', '2025-05-25 15:27:12'),
(16, 5, 'Clothes', 'Men', 3500.00, 4, '683336e340fcb_men3.png', '2025-05-25 15:27:31'),
(17, 5, 'Clothes', 'Men', 5600.00, 4, '6833370f4dd46_men4.png', '2025-05-25 15:28:15'),
(18, 5, 'Clothes', 'Women', 5600.00, 5, '68333770e47d4_women1.png', '2025-05-25 15:29:52'),
(19, 5, 'Clothes', 'Women', 8000.00, 5, '6833378447ba3_women2.png', '2025-05-25 15:30:12'),
(20, 5, 'Clothes', 'Women', 80.00, 5, '6833379931ce6_women3.png', '2025-05-25 15:30:33'),
(21, 5, 'Laptop', 'New', 5000.00, 6, '68333c2b9005c_electronic1.png', '2025-05-25 15:50:03'),
(22, 5, 'Laptop', 'Old', 2500.00, 6, '68333c417a340_electronic2.png', '2025-05-25 15:50:25'),
(23, 5, 'Airfrier', 'new', 1500.00, 6, '68333c62386ca_electronic3.png', '2025-05-25 15:50:58'),
(24, 5, '3 Bed House', 'Deposit Required', 15000.00, 7, '68333ea057d2f_prop1.png', '2025-05-25 16:00:32'),
(25, 5, '4 Bed House', 'Deposit required.', 25000.00, 7, '68333ece4cc25_prop2.png', '2025-05-25 16:01:18'),
(26, 5, '2 Bed House', 'Deposit split for first 3 months', 3400.00, 7, '68333f24ce227_prop3.png', '2025-05-25 16:02:44');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_id` int DEFAULT NULL,
  `paystack_reference` varchar(255) NOT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `total_amount`, `order_id`, `paystack_reference`, `status`, `created_at`, `update_at`) VALUES
(1, 2, 3500.00, NULL, 'txn_682e3c33ee0f6', 'pending', '2025-05-21 20:48:51', '2025-05-21 20:48:51'),
(2, 2, 3500.00, NULL, 'txn_682f84a8d45ab', 'success', '2025-05-22 20:10:16', '2025-05-22 20:13:10'),
(3, 2, 3500.00, 12, 'txn_682f86f04a6ab', 'success', '2025-05-22 20:20:00', '2025-05-22 20:25:20'),
(4, 2, 3500.00, 13, 'txn_682f89819887a', 'success', '2025-05-22 20:30:57', '2025-05-22 20:31:29'),
(5, 1, 3500.00, NULL, 'txn_6831d381a8c2f', 'pending', '2025-05-24 14:11:13', '2025-05-24 14:11:13'),
(6, 1, 3500.00, 15, 'txn_68322ad45df27', 'success', '2025-05-24 20:23:48', '2025-05-24 20:25:37'),
(7, 1, 7500.00, 16, 'txn_68342cc1d29cc', 'success', '2025-05-26 08:56:33', '2025-05-26 08:56:51'),
(8, 2, 3000.00, 17, 'txn_6836f7363843f', 'success', '2025-05-28 11:44:54', '2025-05-28 11:45:11'),
(9, 2, 3700.00, NULL, 'txn_6839eddb6aa94', 'pending', '2025-05-30 17:41:47', '2025-05-30 17:41:47'),
(10, 5, 1200.00, 18, 'txn_683c5b5130e0b', 'success', '2025-06-01 13:53:21', '2025-06-01 13:53:41'),
(11, 5, 1200.00, 19, 'txn_683c62a142542', 'success', '2025-06-01 14:24:33', '2025-06-01 14:24:57'),
(12, 2, 8200.00, 20, 'txn_683da2844cb14', 'success', '2025-06-02 13:09:24', '2025-06-02 13:09:56'),
(13, 2, 9400.00, 21, 'txn_6850101a6bfeb', 'success', '2025-06-16 12:37:46', '2025-06-16 12:38:04'),
(14, 2, 9400.00, NULL, 'txn_685010a3620bf', 'success', '2025-06-16 12:40:03', '2025-06-16 12:43:36'),
(15, 2, 9400.00, 24, 'txn_685011992f0f6', 'success', '2025-06-16 12:44:09', '2025-06-16 12:45:27'),
(16, 2, 9400.00, 26, 'txn_6850121b91096', 'success', '2025-06-16 12:46:19', '2025-06-16 12:47:36'),
(17, 5, 5700.00, NULL, 'txn_685013ce2998d', 'pending', '2025-06-16 12:53:34', '2025-06-16 12:53:34'),
(18, 5, 5700.00, NULL, 'txn_68502dc211d1a', 'pending', '2025-06-16 14:44:18', '2025-06-16 14:44:18'),
(19, 2, 9400.00, 28, 'txn_68502de09586d', 'success', '2025-06-16 14:44:48', '2025-06-16 14:50:07'),
(20, 2, 9400.00, 29, 'txn_68502f424ae5d', 'success', '2025-06-16 14:50:42', '2025-06-16 14:50:58'),
(21, 2, 9400.00, 33, 'txn_685030973d28b', 'success', '2025-06-16 14:56:23', '2025-06-16 15:06:56'),
(22, 2, 4500.00, 34, 'txn_6850335a40eef', 'success', '2025-06-16 15:08:10', '2025-06-16 15:08:24'),
(23, 2, 1200.00, 35, 'txn_68503425934a4', 'success', '2025-06-16 15:11:33', '2025-06-16 15:11:52'),
(24, 2, 4500.00, 36, 'txn_685081484d3c9', 'success', '2025-06-16 20:40:40', '2025-06-16 20:40:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `postal_code` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `role`, `phone`, `address1`, `address2`, `city`, `province`, `password`, `created_at`, `postal_code`) VALUES
(1, 'Tlotlo Tau', 'tchk438@gmail.com', 'buyer', '068 026 1635', '88a Summit View Estate', 'Blue Hills', 'Midrand', 'Gauteng', '$2y$10$hOoMqaGcRZwh24HCQZGfcu.VWzg6WJJ3IM3fL4j5H85r//1bEaeX.', '2025-05-14 19:15:36', '1685'),
(2, 'Tlotlo Tau', 'eduv4777411@vossie.net', 'buyer', '60261635', '88a Maluti Street', 'Summit View Estate', 'Midrand', 'Gauteng', '$2y$10$YTDIxEUEkP3ZPbf69dR.bel/ywzJFQ/Fa3RKoEPBjnhrz2h51vbYq', '2025-05-14 21:48:20', '1685'),
(3, 'AdminTest', 'admin@buyitt.com', 'admin', '1234567890', 'Admin Address', '', 'Admin City', 'Admin Province', '$2y$10$7PQnW5.H0LYiTBhn8gr23.aPJZTzM/ULpyInKeJstzv5/5k.FSxP6', '2025-05-15 07:08:48', '1234'),
(4, 'Lesego', '24228190@stadio.ac.za', 'seller', '0639383309', 'Ethopia Street', '', 'Vosloruus', 'Gauteng', '$2y$10$IO4iDxEWAid2eQwAHAjlK.oQ6qZ8bxNW1ndRRcd/Xm/Bnx1meuXy2', '2025-05-16 11:06:52', '2000'),
(5, 'Cj', '123@buyitt.com', 'seller', '068 026 1635', '56 maluti street', '', 'Midrand', 'Gauteng', '$2y$10$UzPQ4a.E2Ygx27nWstj3kexE.KFrpS3zzx9A6gbR28zdc0lbALhpu', '2025-05-25 09:47:29', '1685'),
(6, 'zack', '14566@gmail.com', 'buyer', '1234567', '19 Ridge Street', 'Glen Austin AH', 'Midrand', 'Gauteng', '$2y$10$7XomDVdU8XfN2mb2RMEIV.Cr/2Y2NhOTbp0FpO6vqcKhxUMCaR3Cy', '2025-06-16 20:44:59', '1685');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uidx_cart` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `from_user` (`from_user`),
  ADD KEY `to_user` (`to_user`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buyer_id` (`buyer_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`from_user`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`to_user`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category`) REFERENCES `categories` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
