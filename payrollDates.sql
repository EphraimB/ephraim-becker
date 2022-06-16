-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 17, 2022 at 08:20 AM
-- Server version: 5.7.37-cll-lve
-- PHP Version: 7.3.32

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
-- Table structure for table `payrollDates`
--

CREATE TABLE `payrollDates` (
  `payrollDates_id` int(11) NOT NULL,
  `DateCreated` datetime NOT NULL,
  `PayrollDay` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payrollDates`
--

INSERT INTO `payrollDates` (`payrollDates_id`, `DateCreated`, `PayrollDay`) VALUES
(3, '2022-05-15 15:52:24', 15),
(5, '2022-05-15 17:03:01', 31);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payrollDates`
--
ALTER TABLE `payrollDates`
  ADD PRIMARY KEY (`payrollDates_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payrollDates`
--
ALTER TABLE `payrollDates`
  MODIFY `payrollDates_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
