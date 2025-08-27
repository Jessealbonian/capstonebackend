-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 12, 2025 at 07:00 PM
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
-- Database: `home_hoa`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `email`, `password`, `admin_code`) VALUES
(1, 'loulou', 'louis@gmail.com', '$2y$10$RIanZBWBcWyiVqREN5OQrOhghwa.wo48Q/0k6iAdmtQMhwlchyA6u', 'Admin Code'),
(2, 'louisjohn', 'louisjohn@gmail.com', '$2y$10$BMvMq1BBPrdk0Y4WAQ6aCeW6a3uUO4YYQFGnimm/qQrwr25WZ.vrK', 'Admin Code'),
(3, 'admin', 'adminhehe@gmail.com', '$2y$10$oXvx/b4wh2Br3AAsv4rQQO.lZyDB88l7ILk4g3Qn7BH1wYH.Ms/wu', 'Admin Code'),
(4, 'Jomarie Del Rosario', 'jojodr@gmail.com', '$2y$10$Ufrjlx/dUPCVc1y1Zfvq2ehCH8rEikWXpERkSSc/tmx1ZToXgGrWa', 'Admin Code');

-- --------------------------------------------------------

--
-- Table structure for table `class_routines`
--

CREATE TABLE `class_routines` (
  `class_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `class_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `mondayRoutine` text DEFAULT NULL,
  `mondayintensity` varchar(32) DEFAULT NULL,
  `tuesdayRoutine` text DEFAULT NULL,
  `tuesdayintensity` varchar(32) DEFAULT NULL,
  `wednesdayRoutine` text DEFAULT NULL,
  `wednesdayintensity` varchar(32) DEFAULT NULL,
  `thursdayRoutine` text DEFAULT NULL,
  `thursdayintensity` varchar(32) DEFAULT NULL,
  `fridayRoutine` text DEFAULT NULL,
  `fridayintensity` varchar(32) DEFAULT NULL,
  `saturdayRoutine` text DEFAULT NULL,
  `saturdayintensity` varchar(32) DEFAULT NULL,
  `sundayRoutine` text DEFAULT NULL,
  `sundayintensity` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_routines`
--

INSERT INTO `class_routines` (`class_id`, `admin_id`, `class_name`, `description`, `mondayRoutine`, `mondayintensity`, `tuesdayRoutine`, `tuesdayintensity`, `wednesdayRoutine`, `wednesdayintensity`, `thursdayRoutine`, `thursdayintensity`, `fridayRoutine`, `fridayintensity`, `saturdayRoutine`, `saturdayintensity`, `sundayRoutine`, `sundayintensity`) VALUES
(7, NULL, 'test4', 'test4', 'Distance', 'Easy', 'Sprint', 'Easy', 'Distance', 'Hard', 'Fartlek', 'Medium', 'Distance', 'Medium', 'Distance', 'Hard', 'Fartlek', 'Medium'),
(10, 5, 'test5', 'test5', 'Distance', 'Easy', 'Sprint', 'Easy', 'Distance', 'Easy', 'Distance', 'Hard', 'Distance', 'Hard', 'Sprint', 'Medium', 'Fartlek', 'Medium');

-- --------------------------------------------------------

--
-- Table structure for table `codegen`
--

CREATE TABLE `codegen` (
  `code_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `Requestedbycoach` varchar(255) DEFAULT NULL,
  `student_redeemer` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `time redeemed` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `codegen`
--

INSERT INTO `codegen` (`code_id`, `class_id`, `code`, `Requestedbycoach`, `student_redeemer`, `user_id`, `time`, `time redeemed`) VALUES
(1, 10, 'P30ZUCV', '5', 'test1', NULL, '2025-08-11 16:11:41', '2025-08-12 05:58:02'),
(2, 10, 'M70SM0S', '5', 'test1', 29, '2025-08-11 16:12:46', '2025-08-12 06:10:57'),
(3, 10, 'ZS2RGXJ', '5', 'test1', 29, '2025-08-11 16:12:46', '2025-08-12 06:11:37'),
(4, 10, '21Y65RL', '5', 'test1', 29, '2025-08-11 16:12:46', '2025-08-12 06:19:49'),
(5, 10, '5FF8THD', '5', 'test1', 29, '2025-08-11 16:12:46', '2025-08-12 06:58:31'),
(6, 10, 'NO5M9TQ', '5', 'test1', 29, '2025-08-11 16:12:46', '2025-08-12 07:16:16'),
(7, 10, 'NHHKOCK', '5', 'test1', 29, '2025-08-11 16:29:50', '2025-08-12 07:24:07'),
(8, 10, 'I7U8B5T', '5', NULL, NULL, '2025-08-11 16:29:50', NULL),
(9, 10, 'O19C02X', '5', NULL, NULL, '2025-08-11 16:50:30', NULL),
(10, 10, 'BYDSMKE', '5', NULL, NULL, '2025-08-11 18:27:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `location` varchar(255) NOT NULL,
  `attendees` int(11) DEFAULT 0,
  `status` enum('Upcoming','Completed','Pending') NOT NULL DEFAULT 'Upcoming',
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hoa_admins`
--

CREATE TABLE `hoa_admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hoa_admins`
--

INSERT INTO `hoa_admins` (`admin_id`, `username`, `email`, `password`, `admin_code`) VALUES
(1, 'rei@gmail.com', '123456789', '$2y$10$MJSmLuszRc990YdRhPjWGOIsDLbew.NVHwU3qEwxOXvrA6qrLcIpO', 'admin_677656d5429b6'),
(2, 'admin1', 'admin1@gmail.com', '$2y$10$lYpcPArDJw8nkaRo01K3WOzDC14UO/..PzGsFZpCTR9PwYhrjgNMK', 'admin_6776584cc4b20'),
(3, 'Jomarie DD', 'jomar@gmail.com', '$2y$10$rh3hOVf/KkwuV1hrf2UTNefHSdDI/eThHnmqlw.wa9xDoZGJvxcL6', 'admin_677c21d78111f'),
(4, 'denise punzalan', 'CCS@gmail.com', '$2y$10$qc8rHQ2Q/nOTeFIMzi.K1.d47h7BYyWxUlCc.G4PSHoFo5Tm/Un6K', 'admin_677d2c26af3c3'),
(5, 'admintest', 'testing@gmail.com', '$2y$10$o4/6uPph.ikcSHZ2c1tLfuIh1uLfm7W7PwKtez4vMpxQ5AFuDOkDe', 'admin_6842ed3f12442');

-- --------------------------------------------------------

--
-- Table structure for table `hoa_users`
--

CREATE TABLE `hoa_users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hoa_users`
--

INSERT INTO `hoa_users` (`user_id`, `username`, `email`, `password`) VALUES
(1, 'rei@gmail.com', '123456789', '$2y$10$tPDcvqLJdwx5JFcCIylyUeCPYxyQFIO25qtfXQZnsPORyBuLnxiGK'),
(2, 'james', 'jameskes@gmail.com', '$2y$10$nK4DqaJIgTnycfv8fsEBS.fw/dgxnKXSMbHhKyHhXDvLxwCsAh0lC'),
(3, 'admin1', 'admin1@gmail.com', '$2y$10$ywfjhUXqjoBYZ4WgP/o.bO3EnOjfZfQ9eO8TdAcaSQxbcYoxfKUwe'),
(27, 'Min Yoon Gi', 'suga@gmail.com', '$2y$10$Fzh3CUA1Z.LPiM9HBWcid./iIUjg7ld5tbd0fk2u1.ZLno0CsTP4u'),
(28, 'jomarie del rosario', 'jomariedr@gmail.com', '$2y$10$U5DBKc1MPpLCJihm3sQobuMckwS/Z8/6XUT72rHWVoYezrOFHgbE.'),
(29, 'test1', 'testing@gmail.com', '$2y$10$ym/YowmXbSIo0XTyxNkGducSZp6tCuB7/toyUwaVwA4eW7h23M1yS'),
(30, 'test2', 'testing1@gmail.com', '$2y$10$iY3mrjtFjsoHN8TMouduKOGxM8vJiu71sASxj7TzbGAKmdAJ8Yrtu');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_user_id` int(11) DEFAULT NULL,
  `sender_admin_id` int(11) DEFAULT NULL,
  `sender_type` enum('user','vendor') NOT NULL,
  `recipient_user_id` int(11) DEFAULT NULL,
  `recipient_admin_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_user_id`, `sender_admin_id`, `sender_type`, `recipient_user_id`, `recipient_admin_id`, `message`, `created_at`) VALUES
(18, NULL, 5, '', 1, NULL, 'Hello!', '2025-01-03 08:50:31'),
(19, NULL, 5, '', 1, NULL, 'Hello!', '2025-01-03 08:50:50'),
(28, 2, NULL, 'user', NULL, 4, 'sadasfasd', '2025-01-05 16:01:37'),
(30, 3, NULL, 'user', NULL, 1, 'dvdsfbdfbdfgbsdbsfbsdbdsa', '2025-01-05 17:52:13'),
(32, 3, NULL, 'user', NULL, 2, 'anong ginagawa mo?', '2025-01-06 18:29:10'),
(33, 2, NULL, 'user', NULL, 2, 'deliver this message!', '2025-01-07 12:37:13'),
(34, 4, NULL, 'user', 1, NULL, 'asdfggh', '2025-01-07 12:39:40'),
(35, 2, NULL, 'user', NULL, 2, 'hello', '2025-01-08 15:32:10'),
(36, 4, NULL, 'user', NULL, 2, 'amgsend ka naman', '2025-01-08 15:32:35'),
(37, 2, NULL, 'user', NULL, 2, 'hilu', '2025-01-08 18:49:39'),
(38, NULL, 2, '', 2, NULL, 'babalik sayo to', '2025-01-08 18:50:19'),
(39, NULL, 2, '', 3, NULL, 'hi', '2025-02-13 09:04:07'),
(40, 3, NULL, 'user', NULL, 2, 'hello coach', '2025-04-06 16:38:51'),
(41, 3, NULL, 'user', 28, NULL, 'example', '2025-04-06 20:17:18');

-- --------------------------------------------------------

--
-- Table structure for table `routine_history`
--

CREATE TABLE `routine_history` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `routine` varchar(255) NOT NULL,
  `routine_intensity` enum('Low','Medium','High') NOT NULL,
  `time_of_submission` time NOT NULL,
  `date_of_submission` date NOT NULL,
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `task_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date_due` date DEFAULT NULL,
  `time_due` time DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`task_id`, `user_id`, `title`, `description`, `date_due`, `time_due`, `image`, `status`) VALUES
