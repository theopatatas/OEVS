-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2025 at 09:48 PM
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
-- Database: `oevs_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidate`
--

CREATE TABLE `candidate` (
  `CandidateID` int(11) NOT NULL,
  `abc` varchar(1) NOT NULL,
  `Position` varchar(200) NOT NULL,
  `Party` varchar(100) NOT NULL,
  `FirstName` varchar(200) NOT NULL,
  `LastName` varchar(200) NOT NULL,
  `MiddleName` varchar(100) NOT NULL,
  `Gender` varchar(6) NOT NULL,
  `Year` varchar(100) NOT NULL,
  `Photo` varchar(200) NOT NULL,
  `Qualification` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `candidate`
--

INSERT INTO `candidate` (`CandidateID`, `abc`, `Position`, `Party`, `FirstName`, `LastName`, `MiddleName`, `Gender`, `Year`, `Photo`, `Qualification`) VALUES
(309, 's', 'Social-Media Officer', 'team 3', 'Cory', 'Nicholson', 'Nicholson', 'Male', '4th year', 'upload/1760300896_boy-candidate-icon-cartoon-employee-job-vector.jpg', ''),
(310, 'r', 'Representative', 'team 1', 'Van', 'Heath', 'Heath', 'Male', '1st year', 'upload/1760300921_boy-candidate-icon-cartoon-employee-job-vector.jpg', ''),
(311, 'r', 'Representative', 'Team 2', 'Aiden', 'Zamora', 'Zamora', 'Male', '4th year', 'upload/1760300959_boy-candidate-icon-cartoon-employee-job-vector.jpg', ''),
(312, 'r', 'Representative', '', 'Brock', 'Church', 'Church', 'Male', '4th year', 'upload/1760300983_boy-candidate-icon-cartoon-employee-job-vector.jpg', ''),
(313, 'r', 'Representative', 'team 3', 'Myles', 'Weeks', 'Weeks', 'Female', '4th year', 'upload/1760301037_girl-candidate-icon-cartoon-employee-talent-vector.jpg', ''),
(314, 's', 'Secretary', 'Team 2', 'Denmark', 'Morales', 'gulapa', 'Male', '4th year', 'upload/1760305764_boy-candidate-icon-cartoon-employee-job-vector.jpg', ''),
(315, 's', 'Social-Media Officer', 'Team 2', 'Denmark', 'Heath', 'Harding', 'Male', '4th year', 'upload/1760306818_boy-candidate-icon-cartoon-employee-job-vector.jpg', ''),
(283, 'p', 'President', 'team 3', 'Katie', 'Olson', 'Henson', 'Female', '4th year', 'upload/1760299888_girl-candidate-icon-cartoon-employee-talent-vector.jpg', 'I am currently enrolled as a full-time student and have maintained a GPA of at least 2.5, with no failing grades in the previous semester. I have an attendance record of over 85% this academic year and have not received any major disciplinary actions in the past 12 months. I have previous experience in leadership through my involvement in school clubs and volunteer activities, which has helped me develop strong communication and organizational skills. I have also submitted a character reference from one of my teachers, along with a clear campaign platform that reflects my goals and commitment to serving the student body. I meet the minimum age requirement of 14 and have obtained a signed consent form from my parent/guardian to run for this position.'),
(284, 'v', 'Vice-President', 'team 1', 'Mallory', 'Morales', 'Henson', 'Male', '3rd year', 'upload/1760299938_boy-candidate-icon-cartoon-employee-job-vector.jpg', 'I am currently enrolled as a full-time student and have maintained a GPA of at least 2.5, with no failing grades in the previous semester. I have an attendance record of over 85% this academic year and have not received any major disciplinary actions in the past 12 months. I have previous experience in leadership through my involvement in school clubs and volunteer activities, which has helped me develop strong communication and organizational skills. I have also submitted a character reference from one of my teachers, along with a clear campaign platform that reflects my goals and commitment to serving the student body. I meet the minimum age requirement of 14 and have obtained a signed consent form from my parent/guardian to run for this position.'),
(285, 'v', 'Vice-President', 'Team 2', 'Mara', 'Bartlett', 'Berger', 'Female', '4th year', 'upload/1760299988_girl-candidate-icon-cartoon-employee-talent-vector.jpg', 'I am currently enrolled as a full-time student and have maintained a GPA of at least 2.5, with no failing grades in the previous semester. I have an attendance record of over 85% this academic year and have not received any major disciplinary actions in the past 12 months. I have previous experience in leadership through my involvement in school clubs and volunteer activities, which has helped me develop strong communication and organizational skills. I have also submitted a character reference from one of my teachers, along with a clear campaign platform that reflects my goals and commitment to serving the student body. I meet the minimum age requirement of 14 and have obtained a signed consent form from my parent/guardian to run for this position.'),
(286, 'v', 'Vice-President', 'Team 2', 'Heaven', 'Ibarra', 'Henson', 'Female', '3rd year', 'upload/1760300024_girl-candidate-icon-cartoon-employee-talent-vector.jpg', 'I am currently enrolled as a full-time student and have maintained a GPA of at least 2.5, with no failing grades in the previous semester. I have an attendance record of over 85% this academic year and have not received any major disciplinary actions in the past 12 months. I have previous experience in leadership through my involvement in school clubs and volunteer activities, which has helped me develop strong communication and organizational skills. I have also submitted a character reference from one of my teachers, along with a clear campaign platform that reflects my goals and commitment to serving the student body. I meet the minimum age requirement of 14 and have obtained a signed consent form from my parent/guardian to run for this position.'),
(287, 'v', 'Vice-President', 'team 3', 'Amayah', 'Harding', 'Harding', 'Male', '3rd year', 'upload/1760300057_boy-candidate-icon-cartoon-employee-job-vector.jpg', 'I am currently enrolled as a full-time student and have maintained a GPA of at least 2.5, with no failing grades in the previous semester. I have an attendance record of over 85% this academic year and have not received any major disciplinary actions in the past 12 months. I have previous experience in leadership through my involvement in school clubs and volunteer activities, which has helped me develop strong communication and organizational skills. I have also submitted a character reference from one of my teachers, along with a clear campaign platform that reflects my goals and commitment to serving the student body. I meet the minimum age requirement of 14 and have obtained a signed consent form from my parent/guardian to run for this position.'),
(288, 'a', 'Governor', 'team 1', 'Lorelai', 'Booth', 'Henson', 'Male', '4th year', 'upload/1760300123_boy-candidate-icon-cartoon-employee-job-vector.jpg', 'I am currently enrolled as a full-time student and have maintained a GPA of at least 2.5, with no failing grades in the previous semester. I have an attendance record of over 85% this academic year and have not received any major disciplinary actions in the past 12 months. I have previous experience in leadership through my involvement in school clubs and volunteer activities, which has helped me develop strong communication and organizational skills. I have also submitted a character reference from one of my teachers, along with a clear campaign platform that reflects my goals and commitment to serving the student body. I meet the minimum age requirement of 14 and have obtained a signed consent form from my parent/guardian to run for this position.'),
(289, 'a', 'Governor', 'Team 2', 'Brynn', 'Alexander', 'gulapa', 'Male', '4th year', 'upload/1760300163_boy-candidate-icon-cartoon-employee-job-vector.jpg', 'I am currently enrolled as a full-time student and have maintained a GPA of at least 2.5, with no failing grades in the previous semester. I have an attendance record of over 85% this academic year and have not received any major disciplinary actions in the past 12 months. I have previous experience in leadership through my involvement in school clubs and volunteer activities, which has helped me develop strong communication and organizational skills. I have also submitted a character reference from one of my teachers, along with a clear campaign platform that reflects my goals and commitment to serving the student body. I meet the minimum age requirement of 14 and have obtained a signed consent form from my parent/guardian to run for this position.'),
(290, 'a', 'Governor', 'team 3', 'Kyleigh', 'Bonilla', 'Bonilla', 'Female', '3rd year', 'upload/1760300201_girl-candidate-icon-cartoon-employee-talent-vector.jpg', 'I am currently enrolled as a full-time student and have maintained a GPA of at least 2.5, with no failing grades in the previous semester. I have an attendance record of over 85% this academic year and have not received any major disciplinary actions in the past 12 months. I have previous experience in leadership through my involvement in school clubs and volunteer activities, which has helped me develop strong communication and organizational skills. I have also submitted a character reference from one of my teachers, along with a clear campaign platform that reflects my goals and commitment to serving the student body. I meet the minimum age requirement of 14 and have obtained a signed consent form from my parent/guardian to run for this position.'),
(291, 'p', 'President', 'Team 2', 'Myra', 'Krueger', 'Henson', 'Female', '4th year', 'upload/1760300233_girl-candidate-icon-cartoon-employee-talent-vector.jpg', 'I am currently enrolled as a full-time student and have maintained a GPA of at least 2.5, with no failing grades in the previous semester. I have an attendance record of over 85% this academic year and have not received any major disciplinary actions in the past 12 months. I have previous experience in leadership through my involvement in school clubs and volunteer activities, which has helped me develop strong communication and organizational skills. I have also submitted a character reference from one of my teachers, along with a clear campaign platform that reflects my goals and commitment to serving the student body. I meet the minimum age requirement of 14 and have obtained a signed consent form from my parent/guardian to run for this position.'),
(292, 'a', 'Governor', 'Team 2', 'Janelle', 'Palacios', 'Palacios', 'Male', '3rd year', 'upload/1760300263_boy-candidate-icon-cartoon-employee-job-vector.jpg', ''),
(293, 'b', 'Vice-Governor', 'team 3', 'Lea', 'Fox', 'Fox', 'Female', '3rd year', 'upload/1760300296_girl-candidate-icon-cartoon-employee-talent-vector.jpg', ''),
(294, 'b', 'Vice-Governor', 'Team 2', 'Mackenzie', 'Fischer', 'Fischer', 'Female', '4th year', 'upload/1760300323_girl-candidate-icon-cartoon-employee-talent-vector.jpg', ''),
(295, 'b', 'Vice-Governor', 'Team 2', 'Keily', 'Lozano', 'Lozano', 'Male', '4th year', 'upload/1760300354_boy-candidate-icon-cartoon-employee-job-vector.jpg', ''),
(296, 'b', 'Vice-Governor', 'team 1', 'Justice', 'Long', 'Henson', 'Female', '4th year', 'upload/1760300400_girl-candidate-icon-cartoon-employee-talent-vector.jpg', ''),
(297, 's', 'Secretary', 'team 3', 'Rebekah', 'Rodgers', 'Rodgers', 'Female', '4th year', 'upload/1760300434_girl-candidate-icon-cartoon-employee-talent-vector.jpg', ''),
(298, 's', 'Secretary', 'Team 2', 'Myla', 'Figueroa', 'Figueroa', 'Female', '4th year', 'upload/1760300462_girl-candidate-icon-cartoon-employee-talent-vector.jpg', ''),
(299, 's', 'Secretary', 'Team 2', 'Winter', 'Heath', 'Heath', 'Male', '3rd year', 'upload/1760300492_boy-candidate-icon-cartoon-employee-job-vector.jpg', ''),
(300, 'p', 'President', 'team 3', 'Kenzie', 'Chase', 'Chase', 'Female', '3rd year', 'upload/boy-candidate-icon-cartoon-employee-job-vector.jpg', ''),
(301, 't', 'Treasurer', 'team 1', 'Angie', 'Lara', 'Lara', 'Female', '4th year', 'upload/1760300557_girl-candidate-icon-cartoon-employee-talent-vector.jpg', ''),
(302, 't', 'Treasurer', 'team 3', 'Giselle', 'Chang', 'Henson', 'Female', '4th year', 'upload/1760300595_girl-candidate-icon-cartoon-employee-talent-vector.jpg', ''),
(303, 't', 'Treasurer', 'Team 2', 'Payton', 'Dickerson', 'Dickerson', 'Male', '3rd year', 'upload/1760300626_boy-candidate-icon-cartoon-employee-job-vector.jpg', ''),
(304, 't', 'Treasurer', 'team 3', 'Aubrey', 'Morse', 'Morse', 'Female', '4th year', 'upload/1760300660_girl-candidate-icon-cartoon-employee-talent-vector.jpg', ''),
(305, 's', 'Social-Media Officer', 'team 1', 'Elina', 'McBride', 'McBride', 'Female', '1st year', 'upload/1760300708_girl-candidate-icon-cartoon-employee-talent-vector.jpg', ''),
(306, 's', 'Social-Media Officer', 'team 1', 'Zhuri', 'Bond', 'Bond', 'Male', '4th year', 'upload/1760300735_boy-candidate-icon-cartoon-employee-job-vector.jpg', ''),
(307, 'p', 'President', 'team 3', 'Ariyah', 'Buchanan', 'Buchanan', 'Female', '3rd year', 'upload/1760300769_girl-candidate-icon-cartoon-employee-talent-vector.jpg', ''),
(308, 's', 'Social-Media Officer', 'team 1', 'Enrique', 'Merritt', 'Merritt', 'Male', '4th year', 'upload/1760300826_boy-candidate-icon-cartoon-employee-job-vector.jpg', '');

-- --------------------------------------------------------

--
-- Table structure for table `complaint`
--

CREATE TABLE `complaint` (
  `complaint_id` int(11) NOT NULL,
  `voterID` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','in_progress','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Username` varchar(100) NOT NULL,
  `SchoolID` varchar(50) NOT NULL,
  `Year` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaint`
--

INSERT INTO `complaint` (`complaint_id`, `voterID`, `subject`, `description`, `status`, `created_at`, `updated_at`, `Username`, `SchoolID`, `Year`) VALUES
(10, 100, '1', 'Voting machines malfunctioned, causing long delays and confusion at polls.', 'pending', '2025-06-03 11:03:55', '2025-06-03 11:03:55', 'joan.mariano.au@phinmaed.com', '01-1234-12345', '4th year'),
(11, 96, '2', 'Some voters were turned away due to missing names on lists.', 'pending', '2025-06-03 11:05:27', '2025-06-03 11:05:27', 'jimz.aluquin.au@phinmaed.com', '01-1234-12345', '4th year'),
(12, 97, '3', 'Ballots were not properly secured, risking tampering during the counting.', 'pending', '2025-06-03 11:06:10', '2025-06-03 11:06:10', 'elgo.miranda.au@phinmaed.com', '01-1234-12345', '4th year'),
(13, 98, '4', 'Campaign materials were removed unfairly, limiting candidates’ chances to communicate.', 'resolved', '2025-06-03 11:07:03', '2025-06-03 11:25:32', 'don.aluquin.au@phinmaed.com', '01-1234-12345', '4th year'),
(14, 101, '5', 'Polling stations opened late, resulting in voters missing their chance.', 'in_progress', '2025-06-03 11:09:13', '2025-06-03 11:25:31', 'aeron.salipsip.au@phinmaed.com	', '01-1234-12345', '4th year');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `history_id` int(11) NOT NULL,
  `data` varchar(30) NOT NULL,
  `action` varchar(50) NOT NULL,
  `date` varchar(20) NOT NULL,
  `user` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`history_id`, `data`, `action`, `date`, `user`) VALUES
(793, 'Laydee Champagne', 'Login', '2025-05-26 16:41:40', 'admin'),
(792, 'Vincent Unarce', 'Added Voter', '5/26/2025 16:39:31', 'admin'),
(791, 'Jeff Ladignon', 'Added Voter', '5/26/2025 16:38:43', 'admin'),
(790, 'Aeron Paul Salipsip', 'Added Voter', '5/26/2025 16:38:6', 'admin'),
(789, 'Laydee Champagne', 'Login', '2025-05-26 16:38:03', 'admin'),
(788, 'Kat CSDL', 'Logout', '2025-05-26 16:37:48', 'admin'),
(787, 'Kat CSDL', 'Login', '2025-05-26 16:36:55', 'admin'),
(786, 'Kat CSDL', 'Logout', '2025-05-26 16:35:07', 'admin'),
(785, 'Kat CSDL', 'Login', '2025-05-26 16:34:51', 'admin'),
(784, 'Laydee Champagne', 'Logout', '2025-05-26 16:34:37', 'admin'),
(783, 'Amiel Villanueva', 'Added Voter', '5/26/2025 16:32:55', 'admin'),
(782, 'Jimmuel Aluquin', 'Added Voter', '5/26/2025 16:32:16', 'admin'),
(781, 'Don Aluquin', 'Added Voter', '5/26/2025 16:31:28', 'admin'),
(780, 'Eljohn Miranda', 'Added Voter', '5/26/2025 16:31:3', 'admin'),
(779, 'Jay Mariano', 'Added Voter', '5/26/2025 16:30:37', 'admin'),
(778, 'Joseph Santos', 'Added Voter', '5/26/2025 16:29:46', 'admin'),
(777, 'Laydee Champagne', 'Login', '2025-05-26 16:28:51', 'admin'),
(776, 'Kat CSDL', 'Logout', '2025-05-26 16:25:14', 'admin'),
(775, 'Kat CSDL', 'Login', '2025-05-26 16:24:59', 'admin'),
(794, 'Laydee Champagne', 'Added Voter', '5/27/2025 8:37:16', 'admin'),
(795, 'Laydee Champagne', 'Added Voter', '5/27/2025 8:37:56', 'admin'),
(796, 'Laydee Champagne', 'Login', '2025-06-01 18:10:44', 'admin'),
(797, 'Laydee Champagne', 'Logout', '2025-06-01 19:13:40', 'admin'),
(798, 'Laydee Champagne', 'Login', '2025-06-01 19:13:56', 'admin'),
(799, 'Laydee Champagne', 'Login', '2025-06-01 19:15:34', 'admin'),
(800, 'Laydee Champagne', 'Logout', '2025-06-01 19:20:35', 'admin'),
(801, 'Laydee Champagne', 'Login', '2025-06-03 17:09:31', 'admin'),
(802, 'Joseph FAFA', 'Added Candidate', '2025-06-03 17:15:27', 'admin'),
(803, 'ASDFA FAFA', 'Added Candidate', '2025-06-03 17:17:54', 'admin'),
(804, 'Joseph FAFA', 'Added Candidate', '2025-06-03 17:23:07', 'admin'),
(805, 'Joseph FAFA', 'Deleted Candidate', '6/3/2025 17:23:37', 'Admin'),
(806, '', 'Deleted Candidate', '6/3/2025 17:23:46', 'Admin'),
(807, 'Joseph FAFA', 'Edit Candidate', '2025-06-03 17:26:47', 'admin'),
(808, 'Joseph FAFA', 'Edit Candidate', '2025-06-03 17:27:22', 'admin'),
(809, 'Joseph FAFA', 'Edit Candidate', '2025-06-03 17:29:23', 'admin'),
(810, 'Joseph FAFA', 'Edit Candidate', '2025-06-03 17:29:26', 'admin'),
(811, 'Joseph FAFA', 'Edit Candidate', '2025-06-03 17:30:34', 'admin'),
(812, 'Joseph FAFA', 'Edit Candidate', '2025-06-03 17:30:44', 'admin'),
(813, 'Laydee Champagne', 'Login', '2025-06-03 17:36:51', 'admin'),
(814, 'Joseph FAFA', 'Deleted Candidate', '6/3/2025 18:8:50', 'Admin'),
(815, 'test test', 'Added Voter', '6/3/2025 18:9:6', 'admin'),
(816, 'test1 test1', 'Added Voter', '6/3/2025 18:9:24', 'admin'),
(817, 'test2 test2', 'Added Voter', '6/3/2025 18:9:42', 'admin'),
(818, ' ', 'Logout', '2025-06-03 18:20:05', ''),
(819, 'Laydee Champagne', 'Login', '2025-06-03 18:20:10', 'admin'),
(820, 'Laydee Champagne', 'Login', '2025-06-03 18:20:59', 'admin'),
(821, 'Laydee Champagne', 'Login', '2025-06-03 18:22:07', 'admin'),
(822, 'Laydee Champagne', 'Login', '2025-06-03 18:23:42', 'admin'),
(823, 'Jimmuel Aluquin', 'Added Voter', '6/3/2025 18:40:40', 'admin'),
(824, 'Eljohn Miranda', 'Added Voter', '6/3/2025 18:41:15', 'admin'),
(825, 'Don Aluquin', 'Added Voter', '6/3/2025 18:41:38', 'admin'),
(826, 'Joseph Santos', 'Added Voter', '6/3/2025 18:41:54', 'admin'),
(827, 'John Arnie Mariano', 'Added Voter', '6/3/2025 18:42:15', 'admin'),
(828, 'Aeron Paul Salipsip', 'Added Voter', '6/3/2025 18:42:42', 'admin'),
(829, 'Amiel Villanueva', 'Added Voter', '6/3/2025 18:42:57', 'admin'),
(830, 'Jeff Ladignon', 'Added Voter', '6/3/2025 18:43:17', 'admin'),
(831, 'Vincent Unarce', 'Added Voter', '6/3/2025 18:43:37', 'admin'),
(832, 'Laydee Champagne', 'Added Voter', '6/3/2025 18:43:56', 'admin'),
(833, 'Laydee Champagne', 'Added Voter', '6/3/2025 18:44:17', 'admin'),
(834, 'Joseph Santos', 'Edit Candidate', '2025-06-03 18:47:23', 'admin'),
(835, 'Don Emmanuel Aluquin', 'Edit Candidate', '2025-06-03 18:48:02', 'admin'),
(836, 'Margarette Roque', 'Edit Candidate', '2025-06-03 18:48:06', 'admin'),
(837, 'Kurt Angelo Aragon', 'Edit Candidate', '2025-06-03 18:48:11', 'admin'),
(838, 'Sharmaine Blanca', 'Edit Candidate', '2025-06-03 18:48:18', 'admin'),
(839, 'Ma.Alyssa Sevilla', 'Edit Candidate', '2025-06-03 18:48:22', 'admin'),
(840, 'Cristian Fernandez', 'Edit Candidate', '2025-06-03 18:48:34', 'admin'),
(841, 'Cristian Fernandez', 'Edit Candidate', '2025-06-03 18:48:38', 'admin'),
(842, 'Kristopher Glenn Martinez', 'Edit Candidate', '2025-06-03 18:48:42', 'admin'),
(843, 'Aleacel Postor', 'Edit Candidate', '2025-06-03 18:48:48', 'admin'),
(844, 'John Cedrick Melegrito', 'Edit Candidate', '2025-06-03 18:48:54', 'admin'),
(845, 'Adrianne Aebes Maligaya', 'Edit Candidate', '2025-06-03 18:48:58', 'admin'),
(846, 'Franz Andrei  Villasquez', 'Edit Candidate', '2025-06-03 18:49:02', 'admin'),
(847, 'Amiel Angelo Villanueva', 'Edit Candidate', '2025-06-03 18:49:06', 'admin'),
(848, 'John Michael Parungao', 'Edit Candidate', '2025-06-03 18:49:10', 'admin'),
(849, 'Aeron Paul Salipsip', 'Edit Candidate', '2025-06-03 18:49:17', 'admin'),
(850, 'Justine Retiro', 'Edit Candidate', '2025-06-03 18:49:22', 'admin'),
(851, 'Cyrell Domingo', 'Edit Candidate', '2025-06-03 18:49:28', 'admin'),
(852, 'Vincent Unarce', 'Edit Candidate', '2025-06-03 18:49:44', 'admin'),
(853, 'David Tristan Bernal', 'Edit Candidate', '2025-06-03 18:49:54', 'admin'),
(854, 'Jessica Villabriga', 'Edit Candidate', '2025-06-03 18:50:00', 'admin'),
(855, 'Vincent Francisco', 'Edit Candidate', '2025-06-03 18:50:06', 'admin'),
(856, ' ', 'Logout', '2025-06-03 18:50:08', ''),
(857, 'Laydee Champagne', 'Login', '2025-06-03 18:50:13', 'admin'),
(858, 'Jeff Ladignon', 'Edit Candidate', '2025-06-03 18:50:26', 'admin'),
(859, 'Lanz Andrei Molina', 'Edit Candidate', '2025-06-03 18:50:31', 'admin'),
(860, 'Eljohn Miranda', 'Edit Candidate', '2025-06-03 18:50:40', 'admin'),
(861, 'John Arnie  Mariano', 'Edit Candidate', '2025-06-03 18:50:48', 'admin'),
(862, 'John Riel Parcasio', 'Edit Candidate', '2025-06-03 18:50:55', 'admin'),
(863, 'Jerald Torrs', 'Edit Candidate', '2025-06-03 18:51:21', 'admin'),
(864, 'Laydee Champagne', 'Logout', '2025-06-03 18:51:44', 'admin'),
(865, 'Laydee Champagne', 'Login', '2025-06-03 18:51:48', 'admin'),
(866, 'Laydee Champagne', 'Logout', '2025-06-03 18:58:57', 'admin'),
(867, 'Laydee Champagne', 'Login', '2025-06-03 18:59:26', 'admin'),
(868, 'Laydee Champagne', 'Login', '2025-06-03 19:00:11', 'admin'),
(869, 'Laydee Champagne', 'Login', '2025-06-03 19:01:15', 'admin'),
(870, 'Laydee Champagne', 'Login', '2025-06-03 19:07:32', 'admin'),
(871, 'Laydee Champagne', 'Login', '2025-06-03 19:12:01', 'admin'),
(872, 'Laydee Champagne', 'Logout', '2025-06-03 19:26:01', 'admin'),
(873, 'Laydee Champagne', 'Login', '2025-06-05 19:17:47', 'admin'),
(874, 'Laydee Champagne', 'Logout', '2025-06-05 19:17:59', 'admin'),
(875, 'Laydee Champagne', 'Login', '2025-06-05 19:22:24', 'admin'),
(876, 'Laydee Champagne', 'Login', '2025-07-06 16:19:06', 'admin'),
(877, 'Laydee Champagne', 'Login', '2025-07-31 21:10:36', 'admin'),
(878, 'Laydee Champagne', 'Login', '2025-08-01 12:16:38', 'admin'),
(879, 'Laydee Champagne', 'Logout', '2025-08-01 12:29:58', 'admin'),
(880, 'Laydee Champagne', 'Login', '2025-08-01 12:30:03', 'admin'),
(881, 'Laydee Champagne', 'Logout', '2025-08-01 15:07:08', 'admin'),
(882, 'Laydee Champagne', 'Login', '2025-08-01 15:10:16', 'admin'),
(883, 'Laydee Champagne', 'Logout', '2025-08-01 15:12:12', 'admin'),
(884, 'Laydee Champagne', 'Login', '2025-08-01 15:12:17', 'admin'),
(885, 'Laydee Champagne', 'Login', '2025-08-02 10:35:08', 'admin'),
(886, 'Laydee Champagne', 'Logout', '2025-08-02 11:06:17', 'admin'),
(887, 'Laydee Champagne', 'Login', '2025-08-02 11:06:33', 'admin'),
(888, 'Laydee Champagne', 'Logout', '2025-08-02 11:35:04', 'admin'),
(889, 'Laydee Champagne', 'Login', '2025-08-02 11:35:14', 'admin'),
(890, 'Jay Mariano', 'Deleted Voter', '8/2/2025 11:38:29', 'admin'),
(891, 'Laydee Champagne', 'Logout', '2025-08-02 11:39:19', 'admin'),
(892, 'Laydee Champagne', 'Login', '2025-08-02 11:39:34', 'admin'),
(893, 'Laydee Champagne', 'Logout', '2025-08-02 11:39:48', 'admin'),
(894, 'Laydee Champagne', 'Login', '2025-08-02 11:40:31', 'admin'),
(895, 'Laydee Champagne', 'Logout', '2025-08-02 11:50:18', 'admin'),
(896, 'Laydee Champagne', 'Login', '2025-08-02 13:49:50', 'admin'),
(897, 'a a', 'Added Voter', '8/2/2025 15:15:15', 'admin'),
(898, ' ', 'Logout', '2025-08-02 15:20:40', ''),
(899, 'Laydee Champagne', 'Login', '2025-08-02 15:25:59', 'admin'),
(900, 'Laydee Champagne', 'Login', '2025-08-02 15:30:48', 'admin'),
(901, 'a a', 'Added Voter', '8/2/2025 15:31:17', 'admin'),
(902, 'a a', 'Added Voter', '8/2/2025 15:31:34', 'admin'),
(903, 'Laydee Champagne', 'Login', '2025-08-02 15:35:44', 'admin'),
(904, 'Laydee Champagne', 'Login', '2025-08-02 20:14:47', 'admin'),
(905, 'Laydee Champagne', 'Logout', '2025-08-02 20:15:10', 'admin'),
(906, 'Laydee Champagne', 'Login', '2025-08-02 20:25:31', 'admin'),
(907, 'Laydee Champagne', 'Login', '2025-08-02 20:26:01', 'admin'),
(908, 'Laydee Champagne', 'Logout', '2025-08-02 20:26:05', 'admin'),
(909, 'Laydee Champagne', 'Login', '2025-08-02 20:28:00', 'admin'),
(910, 'Laydee Champagne', 'Login', '2025-08-02 20:28:55', 'admin'),
(911, 'Laydee Champagne', 'Logout', '2025-08-02 20:28:59', 'admin'),
(912, 'Laydee Champagne', 'Login', '2025-08-02 20:33:40', 'admin'),
(913, 'Laydee Champagne', 'Logout', '2025-08-02 20:33:44', 'admin'),
(914, 'Laydee Champagne', 'Login', '2025-08-02 20:35:58', 'admin'),
(915, 'Laydee Champagne', 'Login', '2025-08-02 20:36:20', 'admin'),
(916, 'Laydee Champagne', 'Logout', '2025-08-02 20:36:26', 'admin'),
(917, 'Laydee Champagne', 'Login', '2025-08-02 20:37:50', 'admin'),
(918, 'Laydee Champagne', 'Logout', '2025-08-02 20:38:10', 'admin'),
(919, 'Laydee Champagne', 'Login', '2025-08-02 20:46:55', 'admin'),
(920, 'Jay Marinao', 'Added Voter', '8/2/2025 20:47:18', 'admin'),
(921, 'Laydee Champagne', 'Login', '2025-08-02 21:03:59', 'admin'),
(922, 'Laydee Champagne', 'Login', '2025-08-02 21:36:50', 'admin'),
(923, 'Laydee Champagne', 'Login', '2025-08-03 10:56:06', 'admin'),
(924, 'Laydee Champagne', 'Login', '2025-08-03 11:26:23', 'admin'),
(925, 'Laydee Champagne', 'Login', '2025-08-03 12:01:53', 'admin'),
(926, ' ', 'Logout', '2025-08-03 12:11:34', ''),
(927, 'Laydee Champagne', 'Login', '2025-08-03 12:11:41', 'admin'),
(928, 'Laydee Champagne', 'Login', '2025-08-03 17:39:22', 'admin'),
(929, 'Laydee Champagne', 'Login', '2025-08-03 21:40:17', 'admin'),
(930, 'Laydee Champagne', 'Login', '2025-08-03 21:40:58', 'admin'),
(931, 'Laydee Champagne', 'Login', '2025-08-03 21:50:27', 'admin'),
(932, 'Laydee Champagne', 'Login', '2025-08-03 22:11:29', 'admin'),
(933, 'Laydee Champagne', 'Logout', '2025-08-03 22:12:58', 'admin'),
(934, 'Laydee Champagne', 'Login', '2025-08-03 22:13:23', 'admin'),
(935, 'Laydee Champagne', 'Login', '2025-08-04 11:20:57', 'admin'),
(936, 'Laydee Champagne', 'Login', '2025-08-04 11:24:57', 'admin'),
(937, 'Laydee Champagne', 'Login', '2025-08-14 13:21:58', 'admin'),
(938, 'Laydee Champagne', 'Login', '2025-08-14 13:31:16', 'admin'),
(939, 'Laydee Champagne', 'Login', '2025-09-26 15:04:42', 'admin'),
(940, 'Laydee Champagne', 'Login', '2025-09-26 15:08:08', 'admin'),
(941, 'Laydee Champagne', 'Logout', '2025-09-26 16:27:14', 'admin'),
(942, 'Laydee Champagne', 'Login', '2025-09-26 16:48:48', 'admin'),
(943, 'Laydee Champagne', 'Logout', '2025-09-26 16:51:03', 'admin'),
(944, 'Laydee Champagne', 'Login', '2025-09-26 16:51:22', 'admin'),
(945, 'Laydee Champagne', 'Logout', '2025-09-26 16:57:33', 'admin'),
(946, 'Laydee Champagne', 'Login', '2025-09-30 15:16:38', 'admin'),
(947, 'Laydee Champagne', 'Login', '2025-09-30 15:27:18', 'admin'),
(948, 'Laydee Champagne', 'Logout', '2025-09-30 15:32:45', 'admin'),
(949, 'Laydee Champagne', 'Login', '2025-09-30 15:34:36', 'admin'),
(950, 'Laydee Champagne', 'Logout', '2025-09-30 15:41:03', 'admin'),
(951, 'Laydee Champagne', 'Login', '2025-10-01 16:29:36', 'admin'),
(952, 'Laydee Champagne', 'Logout', '2025-10-01 16:30:46', 'admin'),
(953, 'Laydee Champagne', 'Login', '2025-10-01 16:31:26', 'admin'),
(954, 'Laydee Champagne', 'Logout', '2025-10-01 16:31:58', 'admin'),
(955, 'Laydee Champagne', 'Login', '2025-10-07 17:30:06', 'admin'),
(956, 'Laydee Champagne', 'Logout', '2025-10-07 17:38:47', 'admin'),
(957, 'Laydee Champagne', 'Login', '2025-10-07 17:40:54', 'admin'),
(958, 'Laydee Champagne', 'Logout', '2025-10-07 17:41:41', 'admin'),
(959, 'Laydee Champagne', 'Login', '2025-10-07 17:41:48', 'admin'),
(960, 'Laydee Champagne', 'Logout', '2025-10-07 17:44:09', 'admin'),
(961, 'Laydee Champagne', 'Login', '2025-10-07 17:45:01', 'admin'),
(962, 'Laydee Champagne', 'Logout', '2025-10-07 17:45:22', 'admin'),
(963, 'Laydee Champagne', 'Login', '2025-10-07 17:45:35', 'admin'),
(964, 'Laydee Champagne', 'Logout', '2025-10-07 17:47:20', 'admin'),
(965, 'Laydee Champagne', 'Login', '2025-10-08 17:24:49', 'admin'),
(966, 'Laydee Champagne', 'Login', '2025-10-09 14:38:04', 'admin'),
(967, 'MARIANO JOHN ARNIE', 'Added Voter', '10/9/2025 14:50:7', 'admin'),
(968, 'MARIANO JOHN ARNIE1', 'Added Voter', '10/9/2025 14:50:7', 'admin'),
(969, 'MARIANO JOHN ARNIE1', 'Deleted Voter', '10/9/2025 14:50:29', 'admin'),
(970, 'MARIANO JOHN ARNIE', 'Deleted Voter', '10/9/2025 14:50:29', 'admin'),
(971, 'MARIANO JOHN ARNIE', 'Added Voter', '10/9/2025 14:51:15', 'admin'),
(972, 'MARIANO1 JOHN ARNIE1', 'Added Voter', '10/9/2025 14:51:15', 'admin'),
(973, 'MARIANO2 JOHN ARNIE2', 'Added Voter', '10/9/2025 14:51:15', 'admin'),
(974, 'MARIANO2 JOHN ARNIE2', 'Deleted Voter', '10/9/2025 14:51:40', 'admin'),
(975, 'MARIANO1 JOHN ARNIE1', 'Deleted Voter', '10/9/2025 14:51:40', 'admin'),
(976, 'MARIANO JOHN ARNIE', 'Deleted Voter', '10/9/2025 14:51:40', 'admin'),
(977, 'Laydee Champagne', 'Login', '2025-10-09 14:56:10', 'admin'),
(978, 'Laydee Champagne', 'Logout', '2025-10-09 14:56:22', 'admin'),
(979, 'Laydee Champagne', 'Login', '2025-10-09 14:56:36', 'admin'),
(980, 'Laydee Champagne', 'Logout', '2025-10-09 14:58:21', 'admin'),
(981, 'Laydee Champagne', 'Login', '2025-10-13 00:54:45', 'admin'),
(982, 'Laydee Champagne', 'Login', '2025-10-13 01:18:01', 'admin'),
(983, 'Carl Justine', 'Added Voter', '10/13/2025 1:49:52', 'admin'),
(984, 'Carl Justine', 'Added Voter', '10/13/2025 1:50:53', 'admin'),
(985, 'Carl Justine', 'Added Voter', '10/13/2025 1:50:53', 'admin'),
(986, 'Carl Justine', 'Added Voter', '10/13/2025 1:50:53', 'admin'),
(987, 'justine Justine', 'Added Voter', '10/13/2025 1:50:53', 'admin'),
(988, 'Carl Justine', 'Deleted Voter', '10/13/2025 1:51:42', 'admin'),
(989, 'justine Justine', 'Deleted Voter', '10/13/2025 1:51:42', 'admin'),
(990, 'Carl Justine', 'Deleted Voter', '10/13/2025 1:51:42', 'admin'),
(991, 'Carl Justine', 'Deleted Voter', '10/13/2025 1:51:42', 'admin'),
(992, 'Carl Justine', 'Deleted Voter', '10/13/2025 1:51:42', 'admin'),
(993, 'Denmark delacruz', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(994, 'Kevin Morales', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(995, 'Skylar Andrews', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(996, 'Theodoraemon Boyer', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(997, 'Theodora Smith', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(998, 'Theodora McKee', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(999, 'Alaric Burton', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(1000, 'Theo Smith', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(1001, 'Kevin Boyer', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(1002, 'Smith Andrews', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(1003, 'Theodora Boyer', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(1004, 'Frankie Bonilla', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(1005, 'Nancy Knapp', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(1006, 'Leandra Valencia', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(1007, 'Rebecca Parsons', 'Deleted Candidate', '10/13/2025 1:52:31', 'Admin'),
(1008, 'Curtis Smith', 'Deleted Candidate', '10/13/2025 1:53:1', 'Admin'),
(1009, 'Aaron Norris', 'Deleted Candidate', '10/13/2025 1:53:1', 'Admin'),
(1010, 'Arielle Kennedy', 'Deleted Candidate', '10/13/2025 1:53:1', 'Admin'),
(1011, 'Wally Bayola', 'Deleted Candidate', '10/13/2025 1:53:1', 'Admin'),
(1012, 'Alaric Boyer', 'Deleted Candidate', '10/13/2025 1:53:1', 'Admin'),
(1013, 'Curtis Rice', 'Deleted Candidate', '10/13/2025 1:53:1', 'Admin'),
(1014, 'ca ca', 'Added Candidate', '2025-10-13 01:59:58', 'admin'),
(1015, 'ca ca', 'Edit Candidate', '2025-10-13 02:02:40', 'admin'),
(1016, 'ca ca', 'Edit Candidate', '2025-10-13 02:03:30', 'admin'),
(1017, 'ca ca', 'Edit Candidate', '2025-10-13 02:03:33', 'admin'),
(1018, 'ca ca', 'Edit Candidate', '2025-10-13 02:03:42', 'admin'),
(1019, 'ca ca', 'Deleted Candidate', '10/13/2025 2:3:42', 'Admin'),
(1020, '312 123', 'Added Voter', '10/13/2025 3:17:2', 'admin'),
(1021, 'Carl Justine', 'Added Voter', '10/13/2025 3:19:21', 'admin'),
(1022, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1023, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1024, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1025, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1026, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1027, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1028, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1029, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1030, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1031, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1032, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1033, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1034, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1035, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1036, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1037, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1038, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1039, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1040, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1041, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1042, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1043, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1044, 'Carl Justine', 'Added Voter', '10/13/2025 3:23:26', 'admin'),
(1045, 'Lady Eranista', 'Login', '2025-10-13 03:58:04', 'admin'),
(1046, 'ca ca', 'Added Candidate', '2025-10-13 03:58:15', 'admin'),
(1047, 'ca ca', 'Added Candidate', '2025-10-13 03:58:26', 'admin'),
(1048, 'ca ca', 'Deleted Candidate', '10/13/2025 3:58:26', 'Admin'),
(1049, 'ca ca', 'Deleted Candidate', '10/13/2025 3:58:26', 'Admin'),
(1050, 'Emory Henson', 'Added Candidate', '2025-10-13 04:07:31', 'admin'),
(1051, 'Demi Conley', 'Added Candidate', '2025-10-13 04:09:24', 'admin'),
(1052, 'Oakleigh Berger', 'Added Candidate', '2025-10-13 04:10:18', 'admin'),
(1053, 'Katie Olson', 'Added Candidate', '2025-10-13 04:11:28', 'admin'),
(1054, 'Mallory Morales', 'Added Candidate', '2025-10-13 04:12:18', 'admin'),
(1055, 'Mara Bartlett', 'Added Candidate', '2025-10-13 04:13:08', 'admin'),
(1056, 'Heaven Ibarra', 'Added Candidate', '2025-10-13 04:13:44', 'admin'),
(1057, 'Amayah Harding', 'Added Candidate', '2025-10-13 04:14:17', 'admin'),
(1058, 'Lorelai Booth', 'Added Candidate', '2025-10-13 04:15:23', 'admin'),
(1059, 'Brynn Alexander', 'Added Candidate', '2025-10-13 04:16:03', 'admin'),
(1060, 'Kyleigh Bonilla', 'Added Candidate', '2025-10-13 04:16:41', 'admin'),
(1061, 'Myra Krueger', 'Added Candidate', '2025-10-13 04:17:13', 'admin'),
(1062, 'Janelle Palacios', 'Added Candidate', '2025-10-13 04:17:43', 'admin'),
(1063, 'Lea Fox', 'Added Candidate', '2025-10-13 04:18:16', 'admin'),
(1064, 'Mackenzie Fischer', 'Added Candidate', '2025-10-13 04:18:43', 'admin'),
(1065, 'Keily Lozano', 'Added Candidate', '2025-10-13 04:19:14', 'admin'),
(1066, 'Justice Long', 'Added Candidate', '2025-10-13 04:20:00', 'admin'),
(1067, 'Rebekah Rodgers', 'Added Candidate', '2025-10-13 04:20:34', 'admin'),
(1068, 'Myla Figueroa', 'Added Candidate', '2025-10-13 04:21:02', 'admin'),
(1069, 'Winter Heath', 'Added Candidate', '2025-10-13 04:21:32', 'admin'),
(1070, 'Kenzie Chase', 'Added Candidate', '2025-10-13 04:22:06', 'admin'),
(1071, 'Angie Lara', 'Added Candidate', '2025-10-13 04:22:37', 'admin'),
(1072, 'Giselle Chang', 'Added Candidate', '2025-10-13 04:23:15', 'admin'),
(1073, 'Payton Dickerson', 'Added Candidate', '2025-10-13 04:23:46', 'admin'),
(1074, 'Aubrey Morse', 'Added Candidate', '2025-10-13 04:24:20', 'admin'),
(1075, 'Elina McBride', 'Added Candidate', '2025-10-13 04:25:08', 'admin'),
(1076, 'Zhuri Bond', 'Added Candidate', '2025-10-13 04:25:35', 'admin'),
(1077, 'Ariyah Buchanan', 'Added Candidate', '2025-10-13 04:26:09', 'admin'),
(1078, 'Enrique Merritt', 'Added Candidate', '2025-10-13 04:27:06', 'admin'),
(1079, 'Emory Henson', 'Deleted Candidate', '10/13/2025 4:27:6', 'Admin'),
(1080, 'Oakleigh Berger', 'Deleted Candidate', '10/13/2025 4:27:6', 'Admin'),
(1081, 'Demi Conley', 'Deleted Candidate', '10/13/2025 4:27:6', 'Admin'),
(1082, 'Kenzie Chase', 'Edit Candidate', '2025-10-13 04:27:51', 'admin'),
(1083, 'Cory Nicholson', 'Added Candidate', '2025-10-13 04:28:16', 'admin'),
(1084, 'Van Heath', 'Added Candidate', '2025-10-13 04:28:41', 'admin'),
(1085, 'Aiden Zamora', 'Added Candidate', '2025-10-13 04:29:19', 'admin'),
(1086, 'Brock Church', 'Added Candidate', '2025-10-13 04:29:43', 'admin'),
(1087, 'Myles Weeks', 'Added Candidate', '2025-10-13 04:30:37', 'admin'),
(1088, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:31:15', 'admin'),
(1089, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:31:15', 'admin'),
(1090, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:31:15', 'admin'),
(1091, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:31:15', 'admin'),
(1092, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:31:15', 'admin'),
(1093, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:31:15', 'admin'),
(1094, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:31:15', 'admin'),
(1095, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:31:15', 'admin'),
(1096, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:31:15', 'admin'),
(1097, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:31:15', 'admin'),
(1098, 'Laydee Champagne', 'Login', '2025-10-13 04:34:52', 'admin'),
(1099, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:35:9', 'admin'),
(1100, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:35:9', 'admin'),
(1101, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:35:9', 'admin'),
(1102, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:35:9', 'admin'),
(1103, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:35:9', 'admin'),
(1104, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:35:9', 'admin'),
(1105, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:35:9', 'admin'),
(1106, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:35:9', 'admin'),
(1107, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:35:9', 'admin'),
(1108, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:35:9', 'admin'),
(1109, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1110, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1111, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1112, 'Carl Justine', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1113, 'Jeff Ladignon', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1114, 'Amiel Villanueva', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1115, 'Aeron Paul Salipsip', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1116, 'Don Aluquin', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1117, 'Joseph Santos', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1118, 'John Arnie Mariano', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1119, '312 123', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1120, 'Jay Marinao', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1121, 'Vincent Unarce', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1122, 'Laydee Champagne', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1123, 'Laydee Champagne', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1124, 'Eljohn Miranda', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1125, 'Jimmuel Aluquin', 'Deleted Voter', '10/13/2025 4:38:16', 'admin'),
(1126, 'Carl Justine', 'Added Voter', '10/13/2025 4:39:20', 'admin'),
(1127, 'Laydee Champagne', 'Login', '2025-10-13 05:08:51', 'admin'),
(1128, 'Denmark Morales', 'Added Voter', '10/13/2025 5:9:3', 'admin'),
(1129, 'Laydee Champagne', 'Login', '2025-10-13 05:13:05', 'admin'),
(1130, 'Laydee Champagne', 'Logout', '2025-10-13 05:14:15', 'admin'),
(1131, 'Kat CSDL', 'Login', '2025-10-13 05:39:07', 'admin'),
(1132, 'Laydee Champagne', 'Login', '2025-10-13 05:49:01', 'admin'),
(1133, 'Denmark Morales', 'Added Candidate', '2025-10-13 05:49:24', 'admin'),
(1134, 'Laydee Champagne', 'Logout', '2025-10-13 05:49:33', 'admin'),
(1135, 'Kat CSDL', 'Login', '2025-10-13 05:49:43', 'admin'),
(1136, 'Kat CSDL', 'Logout', '2025-10-13 05:51:02', 'admin'),
(1137, 'Kat CSDL', 'Login', '2025-10-13 05:51:06', 'admin'),
(1138, 'Laydee Champagne', 'Login', '2025-10-13 05:51:36', 'admin'),
(1139, 'Denmark Heath', 'Added Candidate', '2025-10-13 06:06:58', 'admin'),
(1140, 'Laydee Champagne', 'Logout', '2025-10-13 06:07:19', 'admin'),
(1141, 'Kat CSDL', 'Login', '2025-10-13 06:07:29', 'admin'),
(1142, 'Kat CSDL', 'Logout', '2025-10-13 06:07:57', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `User_id` int(11) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `User_Type` varchar(50) NOT NULL,
  `Position` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`User_id`, `FirstName`, `LastName`, `UserName`, `Password`, `User_Type`, `Position`) VALUES
(6, 'Laydee', 'Champagne', 'admin', '1234567', 'admin', 'Admin'),
(13, 'John', 'Leabres', 'John', '12345', 'admin', 'Secretary Officer'),
(12, 'Evelyn ', 'Juliano', 'evelyn', '12345', 'admin', 'Faculty Officer'),
(14, 'Isaiah', 'Mizona', 'Isaiah', '12345', 'admin', 'Election Officer 1'),
(15, 'Kat', 'CSDL', 'Kat', '12345', 'admin', 'CSDL Officer'),
(16, 'Laydee', 'Champagne', 'Laydee', '12345', 'admin', 'CSDL Officer');

-- --------------------------------------------------------

--
-- Table structure for table `voters`
--

CREATE TABLE `voters` (
  `VoterID` int(11) NOT NULL,
  `FirstName` varchar(150) NOT NULL,
  `LastName` varchar(150) NOT NULL,
  `MiddleName` varchar(100) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Year` varchar(100) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `SchoolID` varchar(100) NOT NULL,
  `Verified` enum('Verified','Not Verified') NOT NULL DEFAULT 'Not Verified',
  `DateVoted` varchar(50) NOT NULL,
  `TimeVoted` varchar(50) NOT NULL,
  `Room` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `voters`
--

INSERT INTO `voters` (`VoterID`, `FirstName`, `LastName`, `MiddleName`, `Username`, `Password`, `Email`, `Year`, `Status`, `SchoolID`, `Verified`, `DateVoted`, `TimeVoted`, `Room`) VALUES
(148, 'Carl', 'Justine', 'Palma', 'calu.palma.au@phinmaed.com', '01-1920-05053', '', '4th year', 'Voted', '01-1920-05053', 'Verified', '2025-10-13', '05:08:25', ''),
(149, 'Denmark', 'Morales', 'G', 'southphinmaau@gmail.com', '12-1234-12345', '', '1st year', 'Unvoted', '12-1234-12345', 'Verified', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `ID` int(11) NOT NULL,
  `CandidateID` int(11) NOT NULL,
  `votes` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`ID`, `CandidateID`, `votes`) VALUES
(461, 218, 0),
(460, 217, 0),
(459, 215, 0),
(458, 210, 0),
(457, 213, 0),
(456, 212, 0),
(455, 204, 0),
(454, 202, 0),
(453, 218, 0),
(452, 217, 0),
(451, 215, 0),
(450, 210, 0),
(449, 213, 0),
(448, 212, 0),
(447, 204, 0),
(446, 203, 0),
(445, 218, 0),
(444, 217, 0),
(443, 215, 0),
(442, 210, 0),
(441, 213, 0),
(440, 212, 0),
(439, 204, 0),
(438, 203, 0),
(437, 218, 0),
(436, 217, 0),
(435, 215, 0),
(434, 210, 0),
(433, 213, 0),
(432, 212, 0),
(431, 204, 0),
(430, 202, 0),
(429, 218, 0),
(428, 217, 0),
(427, 215, 0),
(426, 210, 0),
(425, 213, 0),
(424, 212, 0),
(423, 204, 0),
(422, 202, 0),
(421, 218, 0),
(420, 217, 0),
(419, 215, 0),
(418, 210, 0),
(417, 213, 0),
(416, 212, 0),
(415, 204, 0),
(414, 203, 0),
(413, 218, 0),
(412, 216, 0),
(411, 214, 0),
(410, 210, 0),
(409, 208, 0),
(408, 212, 0),
(407, 204, 0),
(406, 202, 0),
(405, 218, 0),
(404, 216, 0),
(403, 214, 0),
(402, 210, 0),
(401, 208, 0),
(400, 212, 0),
(399, 205, 0),
(398, 203, 0),
(462, 203, 0),
(463, 205, 0),
(464, 212, 0),
(465, 208, 0),
(466, 210, 0),
(467, 214, 0),
(468, 216, 0),
(469, 218, 0),
(470, 202, 0),
(471, 204, 0),
(472, 207, 0),
(473, 213, 0),
(474, 211, 0),
(475, 214, 0),
(476, 217, 0),
(477, 218, 0),
(478, 222, 0),
(479, 204, 0),
(480, 207, 0),
(481, 213, 0),
(482, 210, 0),
(483, 214, 0),
(484, 216, 0),
(485, 218, 0),
(486, 203, 0),
(487, 205, 0),
(488, 212, 0),
(489, 208, 0),
(490, 210, 0),
(491, 214, 0),
(492, 216, 0),
(493, 218, 0),
(494, 203, 0),
(495, 205, 0),
(496, 212, 0),
(497, 208, 0),
(498, 210, 0),
(499, 214, 0),
(500, 216, 0),
(501, 218, 0),
(502, 203, 0),
(503, 205, 0),
(504, 212, 0),
(505, 208, 0),
(506, 210, 0),
(507, 214, 0),
(508, 216, 0),
(509, 218, 0),
(510, 203, 0),
(511, 205, 0),
(512, 212, 0),
(513, 208, 0),
(514, 210, 0),
(515, 214, 0),
(516, 216, 0),
(517, 218, 0),
(518, 213, 0),
(519, 211, 0),
(520, 215, 0),
(521, 217, 0),
(522, 218, 0),
(523, 204, 0),
(524, 207, 0),
(525, 213, 0),
(526, 211, 0),
(527, 215, 0),
(528, 217, 0),
(529, 218, 0),
(530, 204, 0),
(531, 207, 0),
(532, 213, 0),
(533, 211, 0),
(534, 215, 0),
(535, 217, 0),
(536, 218, 0),
(537, 203, 0),
(538, 224, 0),
(539, 213, 0),
(540, 226, 0),
(541, 215, 0),
(542, 218, 0),
(543, 203, 0),
(544, 204, 0),
(545, 213, 0),
(546, 211, 0),
(547, 217, 0),
(548, 202, 0),
(549, 204, 0),
(550, 203, 0),
(551, 207, 0),
(552, 213, 0),
(553, 218, 0),
(554, 291, 0),
(555, 284, 0),
(556, 289, 0),
(557, 294, 0),
(558, 298, 0),
(559, 302, 0),
(560, 305, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidate`
--
ALTER TABLE `candidate`
  ADD PRIMARY KEY (`CandidateID`);

--
-- Indexes for table `complaint`
--
ALTER TABLE `complaint`
  ADD PRIMARY KEY (`complaint_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`history_id`);

--
-- Indexes for table `voters`
--
ALTER TABLE `voters`
  ADD PRIMARY KEY (`VoterID`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidate`
--
ALTER TABLE `candidate`
  MODIFY `CandidateID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=316;

--
-- AUTO_INCREMENT for table `complaint`
--
ALTER TABLE `complaint`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1143;

--
-- AUTO_INCREMENT for table `voters`
--
ALTER TABLE `voters`
  MODIFY `VoterID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=561;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
