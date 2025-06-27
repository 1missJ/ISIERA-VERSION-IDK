-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2025 at 11:16 AM
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
-- Database: `tapintime`
--

-- --------------------------------------------------------

--
-- Table structure for table `approved_students_mobile`
--

CREATE TABLE `approved_students_mobile` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `section` varchar(100) DEFAULT NULL,
  `grade_level` varchar(50) DEFAULT NULL,
  `student_type` varchar(50) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approved_students_mobile`
--

INSERT INTO `approved_students_mobile` (`id`, `name`, `section`, `grade_level`, `student_type`, `student_id`) VALUES
(0, 'Yadan, Marvita', 'Mangga', 'Grade 7', 'Regular Student', 116),
(117, NULL, 'Mangga', 'Grade 7', 'Regular Student', NULL);

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
(4, '250511', 'Karen Soriano', 'Haa@gmail.com', '12345678123', '2025-06-25', '$2y$10$basnU64.B8j4uazWaYQumOl4ug9J.J/VkP1SIcogjQ/5GgABo63KC'),
(5, '251320', 'Jologska', 'jaylinfernandez03@gmail.com', '12345678123', '2025-06-13', '$2y$10$5DAJVFz6VrJwyjqjXrMVjOS6lhYcEO/Ku8KcAdTqV7oYWcVwV2DK6');

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

--
-- Dumping data for table `pending_students`
--

INSERT INTO `pending_students` (`id`, `first_name`, `middle_name`, `last_name`, `lrn`, `date_of_birth`, `gender`, `citizenship`, `address`, `contact_number`, `email`, `section`, `school_year`, `grade_level`, `student_type`, `guardian_name`, `guardian_contact`, `guardian_address`, `guardian_relationship`, `elementary_school`, `year_graduated`, `birth_certificate`, `id_photo`, `good_moral`, `student_signature`, `created_at`) VALUES
(91, 'Marvita', 'M', 'Yadan', '123456789123', '2025-06-02', 'Female', 'Filipino', 'Pilig Alto Cabagan Isabela', '12345678912', 'Hapon@gmail.com', 'Mangga', '2025-2027', 'Grade 9', 'JHS', 'Jaylin Fernandez', '12345678', 'Pilig Alto Cabagan Isabela', 'Mother', 'Catabayungan', '2019', 'uploads/685e1a7e9205c_birth_certificate.png', 'uploads/685e1a7e923c2_id_photo.png', 'uploads/685e1a7e9261a_good_moral.png', 'uploads/685e1a7e927e6_student_signature.png', '2025-06-26 22:13:50');

-- --------------------------------------------------------

--
-- Table structure for table `principal`
--

CREATE TABLE `principal` (
  `id` int(11) NOT NULL,
  `principal_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `principal`
--

INSERT INTO `principal` (`id`, `principal_name`) VALUES
(1, 'Orlyn Demerin');

-- --------------------------------------------------------

--
-- Table structure for table `rejected_students`
--

CREATE TABLE `rejected_students` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `section` varchar(100) DEFAULT NULL,
  `grade_level` varchar(50) DEFAULT NULL,
  `student_type` varchar(50) DEFAULT NULL
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

--
-- Dumping data for table `school_years`
--

INSERT INTO `school_years` (`id`, `year`, `is_current`) VALUES
(0, '2025-2026', NULL),
(0, '2025-2027', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `student_type` enum('JHS','SHS') NOT NULL,
  `grade_level` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `section_name`, `student_type`, `grade_level`, `created_at`) VALUES
(4, 'Nakopo', 'JHS', 'Grade 10', '2025-06-26 10:33:07'),
(5, 'Mangga', 'SHS', 'Grade 9', '2025-06-26 12:24:34'),
(6, 'Rizal', 'SHS', 'Grade 11', '2025-06-27 05:43:32');

-- --------------------------------------------------------

--
-- Table structure for table `section_advisers`
--

CREATE TABLE `section_advisers` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section_advisers`
--

INSERT INTO `section_advisers` (`id`, `teacher_id`, `section_id`, `created_at`) VALUES
(6, 4, 6, '2025-06-27 05:44:36'),
(8, 5, 4, '2025-06-27 06:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `strands`
--

CREATE TABLE `strands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `strands`
--

