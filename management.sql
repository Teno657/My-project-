-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2025 at 10:03 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.2.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `management`
--

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `message`, `is_read`, `created_at`) VALUES
(38, 'Nouvelle inscription de stagiaire : magic johnson\nEmail : magic@icloud.com\nInscrit le : 24/07/2025 22:02', 1, '2025-07-24 22:02:12'),
(39, 'Nouvelle inscription de stagiaire : flyod meweather\nEmail : floyd@icloud.com\nInscrit le : 24/07/2025 22:04', 1, '2025-07-24 22:04:03'),
(40, 'Nouvelle inscription de stagiaire : verline james\nEmail : verline@gmail.com\nInscrit le : 07/10/2025 08:38', 0, '2025-10-07 08:38:44');

-- --------------------------------------------------------

--
-- Table structure for table `task_admin`
--

CREATE TABLE `task_admin` (
  `admin_id` int(11) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `task_admin`
--

INSERT INTO `task_admin` (`admin_id`, `admin_email`, `admin_password`) VALUES
(1, 'hermanjoris@icloud.com', '$2y$10$nyVSJW5pzYoUFc5WAddjo.rCpmQYpWpvldoVX0fprvWOrVyF/.Z9G');

-- --------------------------------------------------------

--
-- Table structure for table `task_comments`
--

CREATE TABLE `task_comments` (
  `comment_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `comment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `task_comments`
--

INSERT INTO `task_comments` (`comment_id`, `task_id`, `user_id`, `comment_text`, `comment_date`) VALUES
(19, 78, 75, 'oui', '2025-07-24 22:09:18'),
(20, 78, 75, 'oui', '2025-07-24 22:23:00'),
(21, 78, 75, 'ok', '2025-07-24 22:46:13'),
(22, 78, 75, 'finis', '2025-07-24 23:02:30'),
(23, 78, 75, 'accomplie', '2025-07-24 23:04:48'),
(24, 79, 74, 'dakkk', '2025-10-08 02:34:27');

-- --------------------------------------------------------

--
-- Table structure for table `task_comment_replies`
--

CREATE TABLE `task_comment_replies` (
  `reply_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reply_text` text NOT NULL,
  `reply_date` datetime NOT NULL DEFAULT current_timestamp(),
  `reply_datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `task_comment_replies`
--

INSERT INTO `task_comment_replies` (`reply_id`, `comment_id`, `user_id`, `reply_text`, `reply_date`, `reply_datetime`) VALUES
(64, 22, 65, 'ok', '2025-07-24 23:03:14', '2025-07-24 23:03:14'),
(65, 23, 65, 'bien recu', '2025-07-25 09:10:34', '2025-07-25 09:10:34'),
(66, 20, 65, 'compris', '2025-07-25 09:11:33', '2025-07-25 09:11:33');

-- --------------------------------------------------------

--
-- Table structure for table `task_department`
--

CREATE TABLE `task_department` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `department_status` enum('Enable','Disable') DEFAULT NULL,
  `department_added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `department_updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `task_department`
--

INSERT INTO `task_department` (`department_id`, `department_name`, `department_status`, `department_added_on`, `department_updated_on`) VALUES
(1, 'informatique', 'Enable', '2025-07-07 05:34:37', '2025-07-07 07:10:59'),
(3, 'anglais', 'Enable', '2025-07-07 07:09:58', '2025-07-07 07:09:58');

-- --------------------------------------------------------

--
-- Table structure for table `task_manage`
--

CREATE TABLE `task_manage` (
  `task_id` int(11) NOT NULL,
  `task_title` varchar(255) NOT NULL,
  `task_creator_description` text DEFAULT NULL,
  `task_completion_description` text DEFAULT NULL,
  `task_department_id` int(11) NOT NULL,
  `task_user_to` int(11) NOT NULL,
  `task_assign_date` date DEFAULT NULL,
  `task_end_date` date DEFAULT NULL,
  `task_status` enum('Pending','Viewed','In Progress','Completed','Delayed') DEFAULT NULL,
  `task_added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `task_updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `task_manage`
--

INSERT INTO `task_manage` (`task_id`, `task_title`, `task_creator_description`, `task_completion_description`, `task_department_id`, `task_user_to`, `task_assign_date`, `task_end_date`, `task_status`, `task_added_on`, `task_updated_on`) VALUES
(78, 'corriger les formules des clients', '<p>fais le bien bien et tu seras promu donc travailles bien</p>', 'oui\n\noui\n\nok\n\nfinis\n\naccomplie', 3, 75, '2025-07-24', '2025-07-26', 'Completed', '2025-07-24 21:06:17', '2025-07-24 22:04:48'),
(79, 'corriger les formules des clients', '<p>qqqqqqqqqqqqqqq</p>', 'dakkk', 1, 74, '2025-10-08', '2025-10-09', 'In Progress', '2025-10-08 01:33:44', '2025-10-08 01:34:27');

-- --------------------------------------------------------

--
-- Table structure for table `task_user`
--

CREATE TABLE `task_user` (
  `user_id` int(11) NOT NULL,
  `user_first_name` varchar(255) NOT NULL,
  `user_last_name` varchar(255) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `user_email_address` varchar(255) NOT NULL,
  `user_email_password` varchar(255) NOT NULL,
  `user_contact_no` varchar(20) DEFAULT NULL,
  `user_date_of_birth` date DEFAULT NULL,
  `user_gender` enum('Male','Female','Other') DEFAULT NULL,
  `user_address` text DEFAULT NULL,
  `user_status` enum('Enable','Disable') DEFAULT NULL,
  `user_image` varchar(255) DEFAULT NULL,
  `user_added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cv_path` varchar(255) DEFAULT NULL,
  `motivation_letter_path` varchar(255) DEFAULT NULL,
  `identity_doc_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `task_user`
--

INSERT INTO `task_user` (`user_id`, `user_first_name`, `user_last_name`, `department_id`, `user_email_address`, `user_email_password`, `user_contact_no`, `user_date_of_birth`, `user_gender`, `user_address`, `user_status`, `user_image`, `user_added_on`, `user_updated_on`, `cv_path`, `motivation_letter_path`, `identity_doc_path`) VALUES
(65, 'administrateur', '.', 1, 'hermanjoris@icloud.com', '$2y$10$HM46D4biwRgZvdSEi1dzx.fx.aI2E5ijndMnGeXsPfISwRG/ucEl.', '621619283', '2004-10-23', 'Male', 'yaounde23', 'Enable', 'uploads/user_6880b3c7e488b2.82808623.jpg', '2025-07-23 10:04:55', '2025-07-23 19:18:18', 'uploads/docs/cv_6880b3c7e48a79.04322517.pdf', 'uploads/docs/motivation_6880b3c7e48aa9.48341363.pdf', 'uploads/docs/identity_6880b3c7e48b52.39259175.jpg'),
(74, 'magic', 'johnson', 1, 'magic@icloud.com', '$2y$10$MsECHgnXa9zOcweHo7rWOubwN1ObG1Si9onsZ.3Yukz7mwzDyvM3m', '65543567', '1990-10-22', 'Male', 'LA 22', 'Enable', 'uploads/user_68829f5467d947.94581287.jpg', '2025-07-24 21:02:12', '2025-07-24 21:05:28', 'uploads/docs/cv_68829f5467db14.50845147.pdf', 'uploads/docs/motivation_68829f5467db58.70832809.pdf', 'uploads/docs/identity_68829f5467dc16.74326392.jpg'),
(75, 'flyod', 'meweather', 3, 'floyd@icloud.com', '$2y$10$uygI83EODUrqaaAYEaC2/eW.VNBCuICvl.EEo4dgdRRNeWQdMte1u', '678946348', '1990-10-22', 'Male', 'LA 28', 'Enable', 'uploads/user_68829fc37639f8.36695413.jpg', '2025-07-24 21:04:03', '2025-07-24 21:05:33', 'uploads/docs/cv_68829fc3763bd0.18607513.pdf', 'uploads/docs/motivation_68829fc3763bf2.23714981.pdf', 'uploads/docs/identity_68829fc3763c56.57340658.pdf'),
(76, 'verline', 'james', 3, 'verline@gmail.com', '$2y$10$Lxn9pBqZU0JkUt4P.oobOuqadHLlTbDGs07aNZo6c0PmZeDVoY6Bq', '675698442', '2007-11-22', 'Female', 'yde38', 'Disable', 'uploads/user_68e4c383b7a8e0.23587622.png', '2025-10-07 07:38:43', '2025-10-07 07:38:43', 'uploads/docs/cv_68e4c383b7ae19.29411485.pdf', 'uploads/docs/motivation_68e4c383b7ae52.90957044.pdf', 'uploads/docs/identity_68e4c383b7aec5.25414123.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_notifications`
--

INSERT INTO `user_notifications` (`id`, `user_id`, `message`, `link`, `is_read`, `created_at`) VALUES
(58, 75, 'Nouvelle tâche assignée : corriger les formules des clients', 'view_task.php?id=78', 0, '2025-07-24 22:06:17'),
(61, 75, 'La tâche <span style=\'color:#0d6efd; font-weight:bold;\'>corriger les formules des clients</span> a été modifiée par l’administrateur le 24/07/2025 23:01', 'view_task.php?id=78', 0, '2025-07-24 23:01:40'),
(62, 75, 'La tâche <span style=\'color:#0d6efd; font-weight:bold;\'>corriger les formules des clients</span> a été modifiée par l’administrateur le 24/07/2025 23:03', 'view_task.php?id=78', 0, '2025-07-24 23:03:50'),
(63, 74, 'Nouvelle tâche assignée : corriger les formules des clients', 'view_task.php?id=79', 0, '2025-10-08 02:33:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_admin`
--
ALTER TABLE `task_admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `task_comments`
--
ALTER TABLE `task_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `task_comment_replies`
--
ALTER TABLE `task_comment_replies`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `task_department`
--
ALTER TABLE `task_department`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `task_manage`
--
ALTER TABLE `task_manage`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `task_department_id` (`task_department_id`),
  ADD KEY `task_user_to` (`task_user_to`);

--
-- Indexes for table `task_user`
--
ALTER TABLE `task_user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `task_admin`
--
ALTER TABLE `task_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `task_comments`
--
ALTER TABLE `task_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `task_comment_replies`
--
ALTER TABLE `task_comment_replies`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `task_department`
--
ALTER TABLE `task_department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `task_manage`
--
ALTER TABLE `task_manage`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `task_user`
--
ALTER TABLE `task_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `task_comments`
--
ALTER TABLE `task_comments`
  ADD CONSTRAINT `task_comments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `task_manage` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `task_user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `task_comment_replies`
--
ALTER TABLE `task_comment_replies`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `task_user` (`user_id`),
  ADD CONSTRAINT `task_comment_replies_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `task_comments` (`comment_id`) ON DELETE CASCADE;

--
-- Constraints for table `task_manage`
--
ALTER TABLE `task_manage`
  ADD CONSTRAINT `task_manage_ibfk_1` FOREIGN KEY (`task_department_id`) REFERENCES `task_department` (`department_id`),
  ADD CONSTRAINT `task_manage_ibfk_2` FOREIGN KEY (`task_user_to`) REFERENCES `task_user` (`user_id`);

--
-- Constraints for table `task_user`
--
ALTER TABLE `task_user`
  ADD CONSTRAINT `task_user_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `task_department` (`department_id`);

--
-- Constraints for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `user_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `task_user` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
