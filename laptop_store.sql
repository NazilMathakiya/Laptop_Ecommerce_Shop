-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2025 at 09:34 AM
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
-- Database: `laptop_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_master`
--

CREATE TABLE `admin_master` (
  `admin_name` varchar(100) NOT NULL COMMENT 'Admin Name.',
  `admin_email` varchar(150) NOT NULL COMMENT 'Admin Email.\r\n',
  `admin_password` varchar(10) NOT NULL COMMENT 'Admin Password',
  `admin_mobile_no` bigint(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_master`
--

INSERT INTO `admin_master` (`admin_name`, `admin_email`, `admin_password`, `admin_mobile_no`) VALUES
('Arman Khorajiya', 'armankhorajiyask@gmail.com', '123', 7869248648),
('', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `brand_master`
--

CREATE TABLE `brand_master` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(100) NOT NULL,
  `brand_status` enum('active','inactive') NOT NULL CHECK (`brand_status` in ('active','inactive'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brand_master`
--

INSERT INTO `brand_master` (`brand_id`, `brand_name`, `brand_status`) VALUES
(1, 'Asus', 'active'),
(6, 'Dell', 'active'),
(7, 'HP', 'active'),
(8, 'Lenovo', 'active'),
(14, 'Apple', 'active'),
(15, 'Samsung', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `cart_master`
--

CREATE TABLE `cart_master` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_master`
--

INSERT INTO `cart_master` (`cart_id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(44, 24, 72, 1, '2025-09-08 11:22:54');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_master`
--

CREATE TABLE `coupon_master` (
  `coupon_id` int(11) NOT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `expiry_date` date NOT NULL,
  `usage_limit` int(11) DEFAULT 1,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupon_master`
--

INSERT INTO `coupon_master` (`coupon_id`, `coupon_code`, `discount_type`, `discount_value`, `min_order_amount`, `expiry_date`, `usage_limit`, `status`) VALUES
(1, 'WELCOME10', 'percentage', 10.00, 50000.00, '2025-12-31', 98, 'active'),
(2, 'SAVE500', 'fixed', 500.00, 30000.00, '2025-12-31', 50, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`) VALUES
(88, 101, 72, 1),
(89, 102, 72, 1),
(94, 107, 75, 1),
(96, 109, 79, 1),
(97, 110, 72, 1),
(98, 111, 83, 1),
(104, 117, 72, 1),
(105, 118, 72, 1),
(106, 119, 72, 1),
(107, 120, 72, 1),
(108, 121, 72, 1),
(109, 122, 72, 1),
(110, 123, 72, 1),
(111, 124, 72, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_master`
--

CREATE TABLE `order_master` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` varchar(50) DEFAULT 'Pending',
  `order_date` datetime DEFAULT current_timestamp(),
  `delivery_address` varchar(255) DEFAULT NULL,
  `payment_mode` varchar(20) DEFAULT 'COD',
  `full_name` text NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_mobile_no` bigint(10) NOT NULL,
  `total_quantity` int(11) DEFAULT 0,
  `coupon_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_mobile` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_master`
--

INSERT INTO `order_master` (`order_id`, `user_id`, `total_amount`, `order_status`, `order_date`, `delivery_address`, `payment_mode`, `full_name`, `user_email`, `user_mobile_no`, `total_quantity`, `coupon_code`, `discount_amount`, `customer_name`, `customer_email`, `customer_mobile`) VALUES
(101, 24, 103410.00, 'Cancelled', '2025-08-13 09:31:23', 'Aman Park Society\r\nHouse no 6', 'COD', 'Arman Khorajiya', 'armankhorajiyask@gmail.com', 7869248648, 1, NULL, 0.00, NULL, NULL, NULL),
(102, 24, 103410.00, 'Cancelled', '2025-08-13 09:57:23', 'Aman Park Society\r\nHouse no 6', 'COD', 'Arman Khorajiya', 'armankhorajiyask@gmail.com', 7869248648, 1, NULL, 0.00, NULL, NULL, NULL),
(107, 24, 188999.10, 'Delivered', '2025-08-23 10:52:50', 'Aman Park Society\r\nHouse no 6', 'COD', 'Arman Khorajiya', 'armankhorajiyask@gmail.com', 7869248648, 1, 'WELCOME10', 20999.90, NULL, NULL, NULL),
(109, 24, 38499.00, 'Delivered', '2025-08-23 19:07:01', 'Aman Park Society\r\nHouse no 6', 'COD', 'Arman Khorajiya', 'armankhorajiyask@gmail.com', 7869248648, 1, 'SAVE500', 500.00, NULL, NULL, NULL),
(110, 24, 114400.00, 'Shipped', '2025-08-24 12:23:47', 'Aman Park Society\r\nHouse no 6', 'COD', 'Arman Khorajiya', 'armankhorajiyask@gmail.com', 7869248648, 1, 'SAVE500', 500.00, NULL, NULL, NULL),
(111, 24, 144500.00, 'Processing', '2025-08-25 14:53:55', 'Aman Park Society\r\nHouse no 6', 'COD', 'Arman Khorajiya', 'armankhorajiyask@gmail.com', 7869248648, 1, 'SAVE500', 500.00, NULL, NULL, NULL),
(117, 24, 114400.00, 'Processing', '2025-09-03 13:04:35', 'Aman Park Society\r\nHouse no 6', 'COD', 'Nazil', 'mathakiyanazil03@gmail.com', 7869248648, 1, 'SAVE500', 500.00, NULL, NULL, NULL),
(118, 24, 114400.00, 'Pending', '2025-09-03 13:06:34', 'Aman Park Society\r\nHouse no 6', 'COD', 'Jamil Badi', '23020201107@darshan.ac.in', 7869248648, 1, 'SAVE500', 500.00, NULL, NULL, NULL),
(119, 24, 114400.00, 'Pending', '2025-09-03 13:07:46', 'Aman Park Society\r\nHouse no 6', 'COD', 'Pranav', '23020201044@darshan.ac.in', 4569871230, 1, 'SAVE500', 500.00, NULL, NULL, NULL),
(120, 24, 114900.00, 'Pending', '2025-09-08 11:17:53', 'Aman Park Society\r\nHouse no 6', 'COD', 'Arman Khorajiya', 'armankhorajiyask@com', 7869248648, 1, '', 0.00, NULL, NULL, NULL),
(121, 24, 114900.00, 'Cancelled', '2025-09-08 11:20:20', 'Aman Park Society\r\nHouse no 6', 'COD', 'Arman Khorajiya', 'armankhorajiyask@com', 7869248648, 1, '', 0.00, NULL, NULL, NULL),
(122, 24, 114900.00, 'Delivered', '2025-09-08 11:21:44', 'Aman Park Society\r\nHouse no 6', 'COD', 'Arman Khorajiya', 'armankhorajiyask@com', 7869248648, 1, '', 0.00, NULL, NULL, NULL),
(123, 24, 114900.00, 'Processing', '2025-09-08 11:22:49', 'Aman Park Society\r\nHouse no 6', 'COD', 'Arman Khorajiya', 'armankhorajiyaskgmail@com', 7869248648, 1, '', 0.00, NULL, NULL, NULL),
(124, 24, 114900.00, 'Shipped', '2025-09-08 12:17:33', 'Aman Park Society\r\nHouse no 6', 'COD', 'Arman Khorajiya', 'armankhorajiyask@gmail.com', 7869248648, 1, '', 0.00, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `img_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_master`
--

CREATE TABLE `product_master` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `product_description` text NOT NULL,
  `brand_id` int(11) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `added_at` datetime DEFAULT current_timestamp(),
  `image_path` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_master`
--

INSERT INTO `product_master` (`product_id`, `product_name`, `product_description`, `brand_id`, `product_price`, `stock_quantity`, `added_at`, `image_path`, `price`, `stock`) VALUES
(72, 'Apple MacBook Air M2 (2024)', 'Processor: Apple M2 chip with 8‑core CPU and 10‑core GPU\r\n\r\nRAM: 8GB unified memory\r\n\r\nStorage: 256GB SSD\r\n\r\nDisplay: 13.6-inch Liquid Retina Display\r\n\r\nBattery Life: Up to 18 hours\r\n\r\nWeight: 1.24 kg\r\n\r\nOS: macOS', 14, 114900.00, 25, '2025-08-02 23:45:35', 'http://localhost/AdminSideWebsite/Images/apple_macbook_m2.jpeg', 0.00, 0),
(75, 'Dell XPS 15 (2024)', 'Processor: Intel Core i7-13700H (13th Gen)\r\n\r\nRAM: 16GB DDR5\r\n\r\nStorage: 512GB SSD\r\n\r\nGraphics: NVIDIA RTX 4060 (6GB)\r\n\r\nDisplay: 15.6\" FHD+ InfinityEdge Anti-Glare\r\n\r\nWeight: 1.9 kg\r\n\r\nOS: Windows 11', 6, 209999.00, 30, '2025-08-03 00:00:19', 'http://localhost/AdminSideWebsite/Images/Dell.jpeg', 0.00, 0),
(76, 'HP Spectre x360 14', 'Processor: Intel Core i7-1355U (13th Gen)\r\n\r\nRAM: 16GB LPDDR4x\r\n\r\nStorage: 1TB SSD\r\n\r\nDisplay: 13.5\" 3K2K OLED Touch Display\r\n\r\nBattery Life: Up to 16 hours\r\n\r\nWeight: 1.36 kg\r\n\r\nOS: Windows 11 Home', 7, 139999.00, 30, '2025-08-03 00:02:43', 'http://localhost/AdminSideWebsite/Images/hp_spectre.jpg', 0.00, 0),
(78, 'ASUS ZenBook 14 OLED (2025)', 'Processor: Intel Core Ultra 7\r\n\r\nRAM: 16GB LPDDR5\r\n\r\nStorage: 1TB SSD\r\n\r\nDisplay: 14\" 2.8K OLED HDR\r\n\r\nBattery: Up to 18 hours\r\n\r\nWeight: 1.39 kg', 1, 132000.00, 30, '2025-08-03 20:43:39', 'http://localhost/AdminSideWebsite/Images/asus_zenbook_14.jpg', 0.00, 0),
(80, 'ASUS TUF Gaming A15 (2025)', 'Processor: AMD Ryzen 7 7735HS\r\n\r\nGraphics: NVIDIA GeForce RTX 3050\r\n\r\nRAM: 16GB\r\n\r\nStorage: 512GB SSD\r\n\r\nDisplay: 15.6\" FHD, 144Hz\r\n\r\nBattery: Up to 8 hours', 1, 63990.00, 30, '2025-08-03 20:49:27', 'http://localhost/AdminSideWebsite/Images/asus_tuf.jpg', 0.00, 0),
(82, 'Lenovo ThinkPad X1 Carbon Gen 11', 'Processor: Intel Core i7-1370P\r\n\r\nRAM: 16GB\r\n\r\nStorage: 1TB SSD\r\n\r\nDisplay: 14\" WUXGA\r\n\r\nBattery: Up to 15 hours\r\n\r\nWeight: 1.12 kg', 8, 175000.00, 30, '2025-08-03 20:55:59', 'http://localhost/AdminSideWebsite/Images/lenovo_thinkpad_x1.jpg', 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `review_master`
--

CREATE TABLE `review_master` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL COMMENT 'Review & Feedback',
  `review_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Review Submission Time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review_master`
--

INSERT INTO `review_master` (`review_id`, `user_id`, `product_id`, `rating`, `comment`, `review_date`) VALUES
(9, 24, 63, 5, 'I liked it personally.', '2025-07-26 15:09:15'),
(17, 24, 80, 5, 'Good Product.', '2025-09-01 01:42:03'),
(18, 24, 80, 5, 'It is very good product for Gaming..', '2025-09-01 01:42:38'),
(20, 24, 78, 5, 'Very Good product.', '2025-09-01 03:32:28'),
(22, 33, 80, 5, 'Very reliable to play high graphics games.', '2025-09-01 03:35:24');

-- --------------------------------------------------------

--
-- Table structure for table `user_master`
--

CREATE TABLE `user_master` (
  `user_id` int(10) NOT NULL COMMENT 'User Id',
  `full_name` varchar(150) NOT NULL COMMENT 'User Full Name',
  `user_email` varchar(100) NOT NULL COMMENT 'User Email',
  `user_password` int(10) NOT NULL COMMENT 'User Password',
  `user_mobile_no` bigint(20) NOT NULL COMMENT 'User Mobile Number',
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6) COMMENT 'Create Account Time',
  `user_address` text NOT NULL COMMENT 'User Address'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_master`
--

INSERT INTO `user_master` (`user_id`, `full_name`, `user_email`, `user_password`, `user_mobile_no`, `created_at`, `user_address`) VALUES
(24, 'Arman Khorajiya F.', 'armankhorajiyask@gmail.com', 123, 7869248648, '2025-09-02 13:57:15.180706', 'Aman Park Society\r\nHouse no 5');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brand_master`
--
ALTER TABLE `brand_master`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `brand_name` (`brand_name`);

--
-- Indexes for table `cart_master`
--
ALTER TABLE `cart_master`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_cart_user` (`user_id`),
  ADD KEY `fk_cart_product` (`product_id`);

--
-- Indexes for table `coupon_master`
--
ALTER TABLE `coupon_master`
  ADD PRIMARY KEY (`coupon_id`),
  ADD UNIQUE KEY `coupon_code` (`coupon_code`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_items_ibfk_1` (`order_id`);

--
-- Indexes for table `order_master`
--
ALTER TABLE `order_master`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_user_order` (`user_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`img_id`),
  ADD KEY `fk_product_image` (`product_id`);

--
-- Indexes for table `product_master`
--
ALTER TABLE `product_master`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_brand` (`brand_id`);

--
-- Indexes for table `review_master`
--
ALTER TABLE `review_master`
  ADD PRIMARY KEY (`review_id`);

--
-- Indexes for table `user_master`
--
ALTER TABLE `user_master`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brand_master`
--
ALTER TABLE `brand_master`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `cart_master`
--
ALTER TABLE `cart_master`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `coupon_master`
--
ALTER TABLE `coupon_master`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `order_master`
--
ALTER TABLE `order_master`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `img_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_master`
--
ALTER TABLE `product_master`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `review_master`
--
ALTER TABLE `review_master`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `user_master`
--
ALTER TABLE `user_master`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'User Id', AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_master`
--
ALTER TABLE `cart_master`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `product_master` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `user_master` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order_master` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_master`
--
ALTER TABLE `order_master`
  ADD CONSTRAINT `fk_user_order` FOREIGN KEY (`user_id`) REFERENCES `user_master` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_image` FOREIGN KEY (`product_id`) REFERENCES `product_master` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_master`
--
ALTER TABLE `product_master`
  ADD CONSTRAINT `fk_brand` FOREIGN KEY (`brand_id`) REFERENCES `brand_master` (`brand_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