INSERT INTO `strands` (`id`, `name`) VALUES
(3, 'ABM'),
(4, 'HUMMS');

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
  `rfid` varchar(50) DEFAULT NULL,
  `mobile_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `first_name`, `middle_name`, `last_name`, `lrn`, `date_of_birth`, `gender`, `citizenship`, `address`, `contact_number`, `email`, `guardian_name`, `guardian_contact`, `guardian_address`, `guardian_relationship`, `elementary_school`, `year_graduated`, `birth_certificate`, `id_photo`, `good_moral`, `student_signature`, `section`, `school_year`, `grade_level`, `student_type`, `verified_at`, `created_at`, `rfid`, `mobile_verified`) VALUES
(118, 'Jaylin', 'D', 'Fernandez', '103109590012', '2025-06-28', 'Female', 'Filipino', 'Pilig Alto Cabagan Isabela', '12345678912', 'Ha@gmail.com', 'Patricia Anne D. Corpuz', '12345678', 'Pilig Alto Cabagan Isabela', 'Mother', 'Catabayungan', '2016', 'uploads/685b94bc9c3c7_birth_certificate.png', 'uploads/685b94bc9ccf6_id_photo.jpg', 'uploads/685b94bc9d0d8_good_moral.png', 'uploads/685b94bc9d675_student_signature.png', 'Mabini', '2025-2027', 'Grade 9', '', '2025-06-25 06:19:11', '2025-06-25 06:19:11', NULL, 0),
(119, 'Patricia', 'Anne D.', 'Corpuz', '103109590014', '2025-06-05', 'Male', 'Filipino', 'Pilig Alto Cabagan Isabela', '12345678912', 'corpuzpatriciaanee01@gmail.com', 'Patricia Anne D. Corpuz', '12345678', 'Pilig Alto Cabagan Isabela', 'Mother', 'Catabayungan', '2016', 'uploads/685d22c78b980_birth_certificate.jpg', 'uploads/685d22c78c478_id_photo.jpg', 'uploads/685d22c78d750_good_moral.jpg', 'uploads/685d22c78e2ba_student_signature.jpg', 'Nakopo', '2024-2025', 'Grade 10', '', '2025-06-26 10:37:23', '2025-06-26 10:37:23', NULL, 0),
(120, 'Joyce', 'D', 'Fernandez', '103109590011', '2025-06-01', 'Female', 'Filipino', 'Pilig Alto Cabagan Isabela', '12345678915', 'Halooo@gmail.com', 'Jaylin Fernandez', '12345678', 'Pilig Alto Cabagan Isabela', 'Mother', 'Catabayungan', '2016', 'uploads/685e1951ddbd5_birth_certificate.png', 'uploads/685e1951ddf87_id_photo.png', 'uploads/685e1951de241_good_moral.png', 'uploads/685e1951de4b9_student_signature.png', 'Nakopo', '2025-2026', 'Grade 10', '', '2025-06-27 04:18:53', '2025-06-27 04:18:53', NULL, 0),
(121, 'Elmarie', 'Anne D.', 'Corpuz', '123456789125', '2025-11-11', 'Male', 'Filipino', 'Pilig Alto Cabagan Isabela', '12345678915', 'Haklo@gmail.com', 'Jaylin Fernandez', '12345678', 'Pilig Alto Cabagan Isabela', 'Mother', 'Catabayungan', '2016', 'uploads/685e1d54eb68b_birth_certificate.png', 'uploads/685e1d54ebf8e_id_photo.png', 'uploads/685e1d54ec9ef_good_moral.png', 'uploads/685e1d54ed1f2_student_signature.png', 'Nakopo', '2025-2026', 'Grade 10', '', '2025-06-27 04:32:29', '2025-06-27 04:32:29', NULL, 0),
(122, 'SAna', 'G', 'UMANA', '123456789129', '2025-06-21', 'Female', 'Filipino', 'Pilig Alto Cabagan Isabela', '12345678912', 'Haaaaa@gmail.com', 'Jaylin Fernandez', '12345678', 'Pilig Alto Cabagan Isabela', 'Mother', 'Catabayungan', '2016', 'uploads/685e3fd9f1763_birth_certificate.png', 'uploads/685e3fd9f2905_id_photo.png', 'uploads/685e3fd9f2fac_good_moral.png', 'uploads/685e3fd9f33bf_student_signature.png', 'Rizal', '2025-2027', 'Grade 11', '', '2025-06-27 06:53:49', '2025-06-27 06:53:49', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `student_type` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `strand_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `student_type`, `created_at`, `strand_id`) VALUES
(48, 'Business Math', 'SHS', '2025-06-26 03:12:07', 3),
(49, 'Principles of Marketing', 'SHS', '2025-06-26 03:12:07', 3),
(53, 'Creative Nonfiction', 'SHS', '2025-06-26 03:12:26', 4),
(54, 'Disciplines and Ideas in Social Sciences', 'SHS', '2025-06-26 03:12:26', 4),
(55, 'Philippine Politics and Governance', 'SHS', '2025-06-26 03:12:26', 4),
(56, 'Introduction to World Religions', 'SHS', '2025-06-26 03:12:26', 4),
(57, 'Trends', 'SHS', '2025-06-26 03:12:26', 4),
(60, 'Reading and Writing', 'SHS', '2025-06-26 03:12:40', NULL),
(62, 'General Mathematics', 'SHS', '2025-06-26 03:12:40', NULL),
(63, '21st Century Literature from the Philippines and the World', 'SHS', '2025-06-26 03:12:40', NULL),
(64, 'Filipino', 'JHS', '2025-06-26 04:24:00', NULL),
(65, 'P.e', 'JHS', '2025-06-26 04:24:00', NULL),
(66, 'Science', 'JHS', '2025-06-26 12:24:12', NULL),
(67, 'English', 'JHS', '2025-06-26 12:24:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subject_grade_strand_assignments`
--

CREATE TABLE `subject_grade_strand_assignments` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `grade_level` varchar(10) NOT NULL,
  `strand_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject_grade_strand_assignments`
--

INSERT INTO `subject_grade_strand_assignments` (`id`, `subject_id`, `grade_level`, `strand_id`) VALUES
(3, 42, '11', 2),
(4, 43, '11', 2),
(1, 45, '11', 2),
(5, 46, '11', 2),
(2, 47, '11', 2),
(9, 48, '11', 3),
(15, 49, '11', 3),
(10, 50, '11', 3),
(7, 51, '11', 3),
(8, 52, '11', 3),
(18, 53, '11', 4),
(19, 54, '11', 4),
(13, 55, '11', 3),
(21, 55, '11', 4),
(20, 56, '11', 4),
(22, 57, '11', 4),
(11, 58, '11', 3),
(12, 59, '11', 3),
(14, 61, '11', 3),
(6, 63, '11', 3),
(17, 63, '11', 4),
(38, 64, '10', NULL),
(30, 64, '7', NULL),
(16, 64, '8', NULL),
(34, 64, '8', NULL),
(39, 65, '10', NULL),
(31, 65, '7', NULL),
(35, 65, '8', NULL),
(40, 66, '10', NULL),
(32, 66, '7', NULL),
(36, 66, '8', NULL),
(37, 67, '10', NULL),
(29, 67, '7', NULL),
(33, 67, '8', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subjects`
--

CREATE TABLE `teacher_subjects` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_subjects`
--

INSERT INTO `teacher_subjects` (`id`, `teacher_id`, `subject_id`, `section_id`, `created_at`) VALUES
(1, 5, 48, 4, '2025-06-26 12:53:57');

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
-- Indexes for table `approved_students_mobile`
--
ALTER TABLE `approved_students_mobile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `archived_students`
--
ALTER TABLE `archived_students`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `principal`
--
ALTER TABLE `principal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rejected_students`
--
ALTER TABLE `rejected_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_name` (`section_name`);

--
-- Indexes for table `section_advisers`
--
ALTER TABLE `section_advisers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_id` (`section_id`);

--
-- Indexes for table `strands`
--
ALTER TABLE `strands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_strand` (`strand_id`);

--
-- Indexes for table `subject_grade_strand_assignments`
--
ALTER TABLE `subject_grade_strand_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_id` (`subject_id`,`grade_level`,`strand_id`);

--
-- Indexes for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_id` (`teacher_id`,`subject_id`,`section_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pending_students`
--
ALTER TABLE `pending_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `section_advisers`
--
ALTER TABLE `section_advisers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `strands`
--
ALTER TABLE `strands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `subject_grade_strand_assignments`
--
ALTER TABLE `subject_grade_strand_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `fk_strand` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
