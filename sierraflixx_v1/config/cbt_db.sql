-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2022 at 11:04 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cbt_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` varchar(32) NOT NULL,
  `question_id` varchar(32) NOT NULL,
  `answer` text NOT NULL,
  `option_letter` char(1) NOT NULL,
  `is_correct` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `answer`, `option_letter`, `is_correct`) VALUES
('369fdab47c2b01A03BC500284600C03A', '369fbb43cfea8E80828E61498E84E224', 'Describes aactions', 'A', 'false'),
('369fec5ba321CFBD0A5F53D4158367B3', '369fb9f4efbe7B424E6B2FCFD5C77E9A', 'Describes actions', 'A', 'true');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` varchar(32) NOT NULL,
  `title` text NOT NULL,
  `duration` int(11) NOT NULL,
  `description` text NOT NULL,
  `year` year(4) NOT NULL,
  `start_date` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `title`, `duration`, `description`, `year`, `start_date`) VALUES
('369a5b24afb30DED7402553A26F6408B', 'Sample Exam', 30, 'Sample', 2021, 1649667240000);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `action` text NOT NULL,
  `action_date` date NOT NULL,
  `action_time` time NOT NULL,
  `user_id` varchar(32) NOT NULL,
  `action_ip` varchar(20) NOT NULL,
  `user_type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `action`, `action_date`, `action_time`, `user_id`, `action_ip`, `user_type`) VALUES
(1, 'successfully login to system', '2022-11-08', '10:11:53', '', '::1', 'user'),
(2, 'delete Result', '2022-11-08', '10:11:04', '369a34267cb4F0F232D4B4E80CC8F25D', '::1', 'developer');

-- --------------------------------------------------------

--
-- Table structure for table `privileges`
--

CREATE TABLE `privileges` (
  `id` varchar(32) NOT NULL,
  `role` varchar(20) NOT NULL,
  `endpoint` varchar(32) NOT NULL,
  `can_read` varchar(8) NOT NULL,
  `can_create` varchar(8) NOT NULL,
  `can_update` varchar(8) NOT NULL,
  `can_delete` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `privileges`
--

INSERT INTO `privileges` (`id`, `role`, `endpoint`, `can_read`, `can_create`, `can_update`, `can_delete`) VALUES
('369a31745640E5137108BCF3C9C525D7', 'developer', 'privileges', 'true', 'true', 'true', 'true'),
('369a325241b13C4277C205D02AF84E8A', 'developer', 'users', 'true', 'true', 'true', 'true'),
('369a585d02885635E027301B49B9168D', 'developer', 'students', 'true', 'true', 'true', 'true'),
('369a5a3836d7CA6383308A06D7BB3F72', 'developer', 'exams', 'true', 'true', 'true', 'true'),
('369a6379e841934BD29B6E5ACC35E347', 'developer', 'programs', 'true', 'true', 'true', 'true'),
('369faf2ebebe42D868AA29A293CD343E', 'developer', 'questions', 'true', 'true', 'true', 'true'),
('369fafcc0e2217414EB05DA597F3825A', 'developer', 'answers', 'true', 'true', 'true', 'true'),
('36a030673c7967A9C2A7F0E5060C744E', 'developer', 'results', 'true', 'false', 'false', 'true'),
('36a031d05c9dA1A71CAE0E88A72BEBE8', 'student', 'results', 'true', 'true', 'false', 'false'),
('36a0cdc946b06ABC821D4C05DB292191', 'student', 'questions', 'true', 'false', 'false', 'false'),
('36a0ceb1ecf4A1B0A5F6E61DD02E86BA', 'student', 'exams', 'true', 'false', 'false', 'false'),
('36a0cf845667B9358A8674E32561972B', 'student', 'answers', 'true', 'false', 'false', 'false');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` varchar(32) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `title`, `description`) VALUES
('369a644bd17b8179F08E4C9257F7B5DE', 'BSc Computer Science', '');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` varchar(32) NOT NULL,
  `question` text NOT NULL,
  `score` decimal(10,2) NOT NULL,
  `serial` int(11) NOT NULL,
  `exam_id` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `question`, `score`, `serial`, `exam_id`) VALUES
('369fb9f4efbe7B424E6B2FCFD5C77E9A', 'Which of the following is correct about verbs?', '25.00', 2, '369a5b24afb30DED7402553A26F6408B'),
('369fbb43cfea8E80828E61498E84E224', 'Which of the following is correct about nouns?', '1.00', 1, '369a5b24afb30DED7402553A26F6408B');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` varchar(32) NOT NULL,
  `exam_id` varchar(32) NOT NULL,
  `answer_id` varchar(32) NOT NULL,
  `question_id` varchar(32) NOT NULL,
  `student_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) NOT NULL,
  `name` varchar(64) NOT NULL,
  `exam_id` varchar(32) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'student',
  `wassce_year` year(4) NOT NULL,
  `wassce_number` bigint(20) NOT NULL,
  `program_id` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `exam_id`, `role`, `wassce_year`, `wassce_number`, `program_id`) VALUES
(35313931393931, 'Sample Student', '369a5b24afb30DED7402553A26F6408B', 'student', 2021, 6021202006, '369a644bd17b8179F08E4C9257F7B5DE'),
(38373237383537, 'Student', '369a5b24afb30DED7402553A26F6408B', 'student', 2020, 6021202006, '369a644bd17b8179F08E4C9257F7B5DE');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(32) NOT NULL,
  `name` varchar(50) NOT NULL,
  `uid` varchar(20) NOT NULL,
  `role` varchar(20) NOT NULL,
  `secret` varchar(125) NOT NULL,
  `is_active` varchar(8) NOT NULL DEFAULT 'true'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `uid`, `role`, `secret`, `is_active`) VALUES
('369a34267cb4F0F232D4B4E80CC8F25D', 'Super Developer', 'admin', 'developer', '$2y$10$C/maa0K7uqEqtXEIXUBFOuvttPcBFWjjNvCpxAa7zpbVnJJUJPiye', 'true');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `question_id` (`question_id`,`option_letter`),
  ADD UNIQUE KEY `question_id_2` (`question_id`,`answer`) USING HASH;

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`,`year`) USING HASH;

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `privileges`
--
ALTER TABLE `privileges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role` (`role`,`endpoint`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`) USING HASH;

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial` (`serial`,`exam_id`),
  ADD UNIQUE KEY `question` (`question`,`exam_id`) USING HASH,
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `answer_id` (`answer_id`,`exam_id`,`question_id`,`student_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_id` (`exam_id`,`wassce_year`,`wassce_number`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `results_ibfk_3` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `results_ibfk_4` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
