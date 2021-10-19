-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 12, 2021 at 05:39 AM
-- Server version: 5.7.35-cll-lve
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ephraimBecker`
--

-- --------------------------------------------------------

--
-- Table structure for table `thoughts`
--

CREATE TABLE `thoughts` (
  `ThoughtId` int(11) NOT NULL,
  `TimelineId` int(11) NOT NULL,
  `DateCreated` datetime NOT NULL,
  `DateModified` datetime NOT NULL,
  `Thought` varchar(255) NOT NULL,
  `hide` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `thoughts`
--

INSERT INTO `thoughts` (`ThoughtId`, `TimelineId`, `DateCreated`, `DateModified`, `Thought`, `hide`) VALUES
(4, 125, '2021-09-30 16:49:47', '2021-09-30 16:49:47', 'Test', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `thoughts`
--
ALTER TABLE `thoughts`
  ADD PRIMARY KEY (`ThoughtId`),
  ADD KEY `FOREIGN` (`TimelineId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `thoughts`
--
ALTER TABLE `thoughts`
  MODIFY `ThoughtId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `thoughts`
--
ALTER TABLE `thoughts`
  ADD CONSTRAINT `FOREIGN` FOREIGN KEY (`TimelineId`) REFERENCES `timeline` (`TimelineId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
