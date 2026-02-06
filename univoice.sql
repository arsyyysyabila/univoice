-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2026 at 05:38 AM
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
-- Database: `univoice`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `phone`, `password`, `role`, `created_at`, `last_login`) VALUES
('ADMIN001', 'Administrator Utama', 'admin@uitm.edu.my', '0123456789', 'admin123', 'super_admin', '2026-01-19 03:17:50', '2026-01-19 04:33:55'),
('ADMIN002', 'Admin UiTM Kelantan', 'kelantan.admin@uitm.edu.my', '0198765432', 'uitm2024', 'admin', '2026-01-19 03:17:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaint_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `issue` varchar(100) NOT NULL,
  `department_id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `campus` varchar(50) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status_id` int(11) NOT NULL,
  `submit_at` date NOT NULL
) ;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`complaint_id`, `student_id`, `staff_id`, `issue`, `department_id`, `description`, `campus`, `attachment`, `status_id`, `submit_at`) VALUES
(7, 2025516791, NULL, 'bdhbbeu', 4, 'uguwdgkusbkb', NULL, NULL, 2, '2026-01-13'),
(8, 2025516791, NULL, 'gcthcvhb', 5, 'bdsbdskui', 'Kota Bharu', 'UNI_1768302057_1204.png', 1, '2026-01-13'),
(9, 2025597133, NULL, 'takde adab', 6, 'salah satu staff takde adab', 'Machang', NULL, 1, '2026-01-19'),
(10, 2025597133, NULL, 'staff tak profesional', 2, 'staff hea dengan student pun nak gaduh', 'Machang', NULL, 1, '2026-01-19'),
(11, 2025597133, NULL, 'fasiliti tak menjadi', 1, 'ac, tv dan kipas tak menjadi di Blok D, D315', 'Machang', NULL, 1, '2026-01-19'),
(12, 2025597133, NULL, 'kertas kerja', 2, 'kenapa hep lulus bajet sikit', 'Machang', NULL, 1, '2026-01-19'),
(13, 2025597133, NULL, 'kertas kerja lulus tak adil', 2, 'kertas kerja kelab xxx ni lulus RM5000 untuk program team building, giler takkan hep tak check dulu ', 'Machang', NULL, 1, '2026-01-19'),
(14, NULL, 1234, 'kereta staff disaman', 6, 'kereta staff pun disaman pb takde access ke data lecture, saya dah ada sticker tau', 'Machang', NULL, 1, '2026-01-19'),
(15, NULL, 1234, 'kereta staff disaman', 6, 'pb sama kereta staff, takkan data dalam sistem pb tak check and terang terang saya letak sticker. mo', 'Machang', NULL, 1, '2026-01-19');

-- --------------------------------------------------------

--
-- Table structure for table `complaints_status`
--

CREATE TABLE `complaints_status` (
  `status_id` int(11) NOT NULL,
  `status_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints_status`
--

INSERT INTO `complaints_status` (`status_id`, `status_name`) VALUES
(1, 'Pending'),
(2, 'In Progress'),
(3, 'Resolved');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`department_id`, `department_name`) VALUES
(1, 'Facilities & Infrastructure (BPF)'),
(2, 'Student Affairs (HEP)'),
(3, 'Library (PTAR)'),
(4, 'Academic Affairs (BHEA)'),
(5, 'Health Unit'),
(6, 'Auxiliary Police / Security');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `reset_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expired_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `campus` varchar(50) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `confirm_password` varchar(100) NOT NULL,
  `attachment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `name`, `email`, `campus`, `password`, `confirm_password`, `attachment`) VALUES
(1234, 'Isya', '1234@staff.uitm.edu.my', 'Machang', '123', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `campus` varchar(50) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `confirm_password` varchar(100) NOT NULL,
  `created_at` date NOT NULL,
  `attachment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `name`, `email`, `campus`, `password`, `confirm_password`, `created_at`, `attachment`) VALUES
(2025516791, 'MARSYA NAJIHA SYABILA BINTI MOHD NIZAM', '2025516791@student.uitm.edu.my', 'Machang', 'Syasya123', '', '0000-00-00', ''),
(2025597133, 'Fatin Audryna', '2025597133@student.uitm.edu.my', 'Machang', 'fatin123', '', '0000-00-00', '');

-- --------------------------------------------------------

--
-- Table structure for table `survey`
--

CREATE TABLE `survey` (
  `survey_id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `feedback` varchar(100) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaint_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `complaints_status`
--
ALTER TABLE `complaints_status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`reset_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `survey`
--
ALTER TABLE `survey`
  ADD PRIMARY KEY (`survey_id`),
  ADD KEY `complaint_id` (`complaint_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaints_status`
--
ALTER TABLE `complaints_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1235;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2025597134;

--
-- AUTO_INCREMENT for table `survey`
--
ALTER TABLE `survey`
  MODIFY `survey_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`),
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `complaints_status` (`status_id`),
  ADD CONSTRAINT `complaints_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `complaints_ibfk_4` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `survey`
--
ALTER TABLE `survey`
  ADD CONSTRAINT `survey_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`complaint_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
