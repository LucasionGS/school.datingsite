-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2020 at 05:28 AM
-- Server version: 10.1.40-MariaDB
-- PHP Version: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_datesite`
--

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `fullname` text NOT NULL,
  `likedBy` text NOT NULL,
  `pfp` text NOT NULL,
  `bio` text NOT NULL,
  `birthdate` datetime NOT NULL,
  `height` int(11) NOT NULL,
  `weight` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `fullname`, `likedBy`, `pfp`, `bio`, `birthdate`, `height`, `weight`) VALUES
(1, 'Administrator', '', '', 'Admin user', '0000-00-00 00:00:00', 0, 0),
(2, 'Lucas', '', '', 'Unfortunately, I still exist', '2002-02-09 08:00:00', 179, 60);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `accessToken` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `accessToken`) VALUES
(1, 'Admin', 'Admin@toxen.net', '$2y$10$o9VTWFrxztWyhHO6.MrFnO6CZZExmiPr.9BFVCGiZyneA8Sfmes2u', '5F0FF17015CD8BB8'),
(2, 'lucasion', 'lucasion@hotmail.com', '$2y$10$vwHuRPb2w1zp0wnraY3yduEsm/RD4zf6R3G/rpFRbgu5QHqKEEImS', 'D52CFD6D2B1A6');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `Inserted_User` AFTER INSERT ON `users` FOR EACH ROW INSERT INTO `profiles` (`id`) VALUES (new.id)
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
