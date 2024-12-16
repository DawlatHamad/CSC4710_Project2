-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 16, 2024 at 02:44 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `final`
--

-- --------------------------------------------------------

--
-- Table structure for table `Card`
--

CREATE TABLE `Card` (
  `cardid` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `month` tinyint(4) DEFAULT NULL,
  `year` tinyint(4) DEFAULT NULL,
  `cvv` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Card`
--

INSERT INTO `Card` (`cardid`, `userid`, `nickname`, `number`, `name`, `month`, `year`, `cvv`) VALUES
(1, 1, 'David\'s Card', '1234 1234 1234 1234', 'David Smith', 8, 25, 428),
(2, 2, 'Dawlat Discover', '5678 5678 5678 5678', 'Dawlat Hamad', 11, 25, 748),
(3, 3, 'Sumaiya\'s Card', '1234 5678 1234 5678', 'Sumaiya Ahmed', 4, 25, 856),
(4, 4, 'Jane\'s Master', '4567 4567 4567 4567', 'Jane Doe', 9, 25, 387),
(5, 5, 'John Discover', '6789 6789 6789 6789', 'John Doe', 3, 25, 835),
(6, 2, 'Dawlat Master', '3678 4856 2903 4783', 'Dawlat Hamad', 4, 24, 875);

-- --------------------------------------------------------

--
-- Table structure for table `Quote`
--

CREATE TABLE `Quote` (
  `quoteid` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `dimension_length` int(11) DEFAULT NULL,
  `dimension_width` int(11) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `photo1` varchar(255) DEFAULT NULL,
  `photo2` varchar(255) DEFAULT NULL,
  `photo3` varchar(255) DEFAULT NULL,
  `photo4` varchar(255) DEFAULT NULL,
  `photo5` varchar(255) DEFAULT NULL,
  `client_note` varchar(255) DEFAULT NULL,
  `status` enum('pending','accepted','refused') DEFAULT 'pending',
  `admin_note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Quote`
--

INSERT INTO `Quote` (`quoteid`, `userid`, `address`, `dimension_length`, `dimension_width`, `price`, `photo1`, `photo2`, `photo3`, `photo4`, `photo5`, `client_note`, `status`, `admin_note`, `created_at`) VALUES
(1, 2, '683 Cherry Avenue', 12, 24, 25000, '1.png', '2.png', '3.png', '4.png', '5.png', 'Before the New Year', 'accepted', 'Okay', '2024-11-11 13:06:44'),
(2, 3, '456 Sugar Cane St', 24, 24, 24000, '1.png', '2.png', '3.png', '4.png', '5.png', 'lala', 'accepted', '', '2024-11-19 16:12:36'),
(3, 3, '356 Louis Lane', 25, 24, 50000, '1.png', '2.png', '3.png', '4.png', '5.png', 'lala', 'accepted', '', '2024-11-27 16:13:03'),
(4, 3, '356 Louis Lane', 50, 24, 60000, '1.png', '2.png', '3.png', '4.png', '5.png', 'lala', 'accepted', '', '2024-12-11 16:13:40'),
(5, 5, '456 Sugar Cane St', 24, 24, 24000, '1.png', '2.png', '3.png', '4.png', '5.png', 'Need Soon', 'accepted', '', '2024-12-14 16:56:15'),
(6, 2, '932 Ice Cream St', 24, 24, 24000, '1.png', '2.png', '3.png', '4.png', '5.png', 'In March Please', 'accepted', '', '2024-12-15 17:18:45');

-- --------------------------------------------------------

--
-- Table structure for table `TransactionPaid`
--

CREATE TABLE `TransactionPaid` (
  `historyid` int(11) NOT NULL,
  `transactionid` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `quoteid` int(11) DEFAULT NULL,
  `workid` int(11) DEFAULT NULL,
  `cardid` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `paid_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `TransactionPaid`
--

INSERT INTO `TransactionPaid` (`historyid`, `transactionid`, `userid`, `quoteid`, `workid`, `cardid`, `created_at`, `paid_date`) VALUES
(1, 3, 2, 6, 6, 2, '2024-12-15 17:25:17', '2024-12-15 17:26:23');

-- --------------------------------------------------------

--
-- Table structure for table `Transactions`
--

CREATE TABLE `Transactions` (
  `transactionid` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `quoteid` int(11) DEFAULT NULL,
  `workid` int(11) DEFAULT NULL,
  `cardid` int(11) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `charge_status` enum('pending','charge','deny') DEFAULT 'pending',
  `client_note` varchar(255) DEFAULT NULL,
  `pay_status` enum('pending','paid','declined') DEFAULT 'pending',
  `admin_note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Transactions`
--

INSERT INTO `Transactions` (`transactionid`, `userid`, `quoteid`, `workid`, `cardid`, `price`, `start_date`, `end_date`, `charge_status`, `client_note`, `pay_status`, `admin_note`, `created_at`) VALUES
(1, 2, 1, 1, 2, 30000, '2024-12-23', '2024-12-30', 'charge', 'Charge', 'paid', 'Please Pay', '2024-12-15 13:22:40'),
(2, 5, 5, 5, NULL, 25000, '2024-12-17', '2024-12-23', 'pending', NULL, 'pending', 'Please Pay', '2024-12-02 16:58:26'),
(3, 2, 6, 6, 2, 30000, '2025-03-01', '2025-03-15', 'charge', 'Charge', 'paid', 'Please Pay.', '2024-12-15 17:25:17');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `userid` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'client'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`userid`, `email`, `password`, `firstname`, `lastname`, `address`, `phone`, `role`) VALUES
(1, 'david_smith@gmail.com', '21232f297a57a5a743894a0e4a801fc3', 'David', 'Smith', '123 Maple St.', '2578467844', 'contractor'),
(2, 'dawlathamad@icloud.com', 'f6182f0359f72aae12fb90d305ccf9eb', 'Dawlat', 'Hamad', '683 Cherry Avenue', '3139709504', 'client'),
(3, 'ahmedsumaiya587@gmail.com', 'd7af994f1f1ef8b5e3beb9f7fb139f57', 'Sumaiya', 'Ahmed', '826 Cherry Avenue', '4789463779', 'client'),
(4, 'janedoe1997@gmail.com', 'cd6c416546d256996e4941fe1170458e', 'Jane', 'Doe', '456 Sugar Cane St', '3794125800', 'client'),
(5, 'john_doe_67@gmail.com', '64414f23baed90db1e20de4011131328', 'John', 'Doe', '456 Sugar Cane St', '2347357556', 'client');

-- --------------------------------------------------------

--
-- Table structure for table `Work`
--

CREATE TABLE `Work` (
  `workid` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `quoteid` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `price` float DEFAULT NULL,
  `client_status` enum('pending','accepted','refused') DEFAULT 'pending',
  `client_note` varchar(255) DEFAULT NULL,
  `admin_status` enum('pending','accepted','refused') DEFAULT 'pending',
  `admin_note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Work`
--

INSERT INTO `Work` (`workid`, `userid`, `quoteid`, `start_date`, `end_date`, `price`, `client_status`, `client_note`, `admin_status`, `admin_note`, `created_at`) VALUES
(1, 2, 1, '2024-12-23', '2024-12-30', 30000, 'accepted', 'I will pay more.', 'accepted', 'Faster shipping = more money', '2024-12-15 13:15:03'),
(2, 3, 2, '2024-12-23', '2024-12-30', 24000, 'pending', NULL, 'pending', 'lala', '2024-12-15 16:15:02'),
(3, 3, 3, '2024-12-23', '2024-12-23', 50000, 'pending', NULL, 'pending', 'lala', '2024-12-15 16:15:17'),
(4, 3, 4, '2024-12-24', '2024-12-31', 75000, 'pending', NULL, 'pending', 'lala', '2024-12-15 16:15:44'),
(5, 5, 5, '2024-12-17', '2024-12-23', 25000, 'accepted', 'Time is good.', 'accepted', 'Okay', '2024-12-15 16:57:00'),
(6, 2, 6, '2025-03-01', '2025-03-15', 30000, 'accepted', 'Good dates.', 'accepted', 'Okay', '2024-12-15 17:19:12');

-- --------------------------------------------------------

--
-- Table structure for table `WorkHistory`
--

CREATE TABLE `WorkHistory` (
  `historyid` int(11) NOT NULL,
  `workid` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `quoteid` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `price` float DEFAULT NULL,
  `client_status` enum('pending','accepted','refused') DEFAULT 'pending',
  `client_note` varchar(255) DEFAULT NULL,
  `admin_status` enum('pending','accepted','refused') DEFAULT 'pending',
  `admin_note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `WorkHistory`
--

INSERT INTO `WorkHistory` (`historyid`, `workid`, `userid`, `quoteid`, `start_date`, `end_date`, `price`, `client_status`, `client_note`, `admin_status`, `admin_note`, `created_at`) VALUES
(1, 1, 2, 1, '2024-12-23', '2024-12-30', 30000, 'pending', NULL, 'pending', 'Faster shipping = more money', '2024-12-15 13:15:03'),
(2, 1, 2, 1, '2024-12-23', '2024-12-30', 30000, 'accepted', 'I will pay more.', 'accepted', 'Faster shipping = more money', '2024-12-15 13:22:45'),
(3, 2, 3, 2, '2024-12-23', '2024-12-30', 24000, 'pending', NULL, 'pending', 'lala', '2024-12-15 16:15:02'),
(4, 3, 3, 3, '2024-12-23', '2024-12-23', 50000, 'pending', NULL, 'pending', 'lala', '2024-12-15 16:15:17'),
(5, 4, 3, 4, '2024-12-24', '2024-12-31', 75000, 'pending', NULL, 'pending', 'lala', '2024-12-15 16:15:44'),
(6, 5, 5, 5, '2024-12-17', '2024-12-23', 25000, 'pending', NULL, 'pending', 'Okay', '2024-12-15 16:57:00'),
(7, 5, 5, 5, '2024-12-17', '2024-12-23', 25000, 'accepted', 'Time is good.', 'accepted', 'Okay', '2024-12-15 16:58:39'),
(8, 6, 2, 6, '2025-03-01', '2025-03-15', 30000, 'pending', NULL, 'pending', 'Okay', '2024-12-15 17:19:12'),
(9, 6, 2, 6, '2025-03-01', '2025-03-15', 30000, 'accepted', 'Good dates.', 'accepted', 'Okay', '2024-12-15 17:20:03'),
(10, 6, 2, 6, '2025-03-01', '2025-03-15', 30000, 'accepted', 'Good dates.', 'pending', 'Okay', '2024-12-15 17:20:11'),
(11, 6, 2, 6, '2025-03-01', '2025-03-15', 30000, 'accepted', 'Good dates.', 'accepted', 'Okay', '2024-12-15 17:25:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Card`
--
ALTER TABLE `Card`
  ADD PRIMARY KEY (`cardid`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `Quote`
--
ALTER TABLE `Quote`
  ADD PRIMARY KEY (`quoteid`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `TransactionPaid`
--
ALTER TABLE `TransactionPaid`
  ADD PRIMARY KEY (`historyid`),
  ADD KEY `transactionid` (`transactionid`),
  ADD KEY `userid` (`userid`),
  ADD KEY `quoteid` (`quoteid`),
  ADD KEY `workid` (`workid`),
  ADD KEY `cardid` (`cardid`);

--
-- Indexes for table `Transactions`
--
ALTER TABLE `Transactions`
  ADD PRIMARY KEY (`transactionid`),
  ADD KEY `userid` (`userid`),
  ADD KEY `quoteid` (`quoteid`),
  ADD KEY `workid` (`workid`),
  ADD KEY `cardid` (`cardid`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `Work`
--
ALTER TABLE `Work`
  ADD PRIMARY KEY (`workid`),
  ADD KEY `userid` (`userid`),
  ADD KEY `quoteid` (`quoteid`);

--
-- Indexes for table `WorkHistory`
--
ALTER TABLE `WorkHistory`
  ADD PRIMARY KEY (`historyid`),
  ADD KEY `workid` (`workid`),
  ADD KEY `userid` (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Card`
--
ALTER TABLE `Card`
  MODIFY `cardid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `Quote`
--
ALTER TABLE `Quote`
  MODIFY `quoteid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `TransactionPaid`
--
ALTER TABLE `TransactionPaid`
  MODIFY `historyid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Transactions`
--
ALTER TABLE `Transactions`
  MODIFY `transactionid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Work`
--
ALTER TABLE `Work`
  MODIFY `workid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `WorkHistory`
--
ALTER TABLE `WorkHistory`
  MODIFY `historyid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Card`
--
ALTER TABLE `Card`
  ADD CONSTRAINT `card_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `Users` (`userid`);

--
-- Constraints for table `Quote`
--
ALTER TABLE `Quote`
  ADD CONSTRAINT `quote_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `Users` (`userid`);

--
-- Constraints for table `TransactionPaid`
--
ALTER TABLE `TransactionPaid`
  ADD CONSTRAINT `transactionpaid_ibfk_1` FOREIGN KEY (`transactionid`) REFERENCES `Transactions` (`transactionid`),
  ADD CONSTRAINT `transactionpaid_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `Users` (`userid`),
  ADD CONSTRAINT `transactionpaid_ibfk_3` FOREIGN KEY (`quoteid`) REFERENCES `Quote` (`quoteid`),
  ADD CONSTRAINT `transactionpaid_ibfk_4` FOREIGN KEY (`workid`) REFERENCES `Work` (`workid`),
  ADD CONSTRAINT `transactionpaid_ibfk_5` FOREIGN KEY (`cardid`) REFERENCES `Card` (`cardid`);

--
-- Constraints for table `Transactions`
--
ALTER TABLE `Transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `Users` (`userid`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`quoteid`) REFERENCES `Quote` (`quoteid`),
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`workid`) REFERENCES `Work` (`workid`),
  ADD CONSTRAINT `transactions_ibfk_4` FOREIGN KEY (`cardid`) REFERENCES `Card` (`cardid`);

--
-- Constraints for table `Work`
--
ALTER TABLE `Work`
  ADD CONSTRAINT `work_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `Users` (`userid`),
  ADD CONSTRAINT `work_ibfk_2` FOREIGN KEY (`quoteid`) REFERENCES `Quote` (`quoteid`);

--
-- Constraints for table `WorkHistory`
--
ALTER TABLE `WorkHistory`
  ADD CONSTRAINT `workhistory_ibfk_1` FOREIGN KEY (`workid`) REFERENCES `Work` (`workid`),
  ADD CONSTRAINT `workhistory_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `Users` (`userid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
