-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 09:39 AM
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
-- Database: `tapintime`
--

-- --------------------------------------------------------

--
-- Table structure for table `archived_students`
--

CREATE TABLE `archived_students` (
  `id` int(11) NOT NULL,
  `rfid` varchar(50) DEFAULT NULL,
  `lrn` varchar(20) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `section` varchar(50) NOT NULL,
  `student_type` varchar(50) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `citizenship` varchar(50) DEFAULT NULL,
  `elementary_school` varchar(150) DEFAULT NULL,
  `year_graduated` varchar(10) DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `guardian_contact` varchar(20) DEFAULT NULL,
  `guardian_address` text DEFAULT NULL,
  `guardian_relationship` varchar(50) DEFAULT NULL,
  `birth_certificate` varchar(255) DEFAULT NULL,
  `id_photo` varchar(255) DEFAULT NULL,
  `good_moral` varchar(255) DEFAULT NULL,
  `student_signature` varchar(255) DEFAULT NULL,
  `grade_level` varchar(10) DEFAULT NULL,
  `school_year` varchar(20) DEFAULT NULL,
  `date_archived` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assign`
--

CREATE TABLE `assign` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `grade_level` int(11) NOT NULL,
  `section` varchar(100) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `student_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assign`
--

INSERT INTO `assign` (`id`, `teacher_id`, `subject_id`, `grade_level`, `section`, `assigned_at`, `created_at`, `student_type`) VALUES
(61, 1, 14, 7, 'Mangga', '2025-06-17 07:27:23', '2025-06-17 07:27:23', 'Regular Student');

-- --------------------------------------------------------

--
-- Table structure for table `assigned_grade_subjects`
--

CREATE TABLE `assigned_grade_subjects` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `student_type` varchar(10) NOT NULL,
  `grade_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assigned_subjects`
--

CREATE TABLE `assigned_subjects` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `student_type` enum('Regular','STI','Both') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(11) NOT NULL,
  `teacher_id` varchar(6) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `dob` date NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `teacher_id`, `name`, `email`, `contact`, `dob`, `password`) VALUES
(1, '252480', 'Kha', 'jaylinfernandez03@gmail.com', '123456782', '2025-04-12', '$2y$10$SsufId.xA5SNjxL.4IIFnOzDo5ybaY7loQdu/YAhXX2h7IpS1VnSe'),
(2, '251413', 'Mar', 'marvita.2003123@gmail.com', '09789643345', '2025-06-10', '$2y$10$tEZXLva9ZivQN1RGPIWa7.Cpp2nN7vaHYzZ5TBflQqzefFYVDn7lu'),
(3, '257457', 'Yadan', 'ataggatanel2004@outlook.com', '09789643345', '2025-06-05', '$2y$10$Yhz9BtljjxeNaspC0PcFfe.7d6WMfd9nOZkoQGELhZCkZKL.js4QW');

-- --------------------------------------------------------

--
-- Table structure for table `pending_students`
--

CREATE TABLE `pending_students` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `lrn` varchar(12) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `citizenship` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `section` varchar(50) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `grade_level` varchar(20) DEFAULT NULL,
  `student_type` varchar(20) NOT NULL,
  `guardian_name` varchar(100) NOT NULL,
  `guardian_contact` varchar(15) NOT NULL,
  `guardian_address` varchar(250) NOT NULL,
  `guardian_relationship` varchar(50) NOT NULL,
  `elementary_school` varchar(100) NOT NULL,
  `year_graduated` year(4) NOT NULL,
  `birth_certificate` varchar(255) DEFAULT NULL,
  `id_photo` varchar(255) DEFAULT NULL,
  `good_moral` varchar(255) DEFAULT NULL,
  `student_signature` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_years`
--

CREATE TABLE `school_years` (
  `id` int(11) NOT NULL,
  `year` varchar(20) DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `lrn` varchar(12) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `citizenship` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `guardian_name` varchar(100) NOT NULL,
  `guardian_contact` varchar(15) NOT NULL,
  `guardian_address` varchar(250) NOT NULL,
  `guardian_relationship` varchar(50) NOT NULL,
  `elementary_school` varchar(100) NOT NULL,
  `year_graduated` year(4) NOT NULL,
  `birth_certificate` varchar(255) NOT NULL,
  `id_photo` varchar(255) NOT NULL,
  `good_moral` varchar(255) DEFAULT NULL,
  `student_signature` varchar(255) NOT NULL,
  `section` varchar(50) DEFAULT NULL,
  `school_year` varchar(9) NOT NULL,
  `grade_level` varchar(20) DEFAULT NULL,
  `student_type` enum('Regular Student','STI Student') DEFAULT NULL,
  `verified_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rfid` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `first_name`, `middle_name`, `last_name`, `lrn`, `date_of_birth`, `gender`, `citizenship`, `address`, `contact_number`, `email`, `guardian_name`, `guardian_contact`, `guardian_address`, `guardian_relationship`, `elementary_school`, `year_graduated`, `birth_certificate`, `id_photo`, `good_moral`, `student_signature`, `section`, `school_year`, `grade_level`, `student_type`, `verified_at`, `created_at`, `rfid`) VALUES
(116, 'Marvita', 'Mallillin', 'Yadan', '102222222221', '2025-06-06', 'Female', 'Filipino', 'Purok2, Narra Street, Casibarag Sur, Cabagan, Isabela', '09111111111', 'marvita.2003@gmail.com', 'Jasmine Cataggatan', '09111111111', 'Purok2, Narra Street, Casibarag Sur, Cabagan, Isabela', 'Mother', 'CSES', '2016', 'uploads/685118294a77a_birth_certificate.jpg', 'uploads/685118294b71d_id_photo.jpg', 'uploads/685118294c033_good_moral.png', 'uploads/685118294c868_student_signature.jpg', 'Mangga', '2024-2028', 'Grade 7', 'Regular Student', '2025-06-17 07:24:44', '2025-06-17 07:24:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `student_type` enum('Regular Student','STI Student','Both') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `student_type`, `created_at`) VALUES
(8, 'Math', 'Both', '2025-06-11 00:06:42'),
(10, 'Makabansa', 'Both', '2025-06-12 08:59:03'),
(12, 'English', 'Regular Student', '2025-06-13 01:48:11'),
(13, 'Mapeh', 'STI Student', '2025-06-13 01:52:57'),
(14, 'P.e', 'Regular Student', '2025-06-13 03:37:39'),
(15, 'PP', 'STI Student', '2025-06-13 03:40:23');

-- --------------------------------------------------------

--
-- Table structure for table `subject_teacher`
--

CREATE TABLE `subject_teacher` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','counselor') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$EE3TzR1MXADx7I4VOKXJMuuFkTI9e0Sgfhci5HddC.CHj8MdB/8ca', 'superadmin', '2025-02-20 15:02:38'),
(2, 'jaylin', '$2y$10$dT6ASoT6angT5yRkPEdDc.YhtwXT8C82AxTrpkq5JvoZogwenC/LW', 'counselor', '2025-04-17 03:16:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archived_students`
--
ALTER TABLE `archived_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assign`
--
ALTER TABLE `assign`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `pending_students`
--
ALTER TABLE `pending_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lrn` (`lrn`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lrn` (`lrn`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `rfid` (`rfid`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `archived_students`
--
ALTER TABLE `archived_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `assign`
--
ALTER TABLE `assign`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pending_students`
--
ALTER TABLE `pending_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assign`
--
ALTER TABLE `assign`
  ADD CONSTRAINT `assign_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `faculty` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assign_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