(1, NULL, '', NULL, NULL, NULL, NULL, 'Completed'),
(2, NULL, 'test1', 'test1', '2025-08-14', '03:19:00', 'uploads/tasks/task_689a4d65b6685_sdgdfg.PNG', 'Completed'),
(3, NULL, 'test 2', 'teset2', '2025-08-07', '16:13:00', NULL, 'In Progress'),
(4, NULL, 'test7', 'tesy6', '2025-08-06', '23:56:00', NULL, 'In Progress'),
(5, NULL, 'test8', 'test8', '2025-08-14', '04:08:00', NULL, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `tripping_request`
--

CREATE TABLE `tripping_request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `property_of_interest` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tripping_request`
--

INSERT INTO `tripping_request` (`id`, `user_id`, `name`, `email`, `phone`, `date`, `time`, `property_of_interest`, `created_at`, `status`) VALUES
(3, NULL, 'Jomarie M. Del Rosario', 'jojo@gmail.com', '09566925077', '2024-12-24', '12:00:00', 'I want to know more abouth this house.', '2024-12-11 16:37:34', 'Pending'),
(5, NULL, 'yoru', 'yoru@gmail.com', '345234', '2025-01-21', '15:29:00', 'gergergerg', '2025-01-05 07:27:32', 'Pending'),
(6, 4, 'jomarie del rosario', 'jomariedr@gmail.com', '09566925077', '2025-01-10', '00:28:00', 'Interest', '2025-01-08 16:24:15', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`) VALUES
(3, 'Min Yoon Gi', 'suga@gmail.com', '$2y$10$Fzh3CUA1Z.LPiM9HBWcid./iIUjg7ld5tbd0fk2u1.ZLno0CsTP4u'),
(4, 'jomarie del rosario', 'jomariedr@gmail.com', '$2y$10$U5DBKc1MPpLCJihm3sQobuMckwS/Z8/6XUT72rHWVoYezrOFHgbE.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `class_routines`
--
ALTER TABLE `class_routines`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `fk_class_routines_admin` (`admin_id`);

--
-- Indexes for table `codegen`
--
ALTER TABLE `codegen`
  ADD PRIMARY KEY (`code_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `fk_codegen_user` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hoa_admins`
--
ALTER TABLE `hoa_admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `hoa_users`
--
ALTER TABLE `hoa_users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_user_id` (`sender_user_id`),
  ADD KEY `sender_admin_id` (`sender_admin_id`),
  ADD KEY `recipient_user_id` (`recipient_user_id`),
  ADD KEY `recipient_admin_id` (`recipient_admin_id`);

--
-- Indexes for table `routine_history`
--
ALTER TABLE `routine_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tripping_request`
--
ALTER TABLE `tripping_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `class_routines`
--
ALTER TABLE `class_routines`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `codegen`
--
ALTER TABLE `codegen`
  MODIFY `code_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `hoa_admins`
--
ALTER TABLE `hoa_admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hoa_users`
--
ALTER TABLE `hoa_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `routine_history`
--
ALTER TABLE `routine_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tripping_request`
--
ALTER TABLE `tripping_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `class_routines`
--
ALTER TABLE `class_routines`
  ADD CONSTRAINT `fk_class_routines_admin` FOREIGN KEY (`admin_id`) REFERENCES `hoa_admins` (`admin_id`) ON DELETE CASCADE;

--
-- Constraints for table `codegen`
--
ALTER TABLE `codegen`
  ADD CONSTRAINT `codegen_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class_routines` (`class_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_codegen_user` FOREIGN KEY (`user_id`) REFERENCES `hoa_users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `routine_history`
--
ALTER TABLE `routine_history`
  ADD CONSTRAINT `routine_history_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class_routines` (`class_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `routine_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `hoa_users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `task_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `hoa_users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tripping_request`
--
ALTER TABLE `tripping_request`
  ADD CONSTRAINT `fk_tripping_request_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
