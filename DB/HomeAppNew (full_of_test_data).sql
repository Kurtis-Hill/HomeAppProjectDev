-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 10, 2021 at 06:03 PM
-- Server version: 10.3.27-MariaDB-0+deb10u1
-- PHP Version: 7.3.27-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `HomeAppNew`
--

-- --------------------------------------------------------

--
-- Table structure for table `analog`
--

CREATE TABLE `analog` (
  `analogID` int(11) NOT NULL,
  `roomID` int(11) DEFAULT NULL,
  `groupNameID` int(11) DEFAULT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  `analogReading` smallint(6) DEFAULT NULL,
  `highAnalog` smallint(6) DEFAULT 1000,
  `lowAnalog` smallint(6) DEFAULT 1000,
  `constRecord` tinyint(4) DEFAULT 0,
  `deviceNameID` int(11) NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `analog`
--

INSERT INTO `analog` (`analogID`, `roomID`, `groupNameID`, `sensorNameID`, `analogReading`, `highAnalog`, `lowAnalog`, `constRecord`, `deviceNameID`, `timez`) VALUES
(7, NULL, NULL, 127, 10, 9999, 1000, 0, 74, '2021-04-03 13:04:07'),
(9, NULL, NULL, 130, 10, 9999, 1111, 0, 74, '2021-04-03 13:26:46'),
(10, NULL, NULL, 134, 10, 9999, 1111, 0, 74, '2021-04-03 13:39:05');

-- --------------------------------------------------------

--
-- Table structure for table `bmp`
--

CREATE TABLE `bmp` (
  `bmpID` int(11) NOT NULL,
  `tempID` int(11) NOT NULL,
  `humidID` int(11) NOT NULL,
  `latitudeID` int(11) NOT NULL,
  `cardViewID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bmp`
--

INSERT INTO `bmp` (`bmpID`, `tempID`, `humidID`, `latitudeID`, `cardViewID`) VALUES
(1, 34, 25, 1, 94);

-- --------------------------------------------------------

--
-- Table structure for table `cardcolour`
--

CREATE TABLE `cardcolour` (
  `colourID` int(11) NOT NULL,
  `colour` varchar(20) NOT NULL,
  `shade` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cardcolour`
--

INSERT INTO `cardcolour` (`colourID`, `colour`, `shade`) VALUES
(1, 'warning', 'Yellow'),
(2, 'success', 'Green'),
(3, 'primary', 'blue'),
(4, 'danger', 'red');

-- --------------------------------------------------------

--
-- Table structure for table `cardstate`
--

CREATE TABLE `cardstate` (
  `cardStateID` int(11) NOT NULL,
  `state` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cardstate`
--

INSERT INTO `cardstate` (`cardStateID`, `state`) VALUES
(1, 'on'),
(2, 'off'),
(3, 'on index not room'),
(4, 'on room not index');

-- --------------------------------------------------------

--
-- Table structure for table `cardview`
--

CREATE TABLE `cardview` (
  `cardViewID` int(11) NOT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `cardIconID` int(11) DEFAULT NULL,
  `cardColourID` int(11) DEFAULT NULL,
  `cardStateID` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cardview`
--

INSERT INTO `cardview` (`cardViewID`, `sensorNameID`, `userID`, `cardIconID`, `cardColourID`, `cardStateID`) VALUES
(8, 29, 1, 22, 1, 1),
(9, 30, 1, 15, 4, 1),
(10, 31, 1, 13, 1, 1),
(11, 32, 1, 21, 4, 1),
(12, 33, 1, 13, 3, 1),
(13, 34, 1, 28, 1, 1),
(14, 35, 1, 6, 3, 1),
(15, 36, 1, 16, 1, 1),
(16, 37, 1, 8, 1, 1),
(17, 38, 1, 4, 1, 1),
(18, 39, 1, 15, 1, 1),
(19, 40, 1, 5, 3, 1),
(20, 41, 1, 10, 2, 1),
(21, 42, 1, 18, 3, 1),
(22, 43, 1, 1, 4, 1),
(23, 44, 1, 21, 4, 1),
(24, 45, 1, 7, 4, 1),
(25, 46, 1, 12, 3, 1),
(26, 47, 1, 26, 4, 1),
(27, 48, 1, 16, 1, 1),
(28, 49, 1, 16, 1, 1),
(29, 50, 1, 4, 4, 1),
(30, 51, 1, 4, 4, 1),
(31, 58, 1, 14, 4, 1),
(32, 59, 1, 4, 3, 1),
(33, 60, 1, 5, 1, 1),
(34, 61, 1, 2, 1, 1),
(35, 62, 1, 1, 4, 1),
(36, 63, 1, 26, 3, 1),
(37, 64, 1, 23, 4, 1),
(38, 65, 1, 25, 1, 1),
(39, 68, 1, 11, 1, 1),
(40, 69, 1, 22, 2, 1),
(41, 70, 1, 5, 4, 1),
(42, 71, 1, 12, 1, 1),
(43, 72, 1, 19, 4, 1),
(44, 73, 1, 14, 4, 1),
(45, 74, 1, 9, 3, 1),
(46, 75, 1, 23, 4, 1),
(50, 79, 1, 7, 3, 1),
(54, 83, 1, 3, 1, 1),
(60, 89, 1, 11, 3, 1),
(61, 90, 1, 17, 2, 1),
(62, 91, 1, 23, 3, 1),
(63, 92, 1, 24, 4, 1),
(65, 94, 1, 17, 2, 1),
(66, 95, 1, 18, 3, 1),
(67, 96, 1, 9, 2, 1),
(68, 97, 1, 28, 3, 1),
(69, 98, 1, 22, 3, 1),
(70, 99, 1, 20, 3, 1),
(71, 100, 1, 25, 2, 1),
(72, 101, 1, 9, 2, 1),
(73, 102, 1, 16, 2, 1),
(74, 103, 1, 24, 2, 1),
(75, 104, 1, 28, 1, 1),
(76, 105, 1, 1, 4, 1),
(77, 106, 1, 25, 1, 1),
(78, 107, 1, 11, 1, 1),
(79, 108, 1, 1, 4, 1),
(80, 109, 1, 7, 2, 1),
(81, 110, 1, 13, 2, 1),
(82, 111, 1, 10, 3, 1),
(83, 112, 1, 7, 1, 1),
(84, 113, 1, 4, 1, 1),
(85, 114, 1, 18, 2, 1),
(86, 115, 1, 22, 4, 1),
(87, 116, 1, 1, 2, 1),
(88, 117, 1, 19, 3, 1),
(89, 118, 1, 8, 2, 1),
(90, 119, 1, 20, 2, 1),
(91, 120, 1, 16, 4, 1),
(92, 121, 1, 22, 2, 1),
(93, 122, 1, 17, 1, 1),
(94, 123, 1, 2, 1, 1),
(95, 124, 1, 13, 2, 1),
(96, 125, 1, 9, 1, 1),
(97, 126, 1, 13, 3, 1),
(98, 127, 1, 28, 2, 1),
(99, 128, 1, 3, 2, 1),
(100, 129, 1, 7, 4, 1),
(101, 130, 1, 26, 3, 1),
(102, 131, 1, 13, 3, 1),
(103, 132, 1, 20, 2, 1),
(104, 133, 1, 15, 3, 1),
(105, 134, 1, 14, 1, 1),
(106, 135, 1, 13, 3, 1),
(107, 136, 1, 11, 4, 1),
(108, 137, 1, 14, 1, 1),
(109, 138, 1, 11, 2, 1),
(110, 139, 1, 12, 2, 1),
(111, 140, 1, 27, 4, 1),
(113, 142, 1, 13, 1, 1),
(114, 143, 1, 24, 3, 1),
(115, 144, 1, 22, 3, 1),
(116, 145, 1, 28, 3, 1),
(117, 146, 1, 1, 1, 1),
(118, 147, 1, 21, 4, 1),
(119, 148, 1, 9, 4, 1),
(120, 150, 1, 13, 1, 1),
(121, 151, 1, 16, 2, 1),
(122, 152, 1, 9, 4, 1),
(123, 153, 1, 22, 3, 1),
(124, 155, 1, 15, 3, 1),
(125, 156, 1, 19, 3, 1),
(126, 157, 1, 22, 4, 1),
(127, 158, 1, 2, 4, 1),
(128, 159, 1, 13, 3, 1),
(129, 160, 1, 24, 2, 1),
(130, 161, 1, 21, 2, 1),
(131, 162, 1, 27, 3, 1),
(132, 163, 1, 4, 1, 1),
(133, 164, 1, 18, 2, 1),
(134, 165, 1, 9, 2, 1),
(135, 166, 1, 1, 4, 1),
(136, 167, 1, 12, 2, 1),
(137, 168, 1, 12, 1, 1),
(138, 169, 1, 26, 4, 1),
(139, 170, 1, 15, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `constanalog`
--

CREATE TABLE `constanalog` (
  `analogID` int(11) NOT NULL,
  `sensorID` int(11) NOT NULL,
  `sensorReading` float NOT NULL,
  `timez` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `consthumid`
--

CREATE TABLE `consthumid` (
  `humidID` int(11) NOT NULL,
  `sensorID` int(11) NOT NULL,
  `sensorReading` float NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `consttemp`
--

CREATE TABLE `consttemp` (
  `tempID` int(11) NOT NULL,
  `sensorID` int(11) NOT NULL,
  `sensorReading` float NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dallas`
--

CREATE TABLE `dallas` (
  `dallasID` int(11) NOT NULL,
  `tempID` int(11) NOT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  `cardViewID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dallas`
--

INSERT INTO `dallas` (`dallasID`, `tempID`, `sensorNameID`, `cardViewID`) VALUES
(1, 11, 44, 23),
(2, 33, NULL, 61),
(3, 36, NULL, 114);

-- --------------------------------------------------------

--
-- Table structure for table `devicenames`
--

CREATE TABLE `devicenames` (
  `deviceNameID` int(11) NOT NULL,
  `deviceName` varchar(20) NOT NULL,
  `password` longtext NOT NULL,
  `groupNameID` int(11) NOT NULL,
  `roomID` int(11) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:json)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `devicenames`
--

INSERT INTO `devicenames` (`deviceNameID`, `deviceName`, `password`, `groupNameID`, `roomID`, `createdBy`, `roles`) VALUES
(74, 'LivingRoomTankSensor', '532454564554', 1, 1, 1, ''),
(75, 'LivingRoomBMP', 'f1d25982881bc05c55a620b2839b5f45', 1, 1, 1, ''),
(76, 'AirSensorBedroom', 'b8697294c81254c60881379feb8eb9fa', 1, 1, 1, ''),
(77, 'sadfsdfff', 'e85d8fd51c02ca4e72d7f65c9f141da2', 1, 1, 1, ''),
(78, 'test22', '37cb7b4c67725488ce06941d52aa9452', 1, 1, 1, ''),
(79, 'test3', 'ef6344118f030cc9a84abb2b202ee9a2', 1, 1, 1, ''),
(80, 'YEPPP', '788210bf447683487df0904d82b548c2', 1, 1, 1, ''),
(81, 'YEPPP', '13cd021a2286128c5e0c3d82179cd706', 1, 1, 1, ''),
(82, 'itsanewdevice', '4bdba8fa0f420ea487863afa1c82c51f', 1, 1, 1, ''),
(83, 'newOne', '4b59de1a3d12d6da4b67367e05e4c18b', 1, 1, 1, ''),
(84, 'dsfsddsf', '6213e263d421dd06b41fca666b325e4a', 1, 1, 1, ''),
(85, 'dsfsddsf', '2cb08005e36289b997f7c93d7f92d647', 1, 1, 1, ''),
(86, 'test3', '479e2e135a59b3c2875e65f77c491656', 1, 1, 1, ''),
(87, 'test3', '7ad3ffbd4893d244fbf20735efa47eca', 1, 1, 1, ''),
(88, 'test3SADS', '9b36c0c57f3abad317e66166592e8052', 1, 1, 1, ''),
(89, 'test3SADS', 'fadfc58fbdd38ad33a8c5f5917111e63', 1, 1, 1, ''),
(90, 'test3', 'c94c3566b800c1b2410e77e20913f860', 1, 1, 1, ''),
(91, 'test3', '1ea2f248d2d0d7c2afe5efc643878c38', 1, 1, 1, ''),
(92, 'test3', '1faf2315b3a85633c1d2bfa60c24542f', 1, 1, 1, ''),
(93, 'test3', 'ff6555e48f3b1839aa8d3932708be83d', 1, 1, 1, ''),
(94, 'test33', 'a82715646b3a8272981399cb58dbb16d', 1, 1, 1, ''),
(95, 'test333', 'c3ca8f5b96a8791284d95e1c15c1ef9d', 1, 1, 1, ''),
(96, 'test3', '17daec5e158b884d9455b91071d739ae', 1, 1, 1, ''),
(97, 'test3', '3085e300694d5822e67d57f7d182fe31', 1, 1, 1, ''),
(98, 'test3324sd', 'fd7d77f19332e7045b070b5471113342', 1, 1, 1, ''),
(99, 'hereWeeGoooo', '$argon2id$v=19$m=65536,t=4,p=1$OHBVcGV1U1VmSE5kTkRDcw$jQQAC4YLGN4W7AWGR6IMwRuC97k8h7LoKMWSzExTJqQ', 1, 1, 1, '[\"ROLE_DEVICE\"]'),
(100, 'hereWeeGoooohey', '$argon2id$v=19$m=65536,t=4,p=1$RWxCRy9GVjFrdEt0ZU5rbQ$RI+qCKmAXrFkKam1y33Uf0BuXvNYIPT4115wzf5JHtI', 1, 1, 1, '[\"ROLE_DEVICE\"]'),
(101, 'apitest', '$argon2id$v=19$m=65536,t=4,p=1$SUVLRzBUOVVRYUJXYW1VdA$DIw24pbHf+PXCGbj7lNrP5RLdADdb3HFu9UddF9pSps', 1, 1, 1, '[\"ROLE_DEVICE\"]'),
(102, 'sudowoodo', 'hey', 1, 1, 1, '[\"ROLE_DEVICE\"]'),
(103, 'whaaaatumm', 'hey', 1, 1, 1, '[\"ROLE_DEVICE\"]'),
(104, 'acd', 'hey', 1, 1, 1, '[\"ROLE_DEVICE\"]'),
(105, 'newfishtank', 'hey', 1, 1, 1, '[\"ROLE_DEVICE\"]'),
(106, 'sdfhnn', '$argon2id$v=19$m=65536,t=4,p=1$cHlteENORi5kWUdPcU5GVQ$KtBPOWyCu8IF+5H44U+rB81ZLxxlbbvlg5bxYIVh2uw', 1, 1, 1, '[\"ROLE_DEVICE\"]');

-- --------------------------------------------------------

--
-- Table structure for table `dhtsensor`
--

CREATE TABLE `dhtsensor` (
  `dhtID` int(11) NOT NULL,
  `tempID` int(11) NOT NULL,
  `humidID` int(11) NOT NULL,
  `cardviewID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dhtsensor`
--

INSERT INTO `dhtsensor` (`dhtID`, `tempID`, `humidID`, `cardviewID`) VALUES
(1, 10, 3, 22),
(2, 12, 4, 24),
(3, 13, 5, 25),
(4, 15, 7, 27),
(5, 16, 8, 28),
(6, 17, 9, 29),
(7, 18, 10, 30),
(8, 19, 11, 45),
(12, 23, 15, 50),
(21, 32, 24, 60),
(22, 35, 26, 113),
(23, 37, 27, 115),
(24, 38, 28, 116),
(25, 39, 29, 117),
(26, 40, 30, 118),
(27, 41, 31, 119),
(28, 42, 32, 128),
(29, 43, 33, 129),
(30, 44, 34, 134),
(31, 45, 35, 135),
(32, 46, 36, 136),
(33, 47, 37, 137),
(34, 48, 38, 138),
(35, 49, 39, 139);

-- --------------------------------------------------------

--
-- Table structure for table `groupname`
--

CREATE TABLE `groupname` (
  `groupNameID` int(11) NOT NULL,
  `groupName` varchar(50) NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `groupname`
--

INSERT INTO `groupname` (`groupNameID`, `groupName`, `timez`) VALUES
(1, 'admin', '2020-07-07 23:00:48'),
(2, 'test', '2020-08-30 23:28:28'),
(3, '2', '2021-04-03 11:31:03'),
(9, '3', '2021-04-03 11:35:43'),
(11, '4', '2021-04-03 11:38:59');

-- --------------------------------------------------------

--
-- Table structure for table `groupnnamemapping`
--

CREATE TABLE `groupnnamemapping` (
  `groupNameMappingID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `groupNameID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `groupnnamemapping`
--

INSERT INTO `groupnnamemapping` (`groupNameMappingID`, `userID`, `groupNameID`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 11);

-- --------------------------------------------------------

--
-- Table structure for table `humid`
--

CREATE TABLE `humid` (
  `humidID` int(11) NOT NULL,
  `roomID` int(11) DEFAULT NULL,
  `groupNameID` int(11) DEFAULT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  `humidReading` float NOT NULL,
  `highHumid` float NOT NULL DEFAULT 70,
  `lowHumid` float NOT NULL DEFAULT 15,
  `constRecord` tinyint(1) NOT NULL DEFAULT 0,
  `deviceNameID` int(11) NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `humid`
--

INSERT INTO `humid` (`humidID`, `roomID`, `groupNameID`, `sensorNameID`, `humidReading`, `highHumid`, `lowHumid`, `constRecord`, `deviceNameID`, `timez`) VALUES
(3, NULL, NULL, 43, 30, 80, 10, 1, 76, '2021-02-01 22:35:26'),
(4, NULL, NULL, 45, 30, 80, 10, 1, 81, '2021-02-01 22:43:15'),
(5, NULL, NULL, 46, 30, 60, 20, 1, 82, '2021-02-01 23:02:08'),
(7, NULL, NULL, 48, 30, 80, 10, 0, 82, '2021-02-01 23:54:21'),
(8, NULL, NULL, 49, 30, 80, 10, 0, 82, '2021-02-01 23:56:35'),
(9, NULL, NULL, 50, 30, 80, 10, 0, 82, '2021-02-01 23:56:46'),
(10, NULL, NULL, 51, 30, 80, 10, 1, 98, '2021-03-02 18:03:27'),
(11, NULL, NULL, 74, 10, 80, 10, 0, 98, '2021-04-02 12:15:37'),
(15, NULL, NULL, 79, 10, 80, 10, 0, 98, '2021-04-02 13:02:59'),
(24, NULL, NULL, 89, 10, 80, 10, 0, 74, '2021-04-03 12:04:33'),
(25, NULL, NULL, 123, 10, 80, 10, 0, 74, '2021-04-03 12:47:20'),
(26, NULL, NULL, 142, 10, 80, 10, 0, 74, '2021-04-04 16:36:19'),
(27, NULL, NULL, 144, 10, 80, 10, 0, 74, '2021-04-04 16:46:26'),
(28, NULL, NULL, 145, 10, 80, 10, 0, 74, '2021-04-04 16:51:55'),
(29, NULL, NULL, 146, 10, 80, 10, 0, 74, '2021-04-04 16:53:24'),
(30, NULL, NULL, 147, 10, 80, 10, 0, 74, '2021-04-04 16:55:08'),
(31, NULL, NULL, 148, 10, 80, 10, 0, 74, '2021-04-04 16:57:00'),
(32, NULL, NULL, 159, 10, 80, 10, 0, 104, '2021-04-05 20:27:04'),
(33, NULL, NULL, 160, 10, 80, 10, 0, 104, '2021-04-05 20:27:47'),
(34, NULL, NULL, 165, 10, 80, 10, 0, 104, '2021-04-05 20:34:57'),
(35, NULL, NULL, 166, 10, 80, 10, 1, 104, '2021-04-05 20:35:20'),
(36, NULL, NULL, 167, 10, 80, 10, 0, 74, '2021-04-09 12:41:51'),
(37, NULL, NULL, 168, 10, 80, 10, 0, 78, '2021-04-09 15:06:07'),
(38, NULL, NULL, 169, 10, 80, 10, 0, 78, '2021-04-09 16:48:46'),
(39, NULL, NULL, 170, 10, 80, 10, 0, 101, '2021-04-09 18:15:13');

-- --------------------------------------------------------

--
-- Table structure for table `icons`
--

CREATE TABLE `icons` (
  `iconID` int(11) NOT NULL,
  `iconName` varchar(20) NOT NULL,
  `description` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `icons`
--

INSERT INTO `icons` (`iconID`, `iconName`, `description`) VALUES
(1, 'air-freshener', 'Christmas tree'),
(2, 'anchor', 'Sea anchor'),
(3, 'warehouse', 'warehouse'),
(4, 'archway', 'archway'),
(5, 'baby', ''),
(6, 'bath', 'bath and shower'),
(7, 'bed', 'bed'),
(8, 'cannabis', 'cannabis leaf'),
(9, 'camera', 'camera'),
(10, 'carrot', 'carrot'),
(11, 'campground', 'tent'),
(12, 'chart-pie', 'graph'),
(13, 'crosshairs', 'scope aim'),
(14, 'database', 'symbol'),
(15, 'dog', 'doggie'),
(16, 'dove', 'bird'),
(17, 'download', 'download logo'),
(18, 'fish', 'fishys'),
(19, 'flask', 'science beaker'),
(20, 'fort-awesome', 'castle'),
(21, 'mobile-alt', 'mobile phone'),
(22, 'php', 'php logo'),
(23, 'Playstation', 'ps1 logo'),
(24, 'power-off', 'shutdown logo'),
(25, 'raspberry-pi', 'pi logo'),
(26, 'xbox', 'xbox logo'),
(27, 'skull-crossbones', 'skull and bones'),
(28, 'smoking', 'smoking');

-- --------------------------------------------------------

--
-- Table structure for table `latitude`
--

CREATE TABLE `latitude` (
  `latitudeID` int(11) NOT NULL,
  `sensorNameID` int(11) NOT NULL,
  `deviceNameID` int(11) NOT NULL,
  `latitude` int(11) NOT NULL,
  `lowLatitude` int(11) NOT NULL,
  `highLatitude` int(11) NOT NULL,
  `constRecord` tinyint(1) NOT NULL DEFAULT 0,
  `timez` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `latitude`
--

INSERT INTO `latitude` (`latitudeID`, `sensorNameID`, `deviceNameID`, `latitude`, `lowLatitude`, `highLatitude`, `constRecord`, `timez`) VALUES
(1, 123, 74, 10, 58, 66, 0, '2021-04-03');

-- --------------------------------------------------------

--
-- Table structure for table `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `outofrangeanalog`
--

CREATE TABLE `outofrangeanalog` (
  `analogID` int(11) NOT NULL,
  `sensorID` int(11) NOT NULL,
  `sensorReading` float DEFAULT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `outofrangehumid`
--

CREATE TABLE `outofrangehumid` (
  `humidID` int(11) NOT NULL,
  `sensorID` int(11) NOT NULL,
  `sensorReading` float NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `outofrangetemp`
--

CREATE TABLE `outofrangetemp` (
  `tempID` int(11) NOT NULL,
  `sensorID` int(11) NOT NULL,
  `sensorReading` float NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `outofrangetemp`
--

INSERT INTO `outofrangetemp` (`tempID`, `sensorID`, `sensorReading`, `timez`) VALUES
(1, 44, 45, '2021-03-07 19:08:10');

-- --------------------------------------------------------

--
-- Table structure for table `refresh_tokens`
--

CREATE TABLE `refresh_tokens` (
  `id` int(11) NOT NULL,
  `refresh_token` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `refresh_tokens`
--

INSERT INTO `refresh_tokens` (`id`, `refresh_token`, `username`, `valid`) VALUES
(1, '000afc2f4323ab0758446a2124b6126158cc75b58386005490caa0312e36b3c23a5c3615e2a9065c206fbdb0305ebf9364918551013637f54c908a6bfc6ff4d2', 'admin', '2020-08-13 21:28:05'),
(2, '8aadadf21aadf591208165d6ef5c8224df94c6278de67bb4a5a15ec0a89771d04b6671d499cdf84805698bcc71d2bffad45e48fa5f4d58c08eceb33d29d0898d', 'admin', '2020-08-13 21:39:15'),
(3, 'bb101eb6a1c6a488860ce6b878fe26aab013d68fea199bd25e48ece981edc2794907a0229cc105fda45c064f532384d07d99830654601dfb70a8478203ac085a', 'admin', '2020-08-13 21:40:14'),
(4, '30852b488e2e22dfec093c21de80e862a8797fe8d80b4fd3b153a1242e541dc84d7416585abc1bc71d20120e8b412965b9307d6b420cd9d8e023e746b73226ef', 'admin', '2020-08-13 23:56:42'),
(5, 'de979232d8e37064942839f9a309ed696e816828b9d1f55a9d5bc8812a023e0ed62d933f5e0b1406e4354590e27212e0d37143446c87d69d7edce7b414abb24b', 'admin', '2020-08-13 23:56:56'),
(6, '6cd685cb81676cab7be82decf224c467bcf36b0747b7abebfedc6b5c0426aae26cc70c6b3dc84efbcd02da3ef1e5edb0641f95cd87ae8ae33a8ca820e079f1df', 'admin', '2020-08-13 23:57:00'),
(7, '40544916cd4599c322008eaceb91d6ca2164a864a5c6aef49c045c5d55029404efe93e3678f47f9ff5da466ce396380586327b7649cd254ac7069f1239acc332', 'admin', '2020-08-13 23:58:09'),
(8, '703fa80c86b7430abf6409121c2b4cb2a9fd17acae1fd63b3823bfd75f04b12b69d3c813f613b76bea37900b4398295d4ed2ca0eefd98e8a6167ad9de98a5512', 'admin', '2020-08-13 23:58:27'),
(9, '4c3d03590892f488a6cce6aab2f29696ab20c96e03b74f9708cbe6c8035e9b081fed6402c75ad805992c1c0c8db94e650d0df07fdb31d3442b19eafd5e1eb57a', 'admin', '2020-08-13 23:59:38'),
(10, 'eb49566ecb46ff576704bec85c4b06f19ff3682a2322179c7d15b3b1d17b1e78eb09a8c699b8b944a0ff1084bfa736d947d6cfb242af111896a9c039413c4d9e', 'admin', '2020-08-13 23:59:51'),
(11, '5eb633e32528d2a5e40991eccc059ca8ee310abeb25ab13d9435abe3d2b036c4a1a4d3b13abf8bc0a02490948153466605c8d2fc5cf18ff6f5fd3d97607bf87b', 'admin', '2020-08-14 00:01:48'),
(12, '581c855487c7d7b356970de8aadbc4c11647dd9d02ee36d885bf7c63507a42f9562d911f7c638fcb9ba5f38ccc1056d831c1948d7484a7f6582209dc9d7b1e31', 'admin', '2020-08-14 00:04:15'),
(13, '1af5373dc18dfc61b281e2c28548acd0f8e8bc47ca49c143af853c4a2c47644a3546f70c13380500ad895e52dbfb3663e625d8e45232829c8d5d657ab3140096', 'admin', '2020-08-14 00:13:38'),
(14, '0b0474d1d47693aae742016fee9aa40ddece30e61a2ad123b69f1c904b1448631a2880426128b1d713de3080c1c9e7aa958d4d961df672573342ca5c5c7e2878', 'admin', '2020-08-14 00:24:08'),
(15, 'bc3ea3c373196834e63cf7782063a92c17cf77ed6e4f4c722b974e47ba37e36b95f92f089f753ecc453dc3ae8420c7ebc219be4b4cafee5765040f1eecd1e985', 'admin', '2020-08-14 00:25:22'),
(16, '6d808b91c9b9c1c1836366a8217affa232c2f5a0bf5af68501546aa7b3fbe22a1676f19357b0e66f7f27df6ebed6541b469a2ebc279b87acd497cdbe04239736', 'admin', '2020-08-14 00:32:56'),
(17, '9a72ecbc0659fd0ddb29cbf812dbf8532b1205509d2bb41396b9962baba0de1cd5927235fcf965076e4485f07e9b14782d09f8ed3201ced6904380c31269ca8b', 'admin', '2020-08-14 14:06:30'),
(18, 'f925f1c10838e712e8765508506133a26d50a35edb4ed1137408fa8d673d8f7caada8879925feeb5746e9395e67d3f54396e8674fce2a28671efe7f8151313d9', 'admin', '2020-08-16 18:09:02'),
(19, 'fbfacc2bbede3e653336b9afa59edd73e797a84f6fd8953dcffd2cd03e723da8a164f52d43d8756b0f08f3f2f1e7e0604afdc93b2051a8bf09aedc2724425619', 'admin', '2020-08-23 18:07:22'),
(20, 'd6cf0bc1732b94ba19ba5422853708860f6c4ac66f761b63e3467e456cb30d945aa4fc750be774693873699edd894eded4ccd4605ec215065c2b4694245a1cb0', 'admin', '2020-08-28 22:33:25'),
(21, 'c45759a0441fffad2b5e286e342107789cf8de3ec730fe64823cd90b90b810af8cfdb593a1bedcbb43283c51d35e664d7b61a32533d4ba56f772b42bafde887d', 'admin', '2020-08-30 13:33:46'),
(22, 'a78a9164f9b2c8bfc818a4825aeb71964fbb340c9a416a81188d732d6e7a74459f48043ef057ae680a981d43997d8d6798f58f23d8a58f1be950811c8d5c3f5e', 'admin', '2020-08-30 16:44:08'),
(23, 'f5646c8eaed7844e0220a00538a59e64051c465868dd6c68eaee6cf712fe56060e7fd5375cdcc7d82de4f3e2e89b94f521ad032ca9ac5193c599822b94d5d9dd', 'admin', '2020-08-30 23:56:51'),
(24, 'b290c7d83666bc3067b4a20102e906ebb7c386858b61f0ed665f386303907f11c0ff589f15ed640051a2f36878f6cfa61df1949e4d2c312eba616b4d8364460d', 'admin', '2020-09-13 16:23:40'),
(25, '4153d3d672f9f8a798ceab87c5b6033d6e818e77c64b9d029c9cc6f87c4aad063c155befe14fcc98eede87234d1516ee18ecfbe324d82f51fee5c9bb41100292', 'admin', '2020-10-04 15:04:24'),
(26, '2ea9a110e6b7d94634079647299ada922ad47e0503ba657a8b87127a39e210cda67e17e56e5bae0d3e24ecd398361b7b1bd3860d5f1164de54f294771853cf4d', 'admin', '2020-10-04 15:17:21'),
(27, 'ae07f4b57c1ae4a4bb5baae7b660b7f80f16f6bd84a5f7399a3428b4a191744c01f3780b0058e6c116f9fc6093376aba3f9a6f3b2a7931220e6a4d01f60025c0', 'admin', '2020-10-04 20:11:23'),
(28, '032b9354f45eab358407a5e1b33ca39cecbe416a98988c6cfe744970f722dd959553f4bdee4a70a521ced788050bd3d2cde258f430dcf6f3ee40c9a138ba2e7a', 'admin', '2020-10-11 16:02:08'),
(29, '2b616fbbbb3998dd318db6d09b05b0471ef3722a0b6b65b704d2bca375e2c296382883c3acf5e1ad0dd351dd089ee135e0c815fb64728f0acdb96671e7b64c94', 'admin', '2020-10-11 16:02:51'),
(30, '410e3484ec789d5dfe474cdd19db4f681b2b912f090e018866246a05d79355f3409c75a89814fca6a91e7a54af2555c35b4058157f827beae2e37220b76596e8', 'admin', '2020-10-11 16:06:02'),
(31, '69cda7d7163c8358f8d181a1d02f6240b0cb94f82fae5c927ad2fc20b5ac612ec24725e49ddb7edd5af0e8fb687db213513fe8c9dea37bd452e3db993fba2d87', 'admin', '2020-10-11 16:06:32'),
(32, '98c4de80c826d132dea0c657431ff1dcb8e97b6d4105f52133f3bfae6a42793b5310beb03d2f9ebe2470e8c3762abba913cdb0a3ff61f4d90ec467009fd821f9', 'admin', '2020-10-11 16:06:54'),
(33, '6cb181f25bc62bc5e164f2730da2c36c2288689e7101146df332bad04538bd3826542bb193a1fcc58ba02a39acf12949404eb7636ff6fa23894fb05b8af23184', 'admin', '2020-10-11 16:07:12'),
(34, '656477677552d9cc511b0b9bf468fe44ff30123036b0cdc5c172151de2176c4db17672a8f0ba57e7e1148de2517e09a3e50706bbd37133ea9c3bff6a1d15e6ac', 'admin', '2020-10-11 16:08:26'),
(35, '858aa6cf39a4b016708c44a06b759571c71e54df6ea4de49333cb3244fb58ca5eee4bf527c8d9452439bfe81953e43d85ce1ce59ef35a9661891916491d44c77', 'admin', '2020-10-11 16:09:21'),
(36, 'bd7f59774c05443a0571d42a8a0ef9b1d45f421dca85383e57d3b3efa926bb1e9481545710993e4068b3495abe4aef27cccb3a04f4c4fe1700743b5f863c9ee6', 'admin', '2020-10-11 16:09:41'),
(37, '3bcf676f3f71d1260017fa9bfca5f6e88a78fbd2be8b2fea741b62a4350a0e5a92175559a3f10c65446e42c345fd2c10fe39e1df78ad7d56c340be3b85d4f1d8', 'admin', '2020-10-11 16:10:44'),
(38, 'a5a2f933960f745d83b125a09d26420628df908c2b4047d7034f9037a3c0738fc0b8c31152518ede1170f45a8faf91aaee80399fb199c53c0ebcf635b4097b50', 'admin', '2020-10-11 16:12:15'),
(39, 'e282f30c69565c919a019dd976a889db2110a39ed4a1f1beb4bf093d92dd383c1de45ab6eb29f5a6a4af1b628f8900ce7b3de4d2f138a4bceb3f4e1f76892854', 'admin', '2020-10-11 16:12:50'),
(40, '72fef162a8f87bef4448020b5405f850a1163ac3df14abb1fa845275620df73bdc15748e4f94a3b0fa7c41a036b861565b0f4b78b33c83956d96ddd5183466f3', 'admin', '2020-10-11 16:18:01'),
(41, '19e6fb6637be0fa3da8a71516a3b9e42b34bcc5cfbbe40ac6578cb10c3df24fef4d63a350048f589c15542caf216ca8468a01d5f432315076b18ebfd47afe667', 'admin', '2020-10-11 16:20:44'),
(42, '7dcf792ad32a3a4d6b4ae2ce2c728266ffdebbc458d326711389453131cd8355d4f81d46cbd7234492e91192ff3e6fbca1403a80c12c325ae6f8a1a7b5ce8b04', 'admin', '2020-10-11 16:22:19'),
(43, '315557ba705ad735aaee69e2f037993774bd048080212cd2966989d88340dd13091e28f259ce0cf7ffb4ed52abeadd91c2719f3ae8548e96d5f87c4df98124ec', 'admin', '2020-10-11 17:02:15'),
(44, '87c4fd08a152bfdc3232976ed20dd3fa646f3760ef24c884b1ba3cd48bcfd72926e263dca745880c6c4b18e9f0e58104d0b3b515ad12485d6cc379544b4dc02f', 'admin', '2020-10-11 17:05:14'),
(45, 'eba3acfe3a66efe62f3b163c4ebfc56fea9d4d803a6569300f9999f70364112c5a9d2bbfd816e51f726022252d2e5ce7e1243b2adcc01aa726bcc8ec52204fe1', 'admin', '2020-10-11 18:21:14'),
(46, '2e483cf020702e06a973e50936c754dfc7a6b411ed49da0c2460444a359536ec2ca79bf7735d438a3270187e54fa7d4067b211e3bb58d77e29d6888667aad0a3', 'admin', '2020-10-11 18:21:23'),
(47, 'de399b508e1935b00a7152825b65da8f7e234d1cbaee1ae4577784d5f57f7ce29077cfb054b21684ce0e443b78e67648febae9bf4237beb8ddbcce198608e8a2', 'admin', '2020-10-11 18:22:02'),
(48, 'd104b5a8ba89ecf8b0faa61238a5f806f57076957c19e00ac39f4d164415d6004fdd061356739168b0aa724fbad82fd03b1462fd135cfd25114b38eb6af21237', 'admin', '2020-10-11 18:22:16'),
(49, '5d15cc3beb64ec373b9f90285fe4c1f78b5f549c99124587a95e671fd53b32370d614e3f474690cc659e794d2484364e5c413ff3e86b59fe1f53c7eac31ba9a0', 'admin', '2020-10-11 18:23:10'),
(50, 'db361b314c11adc76f8da942f556792f013e51ee067e2aa7b0e93eb2c8188f9bea88b35b2398102329d11362c902c8aa3082b8a03e3a71b96874fd3a71899f7d', 'admin', '2020-10-11 18:23:40'),
(51, '0b25724a5efcdecda9395f97c7f53ccd38656e9368966bba0490f69289044f4e14337f241b45465653da4ec1b595f96b9460d30568e24d1de6b275a19b6fe581', 'admin', '2020-10-11 18:24:49'),
(52, 'd6d78b5da91860c61ebad2dbd6713ceb3177074c254c5a0b069aba94f641826a44a6a3f6dea38beb23c238844eb3a392650aa7ab4adb60a925bce9f17c7fe0b6', 'admin', '2020-10-11 18:26:47'),
(53, '7b2c95e798d1795bcf31338895e697353d408fd68b1a2a3926c55942343fe6dd814d18b9666ededb5e81ee085f8dfe999fc82bcb533dcc66a969d301e08c7366', 'admin', '2020-10-11 18:27:42'),
(54, 'd6bcd3ed031921d65162cde38d0cf2fb92b5aebb0a81d552a6ba743f1514a06ea73c44e867c41388bf2af363cc21174b2273e4505f8787e7c8af710a35708cba', 'admin', '2020-10-11 18:28:47'),
(55, '9fb4161e2ad740d08e79c258c1606d0277dbe35e04683bc62395f85c3c4ff4bf2bc8384177290d8bf0038bd69b9b06ef203b2363b80556ac7179a6eae1878976', 'admin', '2020-10-11 18:29:29'),
(56, 'b6d0fccdb653eb0ffd9e279d8304a145ca5399296b5be3b8ec6747e486ba05e588d1b7d3d68e6f825165ac4a0423ef5d644c3cd195c1677dec705bb32d959d86', 'admin', '2020-10-11 18:29:47'),
(57, 'bbeeafa1088dde88e87c11436f1a97adb8da68fabfc2bbdf31a01d3f10de119e24dc07d62fd648d1a981dc1a19f43320789c64fbc2faca84e5004e4e3e9ced1d', 'admin', '2020-10-11 18:30:19'),
(58, 'c3c00bcbee4c7adc7e61bb732b32dd4d5d8188801646d633b2dcc7a2952a10f83190f4d8e92bf4aa202c98417d559dfd9d9144dc4e00f0c0f7c8a8d3a8393881', 'admin', '2020-10-11 18:31:25'),
(59, 'c5c15fbad9124ed825221947a92403efc51d813ae9623576c69987db8b025ec73ea5cd7a8bed37db1e48e1e12f464238f0913033012d5b4fc0740be03f151f8c', 'admin', '2020-10-11 18:32:13'),
(60, 'f7bb8794c567b6541faffdc024d3a335aaadbc049fc26a0f80c142ec55c64e8a27012b8d1c7aeb44b7c12b081dd46017a7721eb5208b6e514ef20a92bd680168', 'admin', '2020-10-11 18:32:29'),
(61, '951f56d2020893c5355e734528a54258f93dea95841a460d083ee88100f7006035913ebaa46ad7879af715b55d5091853145841e3d34b57cc5cd80edc8d08fae', 'admin', '2020-10-11 18:32:52'),
(62, '4e71064062cf5179f6eb326f1c76d4e052f8b34c396630bbeddcdae7f2ab0e2d303c503cd5e82df8b17c6c0da8c575148ce5fc12b687f58574f401bf4fb0bd10', 'admin', '2020-10-11 18:33:37'),
(63, '352bfa2d21db92c3d845854340189ac5e1f17e3544f126cd365caac88ff746a542e019968ed8b886cdaef9c337e57bbe44a1133ff5648c0dca32fb900e1b5bd8', 'admin', '2020-10-11 18:35:43'),
(64, '88e38427789ce9ac076398f5fd5ea183dddaccac6278a86f04966ac40a47244a4904a10a233f9ff54f8e3b12c20168c838ce7f4a56602e24bac07fcf86804d93', 'admin', '2020-10-11 18:36:09'),
(65, '969bf5a5abef8fe90dbe4c7757f3e0502cf1ae23de0454a654750af78e9760de3bfad1f194a0242870d2a095dae8c45cd3b5201e7b1051d1f6fcecfe183af8a9', 'admin', '2020-10-11 18:36:38'),
(66, '9750f93a708fe6a9f5489aa0fa618d7311ee272112527ff1c98a36ded1d3a5f968d9f04a1a2e8b3e8b21b2931cb3fb0344c598229221e856110fa625124b11eb', 'admin', '2020-10-11 18:37:08'),
(67, '6faff19dc2208e1eda3e230906e80f1d3eb5a6133597198f76b95f16b1df3b3ea769fb1db30c7e01dc19a95bdfda0411be0319dceda5f3336918f4bee40c3653', 'admin', '2020-10-11 18:38:00'),
(68, '2c49dab0b60532441f8e33c6c1a16458a3e82cb4e4dd2397b3a0dcacb097e148ba6999dfec2bb60cab7d131a58debb8ed3d575b4da55b3ba5aba3487f8fccb5f', 'admin', '2020-10-11 18:42:22'),
(69, '7aa96ec23cc141e2c1df09a9eb294aa8eea1f6839f4c151ae9601f2743ee2148e188ed56d9da81afab411b60922ba56cb9995cdfc80d77c933c183e54693d224', 'admin', '2020-10-11 18:47:23'),
(70, '2bf7d4c5191ebb8ad2dfa853de04449eae9e9fabeba383985c23126f5b04105f73dca86d294751fd1d23cf6b015a191720e01fd6fa50fdfc64a4820e0dea409e', 'admin', '2020-10-11 18:47:33'),
(71, '79db23cd9d1ae27fc6e921fc17b00a9ab731d6cae8c69106e26e39f98f527bdc5c8ed6559522c5bb07c5ad209e1a43269c014049a537764a833575365035012f', 'admin', '2020-10-11 18:48:53'),
(72, '1ef6f17c6af007348f9ab2ef28088725c57f8c180d2558210ad89d4611a602310c9d0e9698c2d88b7638c35dfba72dbbb7dc67d1ed989e4e58630ad474d1653e', 'admin', '2020-10-11 18:49:34'),
(73, '5846b935ca4189ebce1c424893d90de065c4ee7d61d1da3e18dcc872338bea2949fd6f8e9a948b1de9123737cf6bfd0657313a863d8a10fc091ad94379caeaea', 'admin', '2020-10-11 18:50:34'),
(74, '7a06c3c4cb51acf9ffd92a2067c3ef324a8269a6233e638be764cbb218cd091b2a680bf0fb1e0ad304b72f32bfeb0a4c8fb5b65032febe37932e486c5ec62a70', 'admin', '2020-10-11 18:51:52'),
(75, '29678c12e9a7e95ee10f993741ec94b56ba35862ac1aacd9d267943ec11af3d71aeb97deb2fbef8fecf94711815621767f64753f01f7bc822c8b4c2e3709790f', 'admin', '2020-10-11 18:55:21'),
(76, '510b416b766b8e19ec81b885080d45b7cf2a38afa1019127a171af939e747a7c7cf7a83156af620eed0086f47263a2824953bc18bded5985fa2b2ea894160978', 'admin', '2020-10-11 18:56:02'),
(77, 'e8e9d6a54e715c680184826036cd381cc1a9d03002dc4a3d3a90dd456c62a4d7763aac383666a7243d3d0f2bd30f970741baf5f3b98911e481525260c44cf520', 'admin', '2020-10-11 18:56:48'),
(78, '3468b1a1f8716cab39cff9fed8b368af2a32741a96b6a33b9f86092e9cada416b6124a7b174c2c15b9c87e1dc40a7abf8a77a1c066d1c681faae9fd113b0aa6a', 'admin', '2020-10-11 18:57:31'),
(79, 'b8372c4eeb7a8ed5928f4f6d1301d53d1d8c9903fc095dad65f3bb6992844c7a031d8efdd0d98e0c82434a41a842dff0c75007a6aff977ff2327ff749081b04a', 'admin', '2020-10-11 18:58:25'),
(80, 'a5b704c91b5df48c874c0919d22fdf62d45ac76b5566d02ad421719d0ca966190760fd6d45a79087b1246a90139802d9c66a558d45edba5e447156bb35753b69', 'admin', '2020-10-11 19:00:19'),
(81, '78a5299f28789912775503b241e2459c4a287a598233e2d0b1d549ced1d13c8ef18ce14a47dfdf0dde8ffe67eef401459fbf1392b8f5cde1056de296cee35529', 'admin', '2020-10-11 21:09:09'),
(82, 'd5b8f590e8953bb958bd6f3d08c62101df40d018ad01fb783421ff6f1b82acdf8587058ab67b03022bc19565ac22c118ba211a691d47162c145df184af371e32', 'admin', '2020-10-11 21:09:20'),
(83, '7874215f93b9b80a208d8408a884e3ffb669afaf02dd94f12e373c4548db87c339656b7b5632057970bab509886141e0e3ffa5515023cf8e15cdb465c65e77a6', 'admin', '2020-10-11 21:23:30'),
(84, 'd50b889eef77fde6d3c0b9695f6a415a6ac1d40aa09f32e3854a8e13c4e04ab3139d82778562f77d436d372593956cd0fc744b7b1b6c9e040543a2cf94c14c8d', 'admin', '2020-10-11 21:28:35'),
(85, '547429c42a2cd01cadb45fff60d7e35f8da070683198a5ffae15a19c4f6961d18c47e5dd68791e78f66c2b2585aa8d33596d5c150519fbbe4cbeadbd6d6b98ea', 'admin', '2020-10-11 21:33:48'),
(86, '554b8eb41e232dd01c767b44da02358a480449d5088b2abeefaa4a990138c7959d0199593b6b1b19bcd0ec631e5c0004bad445afd2c5718b685eb097ff3e526b', 'admin', '2020-10-11 21:34:31'),
(87, 'c725f9c9da1eac2b97784fde02cbc0f402265f8e4a1c0a1bcc17d1aff91eccb138bb28700492e9314c683aa0dda4d48f96e28f0d52322ad983a49192a4ee5289', 'admin', '2020-10-11 21:38:24'),
(88, '06f6467616169cfd1ce1fb83a9cdedbd5ee20d40f78cf7eb28d967bf76e92f72408575839b417b9d31165463f2f5f5c1fb7d089a5e90e4a75269cc8f4fc6963a', 'admin', '2020-10-11 21:40:47'),
(89, '45b159caac3d7784b31298d8619c993d0d8e2af8082f283ea5986da88fe93662e98999fd99649a3d276c491928f10a50be6de6158c4de7975fc4c52118cbb242', 'admin', '2020-10-11 21:43:20'),
(90, 'a8d63cf9c8ba045790e1d6c653536525ae277619240e46d42a3a5269ca4644c89b05b4611ff46d4289187767b4fdd3349703db3ba1d46bcc91b4e27d7cd4cd8a', 'admin', '2020-10-11 21:44:12'),
(91, '110fe6a784ff72d473a355dad091ca49f0181119e931788c6dc2772b0af14bb15638b9291239ef90a215af37932ff5886979ffa0725541d7e5964f631c714987', 'admin', '2020-10-11 21:46:59'),
(92, '00590e516736990c96be02e50eb146b0bbf6f7b3dba38bf181c95c167a55ec632aa2c11ab23f6073076513bd55640a1c248b162e8f23474c9dbda029943c7262', 'admin', '2020-10-11 21:47:22'),
(93, '5b6545767fb313ce2f7090ff08aa532a65dde1d22c301b70376b676c80129fb48eadbf8618c32ff4dfe245936aac519f82f2dfe2d39edaab4036387a86ade200', 'admin', '2020-10-11 21:48:48'),
(94, '3842adf97a3ece47eefb993d4cf221c06177c85aa8f4af1ad2425421691ce6aea7c76cb1d68d617d8a243a49198f8028d5d576b3a000b4e45ed5ff7d2dfc4588', 'admin', '2020-10-11 21:49:12'),
(95, '188030d2e24ad5db2c003746c8d7ce913d50ce065b0b8c6700f6d8b46745bb0ce524f38d4477ea175ee73372042dd1d07d1da7ca0bfe32f62081315cd32fe120', 'admin', '2020-10-11 21:51:03'),
(96, 'f746d50c5999cb0db43e963395ca3a142e60704eee408db88d6f9f4c9ca39d2a40efd94204e9907a723b858015876f8d712d18a5565f53ba110ac13b69409363', 'admin', '2020-10-11 21:53:39'),
(97, 'db09aac3133f8de967be3545be0bbf50705ec3f868d4c55a62073885a541213f724a4fb0c9dd8a0e52e38b8111bffc4de3ef88ce524acbb01647eb99d4f03ba5', 'admin', '2020-10-11 21:55:45'),
(98, '9374a996c1167cdcb8a2ba3bc6cf0a41e12a2388316eefc5c01a0aedb52325873ef90302353ac9122dd1dab91d278cc7549d2e842ac6e1c6ecfb385466ae6aad', 'admin', '2020-10-11 21:58:00'),
(99, '7f69a93ce35512a117356998703fdd1d18b173eb13dccac7d21e5dafc840ee92709083621a38c9125cabd8c5c1252fb2fb5f6fe6af6819621d94e2a1140a5a93', 'admin', '2020-10-11 22:02:39'),
(100, 'dfb0061d613323cbab82e320f75d0e0b8c0300d8bd3d346813eb2383f7d5a22ec571d57e0dc560c462a3e0df5b6242bb5ad778b0f0359fe135ecb573ce1dc665', 'admin', '2020-10-11 22:02:56'),
(101, 'bed53350def12e9d793d24e8961b03f07df932e70f35d0718f7b52e3fdced44ed58819dcaf9baf482de9315ffafae31c4a5f0ae8fc4150930891c58e49c44deb', 'admin', '2020-10-11 22:04:19'),
(102, '6fa10f519e35f6b7366e82c6b1a936f5af2bb42c42ff430b6967495eca35e9c65d3e8fd678624c4f941e432d7b238c213bc8e58004286a9f68056b023ac56b8b', 'admin', '2020-10-11 22:05:50'),
(103, '7d78e21c3fa4e9704044c9fc00ac38415c91610e408d01e0fc3430c227652588e777a42c4a912c14069f15318bc13eb94a3937c138e71a502b1a70745c4d2799', 'admin', '2020-10-11 22:07:18'),
(104, '8c27cf8c59b9bc96c8530a6c8f9f1894923421b035a9b3bc383bbe58d8795da0784ae0d71c549a84130088d0fbbbc7398e67b1005c2433dfa172e0d884896d02', 'admin', '2020-10-11 22:13:36'),
(105, 'b53041c7638cde9761dc5ea483a5f4ab43b4fc497d199dcd00b3186ff00abaae8bf29ca9cc54fb1ed071311d0fc713106ef60230cffa1a3d2110a30b0fac04b9', 'admin', '2020-10-16 15:02:08'),
(106, '14502da069f2b90ee955039d85e257d2af760e902b450415674c869fb66b4ec87ef864f81224ce6a0879b33ead6aabd1e9967185cc908730bfb7af1763fbaca1', 'admin', '2020-10-16 15:03:03'),
(107, '83fc08ff7c7fedcdf9e8d5a2a9ca99586cf286ac84e91fb8b6bc1082a46fe2ee1864746607f5ab30459435cf0866a8d0951b236848784d77e11eecbfa7f97c57', 'admin', '2020-10-16 15:09:14'),
(108, '334cbe73837211f2ea1e6c3a4655974c6a754f1a8f5209b2384d34c0b78a58fcc079775d19c8b7af94990f1dd300addc878e42a0e22ab2bb87d31a8ff7e621dc', 'admin', '2020-10-16 15:11:12'),
(109, '2ac0b2153fbee9b7fabdd29d9b357105e551a97edfee6450a5ea8d916e04f550b738b4295e4849c6a34fd87cf53d29dc88ef5d216e1272e182d0da5f10c1ce94', 'admin', '2020-10-16 15:11:41'),
(110, '32eaf66c671fc4b84ee0472d726d0e3f1ec2bf904ec65328212127cc59b385077fe71c931a2f2c2b17f74d24e09982222bd8b9ae516124efe684c97bd148a464', 'admin', '2020-10-16 15:12:23'),
(111, '14a2f8dfe3dfbdba0801f61e3b8e22440fb4c4fe3a87014ac323c412fa12176b8eb5341f228f3318a9c5ea98324ccd744919dc09afae85067c21ed6014a7df62', 'admin', '2020-10-16 15:45:02'),
(112, '83cdad4bd82236cfc17fd87dc7e609852884402ab3f67d5705087a838e4abb5b013b31a3e72671a5727b8590f78b7e15fb0b9b19ff2d82ddd123a33beec2cb02', 'admin', '2020-10-16 15:48:17'),
(113, '0ba4fd7051b51b2f91a1c9fdbc5360e56f5fdc80e62773d4668b2f500480737a0e42724000dadda86a33da1e8295684ecec670495f76904ddd82fa8c2dff8746', 'admin', '2020-10-16 15:51:20'),
(114, '8a51509e6a269675b2c51585c1bf8d14f0ff0fe065e1a2bb2a91a2eb0e49e6b7b5560e45fece1a411f01231b70f3da18fcf29446aa7c3c6ea33037434a586f3c', 'admin', '2020-10-16 15:52:18'),
(115, 'e35656e797c06b371e8f9ecd9ea4d7f644662edf011f6b46140ec8d5876a6224c1a80ea48d9cbb757c10cf42044cb5d6316a33db84c1ec54dfa48f53313903ac', 'admin', '2020-10-17 01:09:38'),
(116, '5bd1c4120aa9e69cc41a63eb2f9443fde5356fe7baa1c63b8707cf8166ac71520af5c978955e7f08279e8b7bc7bdb6c184f4f139be05659f2ef53e20577d1dc8', 'admin', '2020-10-17 01:10:52'),
(117, '70863baff54ec3bd9c51e8c9ee885c2da6d2e20f5f5e71de13c3c3ff0fddcee8c64d0a15effdac915acb884b1f4962fd48293b2094333fb9ba504b5ee747c9a6', 'admin', '2020-10-17 01:12:23'),
(118, '1133bb40407af1405484553f3c213d7dda305f0d0be89a64be11fa97ed758b90e27870a967f0e612ce73ab8be3b5a9f34eeef2d5fb53210d6a5bad42ec63bf5f', 'admin', '2020-10-17 02:21:47'),
(119, '8edbd3dc0845ae1818f5f94e7ea903a0227270e3b5915a7e49b5552f71060b3ade03d5efabd72b2f5a2342bcbbcacd885e636e5b5daf2b7528de839cf66a4109', 'admin', '2020-10-17 02:24:01'),
(120, 'f543ceb0bdd72b351fccac230ae98f0517d4043706ba81f33c51b24c9872d475625b0fb9e8357adc3818bc18abc4e50ad50afafabe0e1b58416930b13951eca6', 'admin', '2020-10-17 03:26:34'),
(121, 'f800edf2cb9a3815afc4703603517e8d439739bb6d2b62b22a751cc63f88692e35db2597eb1a8d77a45446e2bb81ec57dbf4636e100ca7dcf57e9e270b71ec2b', 'admin', '2020-10-17 03:48:20'),
(122, 'd4a66a167e3fdbb97b438544c8543813526bd1a764a017d67bcd3505344e5fb24aef65cba5d18db2ee73d7cf2b7bcd40956c32898092b1e8d7ee476f347e160f', 'admin', '2020-10-17 03:50:22'),
(123, '5415414ed77863613feef88582f4c1ae84e51a3d7a15c89882bc1634797172e8b10e3673de032b9492c9068e9c64c79f96865bd54ba4ba60b3d2e3b386d7022f', 'admin', '2020-10-17 03:51:04'),
(124, 'e75b6c046712b66f7430a993a4bb8bcfc0bdc931e1aae5325745906c1316df17cfc6ea2314d692bc40efed0fe4bd4ade27c7fb1881e06e4d02cd615b2a048d36', 'admin', '2020-10-17 04:03:26'),
(125, 'c2dd05592f1237a535259762a5e766ea0d19d8e013ed8c5f980d4ba9212ade38f7760e392b097d532f9bc15869bb79be00fbdc3a075e3a4a213c23c71aba49fc', 'admin', '2020-10-17 13:53:21'),
(126, '10589cc51341dac4ae231416dde69bebff6a0cc325344cac68194cd62073fd9293f60c9a3ec7daf0a11b1084ab215f136ae5aa9e2ab5d25b86ac8583e9ec8ddb', 'admin', '2020-10-17 13:54:05'),
(127, '16809a6c093bb6415fc11f6271a0ca3aa926b220fc2da5e0e9e9eb6d28803230cd8a8db7473867a3242b91f9792dba83221bdc5a0d7006d867334a5c6238be02', 'admin', '2020-10-17 13:57:54'),
(128, '3740bbc593ac42f188962c5276d899e0708bb000ab9eaa5400e379d1df18ce2cbd2cd20be1bee2e91eacd98dfc0a27ad211059e1250b5821451da2abf3ace554', 'admin', '2020-10-17 14:11:30'),
(129, 'ed31149fa40b0443f88404e4c1ad8eb47c603430c068aa1a195b7cdb1df2074d33889c09bcedf19e955db28084ff4203e21c80babdca3973482f849859fd291b', 'admin', '2020-10-17 14:20:20'),
(130, '1251857af3673a5285ce33a466782a868a5e7fe7ebb7d4d18c15f3478aa70108411a0a75c84f3eb562c8a4063fb957d6e012de86edbaca86bf641a740b0ea4a2', 'admin', '2020-10-17 14:21:51'),
(131, '5d22c92c14d9b7eba1150932c943ab5d49fa7a983c11b13a1b155f8796afdb95eaa738b697bfd3d0674beb6bb31fbd14b79dff809d4e947639bd81c0ff7ed87f', 'admin', '2020-10-17 14:22:46'),
(132, '70c24ec231770ff3a1ab4f5254eeef1a8c29632254cd078831944169b03610762fbd7fe378ad9e466e7c847dbe24678d7b8b68716e36f30647b1257af418c832', 'admin', '2020-10-17 17:15:49'),
(133, '8ca66cfd18cc190974f1b1ae565360ac2b1d69864d2b97f23f1d0d28dbc2e07201dd26704a275b80b94e43bcb7ca16edda6181f730c09756536600388c744846', 'admin', '2020-10-17 17:17:08'),
(134, 'ab65fb0affb32a6fb9984fb19631fb496f29485116c064479659009ba74cd4fd6ce63ddfbe34aaf537d4348a29fe8bec2ae3f264a73e128f712005724bb05a6e', 'admin', '2020-10-17 17:23:06'),
(135, '6568aae9e13b17b6f2e5d24d64cd0a0e1cad90f685dd69584fbc14b2bb17e62c69d4a8fee432059c71852db8bfdd99ef8bf96e69853adc35d3f098545385a31a', 'admin', '2020-10-17 17:23:36'),
(136, 'e982ed9f03b78545be31da7d95639dc2a49ecc9499e68de0d2d6258cfe3fe264fd2ab4f17cf33af2e39d4ca2f93192764fb3f584fa05621f38d4d88faeab9651', 'admin', '2020-10-17 17:25:21'),
(137, 'f60049d958b625fee34c2db13b2a25faa8caf77b39f86329b141abc572a1c5d57c27c55347ab9d41112a8667d7b040211b28f36496f3198f7923e42c02d769b8', 'admin', '2020-10-17 17:28:55'),
(138, 'd3419f4f1e9592371d8aee110a66d9aec2e5c24197274ae62a4e6df61123e1c264fbf28478634f412f3ab74032a1f50428716da8825181999510d908ad6ef8d6', 'admin', '2020-10-17 17:31:30'),
(139, '7919690c498ae6964c3af18c1199fd4214560558ad77209db0b398180a94a41553cb38d95df5941e5f2b0ad8114760dc428eff1af83ef2a74f5f9222ffda9b38', 'admin', '2020-10-17 17:36:18'),
(140, '041d35fbbde68bf315e50c1e338916c0862a95b7e7ed61d5d430cc86fd386fa9071a6af249c51ee666e9da717ee03ddba1350a5108d41d6836145b36eccac567', 'admin', '2020-10-17 17:36:50'),
(141, '1cc9c0fb3d179a2d301b3b4267f13b33d3583cbce35dd4d68f467d990e3553c1d4234e85c2c8932efd999a20be69d3bf15571c17522d104355cf5d3bf2f41046', 'admin', '2020-10-17 17:38:34'),
(142, '972a013763bede25cd5ca5e05c9288a378dd386efaa92413fb07bb597d92d2ad8ea42b0c8bc4eddcdc3ca3dd69f8fc81efe8aaf8dc2c461142f73962f0f4c245', 'admin', '2020-10-17 17:38:47'),
(143, '0980d009ffed27418251c308fe15cc6610ae9e15630d12a87267930f7b92fece7018e79bec84947096ffb9aed1d8fcec93a50aeba0fef128be663427d38aa5f7', 'admin', '2020-10-17 17:39:20'),
(144, 'b9c96c434ddc27ac6de2b91bb88a848c1cc96a12bc1160284efe5901e738aef8bcf7f66ed77f21b9305fb88b6368dbbdf3df7c12381238a72291747fafb3e4e0', 'admin', '2020-10-17 19:47:03'),
(145, '9cfc0dcb1783e0054a4f74e563f9e1901283f4e279cd1cc86afb4b9db58e72f5c176673af9b55dae9e3949daed7e22c23e0821f76d00bad5085e19df6114fe0b', 'admin', '2020-10-17 21:53:11'),
(146, '2af5879e16434a663d8ac3df3af872e1e691f2f5743d776ffd3d9702f5020862240027680be8345d5980fcd3f8f2bade489001965c41d5b31b60a74a1946e819', 'admin', '2020-10-18 02:30:51'),
(147, 'f79bf39a8296714bc674cb66964e6885187a2016250f978103d1cbdc072ec4aeee1f05f2ee729d9625177c3344e7894dcbca7056fc33dde235a7a6a96f0c71ef', 'admin', '2020-10-18 02:32:10'),
(148, '63c7d77350ace41acfa492c56fb109a4334cf4d9491b2a4bf4daddaf35d0a9b2612547de58fb80bc70d326fa04ca8258abfac19daa3ed24aa9a8ffc6937a0f10', 'admin', '2020-10-18 02:32:58'),
(149, '0d8cbd60aaf6301817300156c075e0512d177270dbc21b981dc6e5e10d4ce06157d6fe3456180a077837289d12b12b89a709a8366d62abacceb815faddf55e92', 'admin', '2020-10-18 02:33:17'),
(150, 'c1ae93a2fff15f0132bb77e8a9946111cdd7073f6f9c0d54b913861ab132234f3de920f86cf88c4d88704ccd6ab9799adf3a25cde898e1eeadb4989e9ec781c6', 'admin', '2020-10-18 02:34:06'),
(151, 'bf56d10e59c25a9f99a2f45a6d55707a49875de2b4c635b7562b00a8d20e2c07e82cb6187e64c7b0807ba34e6d8c23be0afa21b7f9ecef65bd86db0a20bb88c3', 'admin', '2020-10-18 02:35:40'),
(152, 'cb395e81e0d20e7469f829df0f0f309cb8dbc10a0fc65953b21a906ffb07ff6c945f8191e0fa01142d07e59afc0db04695155d53f6f580b6042d8784e44af925', 'admin', '2020-10-18 02:36:00'),
(153, '7a4597ffe093765fb1a3f489ba29669dded2dd31836b9c47c3e00311b7da26282090a2e9bc689c85ddf61424c19d1c4e9d3ff672325b926f38dcace505e596b3', 'admin', '2020-10-18 02:39:35'),
(154, '1521ece334d95c6f07589da6e9d66bf3b3c2c887b5025b38b18fb8989219f048d94820f2eefef6d17f65aee70da296b370087f351e65f342df1c2a4f41788d12', 'admin', '2020-10-18 02:40:44'),
(155, '38fdaaec7d129e9d935478b0cbf06376a4a70bb1003383db61897c61fb184a44551342d649b3d8d660b4d59e1767ae4e9e05c760b279a0ad42b6ef688b1a2fd7', 'admin', '2020-10-18 02:47:01'),
(156, '29b6e5236a61d716a8f1166831a93b6fc14194405777fb72ae1ce01ea71e22e27f293b9c990f636b90df8f5b0aafd51b662909aab05afaf740f21b1b1280f6d5', 'admin', '2020-10-18 02:48:34'),
(157, '602efd05de9c2810e5a69740673c545f9a673b383c402c978c141917660b912d685cfc91f2b4d62493aa837b013e31e2987d90fb20dd73926abbb14e1c416655', 'admin', '2020-10-18 02:49:10'),
(158, '9bfea1c3a52e3c2fc7399e60df76e4ac5440d0b18d58df08bc6aaede8d6d96c74e761ef5a55f5951b46d52fc1a8cba5e658f54919fb84844b2c340f97a15d140', 'admin', '2020-10-18 02:51:10'),
(159, 'bc4fcf2fd03e1fb5628b1b822e15acaf5134b9186717342188c4b5144696953ed8f9b8e92d3e5bf146ba0309b705ed8b4584177640759a824d6647c0bf74a24d', 'admin', '2020-10-18 02:51:32'),
(160, '4a0a260f1d59dca740a417ae148988a65f9c7f6cd8b908866255a3b136107482aaef1a498ac4e0ac4e74a217400c8674ae3e86c6d2c3d316e212cbeaa7f762a5', 'admin', '2020-10-18 02:54:20'),
(161, '6f941546522213a2eb9eec7e6105e0c50b02ecfaeb46f9880a6427685f38dd61eade9a9f356724f4db672bb7c5e892b72d2651eb90ab1b0fd3d98405a06dd24e', 'admin', '2020-10-18 02:54:45'),
(162, '7d06d7eab98870c54b24dbf92f82a56b9798a22a4c7b96e61382440f86fbdb824d44522e99104d970f1069f29476bb61a654f5615a20e9cbfd78f28c01ad7dfd', 'admin', '2020-10-18 02:54:58'),
(163, '571e3c3ebc7956a530d4cf297aa67eb770a5bc648c6dcef81c815b43a570ceeb366a627c04264e7ca35b40bc377c7bcd3ed46469a291ff95702eff2600886b31', 'admin', '2020-10-18 02:55:15'),
(164, '367775692645c2753b9893ebb8dee7e543d09995ebad3593c79820b4d714b69a19255993929b09676e227b600abeb81b8467ae8e49a0716605162b4277675fed', 'admin', '2020-10-18 02:56:31'),
(165, 'ad7e2a13bb568ac441b69756e56f65428918ef1d13d229c4a9610ad69a812f44ae217a917f6c2d4d29764a3fb89e324d0baad58d147649c43eb26f0df22a21f2', 'admin', '2020-10-18 03:00:36'),
(166, 'c7a00837f9287ba2ff125b5ad014ae6ea79c563313ad8b38bfee4bc281bc7426c6742b39723454896bab6a37b75fe0cb40a0c71f382f5a1d65269467f2a0d22e', 'admin', '2020-10-18 03:02:24'),
(167, 'aa0225ac1e16ed7a11d960bc490010c54deafb6d7bcc5ccc55507fdb3fe945f62fa2caaa49c13fa70ce382a87d093c8326f2960ae542a0528b284a3aa2c324a6', 'admin', '2020-10-18 03:07:39'),
(168, '998de6d01e62b1365502e13f56a73f95dce8aad82b9e27ca79da6fcb9466b09ff8942030ae9eb639726666d2f95d29e77df633a8e7a8c71330f7b3aa69ce1e23', 'admin', '2020-10-18 03:07:55'),
(169, '4f2640431400a08fa3a6f5f792bea98c7087862432318fcfbd113d38ade4ab566d2a5331889b844ad9e55b9c645ec949f655e916edc8736f31802b45ce9d254b', 'admin', '2020-10-18 03:09:01'),
(170, '5ec12ecbd2ba62798706c35ae3edfd8e22e0b6a4d496a85d0dbbe7d66974c17b802ed13587b64ea8a7cd10e218bbbcc35ce8f5cfc7b83c395b4fb150ce49d9d3', 'admin', '2020-10-18 03:09:18'),
(171, '7950b5367ae2417dfe461290138e4507c807a2e348f116d1ef53d490a5d05844205ea4131c7cb90f0e9aa0133eaad55a73998600aff6bb7202bd74c60e3d3079', 'admin', '2020-10-18 03:09:39'),
(172, '5817909ad84ad2666cc88b1a06f08e272ce53b321b3d42a17122b418d615c103f6bb5c55f8abc8717076bce670e7825483397f8aa0828aa85733cdc482843d0a', 'admin', '2020-10-18 03:09:55'),
(173, 'eed5c8abecef212b192a1838eb105dd33cd6c3b9615c0564930ef1afe6aab3d755b774b52767d85b3e33b272d036cfd66035f44fd2599618cdb5a4fb08ed95bc', 'admin', '2020-10-18 03:10:59'),
(174, 'e56dfdc62fa5e678683ff42159ae74ec4bb076b7e661c9609aa1de4d47f9f762d8710cabcf784629371d8d372f11a2246e561b84bebbb793cd67b7694f484fcb', 'admin', '2020-10-18 03:11:42'),
(175, '0c6e4ca42e767e52343b1e8ee733ad1f65354b6dd2f87a582aadce663283cb9988a187f19607f06d2aac587a2ea5d0488fd0fe83d0238d10a138a05bbc445a79', 'admin', '2020-10-18 03:11:59'),
(176, '589803e17253e7ae7107e7167d30e23a8ae45405527492897e327f0c5ad034db277b781bf1f46ae62278c27bbc1c881a97cb5207a48b515f45b4775847b353a6', 'admin', '2020-10-18 03:12:42'),
(177, 'f534808e4bfd3a649064eb1e4bf1b4ada9787e9f3c101128860d363155f96d902eb8e8a13503934ec74b13d1e1afd2161e1be4a61732f5523a84ce04dbf27dbc', 'admin', '2020-10-18 03:13:49'),
(178, 'e7beb9dfdf4db5814c9f6cdedc679eea99649dc9b97fe31163ff8c1e9217077cb2e266caa999481046913e2036f0c62ed91d21174edb1750a774f0507736e35f', 'admin', '2020-10-18 03:14:15'),
(179, '89b6ee66987a8924ddb265f22ef585db84936dc51cfeae3f3a604dba2d69a288abe4ffa46682ec6f2f686671e4642ffe1d7a5fa4b05f43cbe20ed8f6087e86aa', 'admin', '2020-10-18 03:15:28'),
(180, 'ce9af1013547afa8b3622cc6e1c182c103877c6657ac5a14339c89ed2f1bef1a2704efbd29e36a074d267c3dae9d837c8d5e2e7915192a73301ab4d56dd6777b', 'admin', '2020-10-18 03:17:07'),
(181, 'd6d59821d809cc041c3aecdd12f519d48f02d6f753b58c71040c8b4ad01ca6b26a0630556d416e12b81fdf8219307b10853d00562cf87fdbdb621f9a46eaad4f', 'admin', '2020-10-18 03:17:38'),
(182, '8190921a709bdd3c9d55ecb688c879616c1ae5c07fc872f4250976a4cbdb62c54b62e0af501beac861116380b1b49cb0613baba9ccf31837f748ed6f454834b3', 'admin', '2020-10-18 03:19:53'),
(183, 'db70e8df38e77c9c358028d9505bc43ca111996396951bee334296f7daf3e5ccdcf607dd4a032b0104c6ad2b9c725a3bfd5f467ecc5e12c5ebec499d1558872f', 'admin', '2020-10-18 03:28:06'),
(184, '43485b2449d1dc9308133fb979583db8ce4d99c3eb5176f90410d9f06e990895380f7980f6bfc572ba6c68a33fbc831f42945669c6a562c63d0cd5f0e597ab6f', 'admin', '2020-10-18 03:28:40'),
(185, '6b138ff6a888135e65721dbed906d88006731fa5f7a9fb9a4512a3e43dab2767fd9d6dd42cd44299f32c43b5f495dd0914d9d0bf0466e184f48d267b400d4b6c', 'admin', '2020-10-18 03:28:55'),
(186, 'f8187d113bc7adf18efbdca49a96fae6a53f1097d6d759a341853e63d6f464f526a896eff011124baa97ef8e79c00b5ddcbbc25225a479fd10bc55294aaecb6e', 'admin', '2020-10-18 03:29:10'),
(187, '04967d8b8a0755b15c1a5080c37e65868de4f8eb6531fd13ac944b1130159dc93dd0083b7385dcfb4a17dce8b0791efe9d04ea63abadf602fd5032d8cce5df4b', 'admin', '2020-10-18 04:00:43'),
(188, 'cc1a61ac9febfca9b2dc40bffd5c1f3d36a51408efa2bb9453d82768d45895387836abe3a0c8a79fe3a4171d7f87b44bfc3dfcab9456387af553cf1c699d3a2c', 'admin', '2020-10-18 04:00:56'),
(189, 'b43448874d7919c5ce03ea8f93a2eb4f0f7c93c3f289283c9f006e466f5013a00f04c831d9db7a2d76101067134446c96c2c359eebf848a657cec4b342d56dbd', 'admin', '2020-10-18 04:01:07'),
(190, '2c4419a59cf4462a591b1d42d169fbabdf18930c1e31a64d8c0cd28b31aaf0eb0aac08a25258f612e7a3b9551d59523d02efad9612ed4822891cf6b815dc4571', 'admin', '2020-10-18 04:03:06'),
(191, '29491b3cf1694c33e39fd95c5064b54e5e9be20728e1897e03608ea3082573e5636f155684ed5a7946af99a30318f226e83f8a3453e60f6901c57b305e8162e4', 'admin', '2020-10-18 04:03:39'),
(192, '34ae0e501857138e6ae43832bbd2fd582c1a8f7aad0a735f36b26312a0d424d8030b506a54bd84ea5173dfa92735445a1b6e536937c96e816585c0c9487b78c4', 'admin', '2020-10-18 04:05:52'),
(193, 'f768ac3eb4893be4fb074cdb375e3de76d3ed6cee7fb1f61e459e0bcc036490c72579ade7a8f513ab494f228da955a10ff273fcdbd6fc1e440683b65c1f99fe5', 'admin', '2020-10-18 04:06:24'),
(194, '9110133f1f45b1b2987680095e79e19a908d0e332e0e624a5702b595a0c07d29ee08086cee6d1215d0a49780e90bfdec864471968264899de20a6195612c6a92', 'admin', '2020-10-18 04:07:11'),
(195, '764c7dcdf8a499f7c6daad0d07b7b4fa5e2719a24c25c2a38370a23c92bc70e254c53e42a2eab0e4334c74ced0560daa5295bb2c486fd044dbd5acd11d98a509', 'admin', '2020-10-18 04:09:09'),
(196, '6d1b82da9aa8106680c33b3d4e8906e704f567fd3aa4950c4a7405e8bb2dddd991aa03b8d122f313d21918c0dbd43c6891dfa5c39710559e3e7183092d13ab40', 'admin', '2020-10-18 04:10:15'),
(197, '833e2cc00fa67ebf194458e2bb7ed9b03b0c1eccaec1a00c07d86d0363a52441f8c24b5a4c1869eb4b1987eceee6e47b814b9760d6dfc91326f3a285876eee00', 'admin', '2020-10-18 04:11:47'),
(198, 'c8cb27f436039bfb117a44e53fe884c1bf6aa61d8797d0c302b5fc16920e489512b8f0e1aa45e700d3aef4f0ec05ae58fbcf939ecd75e27ba8d99b84d5886aa3', 'admin', '2020-10-18 04:12:28'),
(199, '1b49b06a4f55fb34e80f089aeea54d9c0eaef1fede4e6beadfcd3cecd03bc3bab0fa84cda070a9856a083a21b7ed831ee584254a82167d435a76a406cc9ecbb2', 'admin', '2020-10-18 04:12:56'),
(200, 'a3e47f0a02512175735b0d341615f7c485b54dd2d37188150122ff31da4e77042a2d51696fd72bb85b64c81fd12af9f9f74859524347dd4a62a8691fd418285a', 'admin', '2020-10-18 04:13:37'),
(201, 'c00b0ccefab956fbb227c643139c72c45f5b466bf76495973be33742793205fb8b0cf6c63558d5c4ca2d92a41edfbb213f40484614efd460bcdef377ba28a191', 'admin', '2020-10-18 04:14:06'),
(202, '0c47558345ebb846571f5ea3b1a20a298146af51f370d835a093af2f97657cba489f651664deb19cdab83268204d1c746566a4fd1d80de0463b6e567f37196c2', 'admin', '2020-10-18 04:14:38'),
(203, '147ae5b20ab5f31a1e1832bdf3b4ee78745d78a55302444d58b4651d73148663c9c3cc5ee96214821bb1de5b28377d1583a166a129fd8116be7292ef58899c16', 'admin', '2020-10-18 04:16:37'),
(204, 'e85b0d6aa304deea7805481470c408e9d5ab70f46168dd542ff1ed5bc9688557974d45e3ed316dbfe53ddb4ce799885d64a97b540c2c60ede9c9e23cc87c5b3b', 'admin', '2020-10-18 04:18:06'),
(205, '2432987e5793ac24f3cde6ef56b42f66128c60bfae118fbffa8fffee49639fda6d4330e5d865dac2590bc045a9f7a887225264ec66b876ca336b4c3b2585880e', 'admin', '2020-10-18 04:18:57'),
(206, '440c9dc43331545cd47d0626cd2b5e5e2f5dbf7a433555bf448e755b010f14ed0acbed509c3f132fee6c29ab77f10ff5a16a0bdb95c72d5217f7ab5435dc0c4e', 'admin', '2020-10-18 04:19:54'),
(207, 'c2e6707effb5ba700ff1aaa5124453727a75cd4fbd5583b4212f08301862fcc6076695a462829e78dcec51dd84eb672d1adbfb0e981c45f1b80afdd883bb1ea4', 'admin', '2020-10-18 04:34:15'),
(208, '8bc04e61da4b1bd2e187b7a31011cc086201c8bdc6b29befefa50d0841676aa5659954653308b5cedc0a482d32329cc155505564e6d6820ef82a28fc9b66177b', 'admin', '2020-10-18 04:34:32'),
(209, '25a49a76fd9219a93ad1b4b05079729ae983a5a7255cfa84028d1488136473433d7e6970b9b2b7bf1574ca8cb8d0013cde336c2a48e3d8c405e7bdf02a99bceb', 'admin', '2020-10-18 04:37:05'),
(210, '3dac7ba1440fc145bb5cbbf1b20d51352044aef5744b92356e3ef21d954b3a9f6217f0c14cdc5ba7d94ff1fd1f751d7d0de84805ce08cff5f87ff0c0ddee780b', 'admin', '2020-10-18 13:18:55'),
(211, '15a7ce46f008763de0e8ed1a726abf433c3cee434dca71f49429eb19535635d22e9b42c825c1deb1d5c5f7de1a73e16782baf76460b397d41155a2f0a3189206', 'admin', '2020-10-18 13:19:59'),
(212, 'a0f089ad0cf15f1251587ca5e5bb3d95bf07f3be846d79577b86bc71af32bd0c23c1501f74805013e00b1b95c16799734e6ad35d97b641ef84b1d5f84da99123', 'admin', '2020-10-18 13:20:25'),
(213, '2300ed9ce1bbbeff3c9ee2d9124f9aecb59e047a730cd02930ecfd5b90a7e6dfeb1e0aee3792118a71e16d0d22b8869491f41db3d6d7c01fbc7f30b6eeaea124', 'admin', '2020-10-18 13:22:07'),
(214, '6b180dcbe596de81ef4ae6aa5bef4b37ee27ae476cff113acf04645a28167647ed18b1ade0f1b344ebfdb83c3780be34e46ad405c99de5b03b2c00cae9739a46', 'admin', '2020-10-18 13:29:08'),
(215, 'd3aa0564195aeb91e8ebc54f35ba59dc16e530069ec3a2927b5a449edd15f6e65782357f39f32b13df39f5801df7dcafd61254f2293fe81a5666fa18901d89c0', 'admin', '2020-10-18 13:32:24'),
(216, '456a42d98c0db7d585fd4e6f9e075ab5f0ef8a6f5b553c079fb837a8a07cf99a43b424ffbcda66c8234bbd3f54456f130b338e709002d1a57336832b249225ee', 'admin', '2020-10-18 13:33:31'),
(217, '01119a720be78b4f7d62ad05affc70788ccb61b648bc27cffc04c95c311389222532cf218a9253a7247e378f09f608b26bda28e179e894500693ddb4f6082f2d', 'admin', '2020-10-18 13:33:53'),
(218, 'ff49d91b9e68bf471cf163d6c25b277459652602949748fa01a7b62c85586d68feaeaa48b97aa633826a557fd298b432b92a9e05c0d1016a661a32d2e38a0e48', 'admin', '2020-10-18 13:35:24'),
(219, '966eb650c6c563f8be9d425ef66e3fdf6c986007c453d6ddeb8cb656b65613c057e1b930949c75a93369313ff04bc446081da530fd9f4a5d53b1b8d520655bd2', 'admin', '2020-10-18 13:36:23'),
(220, 'eb67ad873869c677cd9d50de27882e0a1f5ad3401d3d3f60aeab783a54cbe337fe7a53bcac1a9f49c96ae25c08e967a43a9e398e7486ef96acb2b9f9e3612400', 'admin', '2020-10-18 13:37:47'),
(221, '2c20d06ee9a239109819b1dd3e1d04412c3811502d324b4533af7e451486651f60e553727947623721703e0caa14ccbe213853350a72ef8cfaba3087ef8b157a', 'admin', '2020-10-18 13:39:04'),
(222, '188d6032dfd8ad65de4ae64d576d621b6007c9b2dbd80f504012fec4e4d1af79fdd72615854364009bc06098e87c771e8fe2941f90d9b225fadb79cd6aede7a7', 'admin', '2020-10-18 13:39:14'),
(223, 'e1ba8f4d1fcdf8f55d8fe992659cb3469c26df3c393ba07f976d1265291ee0c7a37394e6fe5ec009a5f92dfd35b8f6991925f64d5b98abdd630a817d26193b46', 'admin', '2020-10-18 13:48:52'),
(224, 'f7a89042a6935845b7049febf38df58e5024f2956be3e90b1b6c1f1c73975f7b09537da8866bf07b6ef45a9e501fc9e35c43279ebe4607e7ead84ae1a9e683b2', 'admin', '2020-10-18 13:53:46'),
(225, '04c78fde89db192e0c96bc0e065147ef2668e3e729d16d535168db5fa485d7f68ccaadedc08d359fdcd96508050d6bf1557b9a3d5d861f3fc23a87f6858970f7', 'admin', '2020-10-18 13:54:36'),
(226, '02b7f60910820239803050bb3618eb419280783843be9d0b105f58e6e00b1a5ddec978e3acad42bfb1317ef6ec300b66e46585aa5f4e6f7b6b153568eb81b953', 'admin', '2020-10-18 13:54:49'),
(227, '77c2b28cd65e6ca5654c4d8701d89de5450112c50abe441e3339d6afa5a171218798473e84876f9891c0796339822b435740563faa77bfe0489ea141eaa639b2', 'admin', '2020-10-18 13:55:29'),
(228, '6062e5892fef24b8f56f0b11661ca56e9931b9b2d1419fc0e3d28882374348a86ada659ec450aa70d16fc8a85f040e2428d062006e929800d089fa082a521a90', 'admin', '2020-10-18 13:59:45'),
(229, 'c3410a24bea6d0a1698710bd6248131ad5b4b3bb98e9fefd89fd67be2f5fda3d65c2c379fa046154496769a583ba585e97003b2c0ebbb5465051703ea7695f73', 'admin', '2020-10-18 14:01:51'),
(230, 'e468abf1aedda49e1ebd25d417eaf7f43b65a8d78445f469c1787a46eae11d708607e5fd6e45115e5f9b7c7484e99ae288cd206dd3aae98a2c0f80ace4e0ac10', 'admin', '2020-10-18 14:02:12'),
(231, 'fdb24320d7ed244a25d04972d9c8d8ebec14e11554cada83ff9bad88628a44b85dd22200c9274acf4d665e4c5f8b35598b4a2ce6485e481091cef263d02a3c23', 'admin', '2020-10-18 14:02:28'),
(232, 'e73e40f63475d078d99698a25c7ef8755d23f93fc75ad112d733e51076dba1c289824c026ff67ec6512c6d4c031d075205d21f996ea6e45ba71baaad8e040ac8', 'admin', '2020-10-18 14:02:43'),
(233, '1afbd1c0cea060dda33d5e6dd11d9d0ab8e08461a7ad36c964d55583d808c1ce893360397eea84acde65f06df2958e4653fe609ed80b78808410209a8d860cbf', 'admin', '2020-10-18 14:03:26'),
(234, 'f118eb6c356cb049413c0257e80ef349b4a00303e57025c6a3180d0d7e4740ebd130aa494d583b207f7b8c5240738a81085fe1d5dba5ae7f71dc48a5a7d73e36', 'admin', '2020-10-18 14:03:47'),
(235, '8c6aa7c03497f05442da4cb5749311f687361f9889520a3c586ecab2b23802b734468b47e0a17c0b5f07634baaddbdcce7d2a6f7c8ada761aea86650fd8ae272', 'admin', '2020-10-18 14:07:28'),
(236, '645fcb35714ad4e22c267dd4a89ff3c5c845da071e7e729cde5058543694eb45ab1d786f9aed4bf3a44b8f85bd8078a41e2470814d9a5592520665f1aabca433', 'admin', '2020-10-18 14:07:39'),
(237, '8c2c30b59ad3f1c727f722dfa021b33be2a2377648fadec0989ce57fef32c44a4bfd6b366a02f121f81b8bcff848a5e8c74ff2e3c3d57cf1d654b600d326869a', 'admin', '2020-10-18 14:09:42'),
(238, 'a5ce8a25fb8180b8890cbfdfe0dd763ea1ac5b28803d320dfdab9cdf21a5c7cab55ab7527dde0f6e56939bd4e5faedcefec20fc2b5a7ad0b2937156d69fc1672', 'admin', '2020-10-18 14:10:23'),
(239, 'f80865fc933bc9c4d5ed947dc8f602b6744d804d8e81752fb3d6c776130b07ac2a71f59f85bfc7e71ec7cec4fe02c179a79bfbc9c8c073d95171e78527ed617d', 'admin', '2020-10-18 14:19:26'),
(240, '3fec10379640ad92fde5566f8e628b049f01569f13256ba346528225e3f64594022802d99d4632b684e6f398527ef3292596f20ce9df44d9a8a1f6b567bddb2d', 'admin', '2020-10-18 14:20:38'),
(241, '42dd94a4af1e7c7d36d517b7540ed45ae1b00ccb220695abbbcd0f3003c1ced49cf91f6236c30c31efab18be72969d0241c406f15649cf8134d4c22b720329db', 'admin', '2020-10-18 14:21:53'),
(242, 'aa2364bd5f1a3987c32921de135ad4d7a3e9006831541717727c9f6628c4f54c896b8137e7f2cb8fabefa7b6987bfebcc4668a0907b751dc994658c85a5defb6', 'admin', '2020-10-18 14:23:45'),
(243, 'f757492821327107ac0d7236ef0cdc806ad6a6c355dcac58400743a5ee66ad00d5ffdad1c1921de23b75941629425c1e9431e5588266cf057b9a8b206c5cb506', 'admin', '2020-10-18 14:24:43'),
(244, '9cd651d29f9a1754e2ffcf11f8267af0f80c6b277028372c5a637225c853f30c62c1b5f3a09407629ed4b023658d272f983c086bc363de24dcd40ec0a0aac2a9', 'admin', '2020-10-18 14:29:57'),
(245, 'b461c7b918c5ea891235bba47ffe071dcf0419dd3a47d451811f40037b5d12e6cb06cf4940564cc8187536babdae90a0ffdc67b46811466c82f7a15203d7f25c', 'admin', '2020-10-18 14:31:00'),
(246, '87241a7ed9f10f0e6b334c1f01f0ae3ee9ef8101374bdcb0d75d4898660c294494da329707f959609e54ba510b793862889fb0c9b3278b0d4c72f34e8ced27f9', 'admin', '2020-10-18 14:32:59'),
(247, 'f21a90bbeb557a32ee1499bd51b03f5b7928628e200c849ad62cd8f00bf4085c1b9f6968b4bb44397875d08eccb5d2e054c9a9ebdc7bb8f4a24a24ad23434c03', 'admin', '2020-10-18 14:37:09'),
(248, '90f3e17653e9b6019b338e8e4dbb21cf9cf7a4c849e981f22b677682f7f2cbb71ec1d2305865a4184514a6c8fb53c051b6a93720a53c65c20989691d48a6b5a0', 'admin', '2020-10-18 14:46:36'),
(249, '7a31104a71a36a8229643561875bf7a64a52b44e06661a264580d2bbfce46f424558caa3753538cedaa07e60909caef2046d727ae418b25ecf6fe2bf3de3846f', 'admin', '2020-10-18 14:46:45'),
(250, '919abedc3d0e9ba327dded12f5afc8ca2e5c0e4b779060526cc9dbe12a2d68f8454e55d8725ee2bf8287d402755cb1a6cba40988de1dd97cf18d220e5d56a03b', 'admin', '2020-10-18 14:47:17'),
(251, '14c1c3663231be2713abd9da50a90703c3dfb508c04da129788a1eeca41a798c94ae09ec1a85efdf1450ab6a17f887ad438b129ac78a3410bc55c9a564cf3ae0', 'admin', '2020-10-18 14:47:39'),
(252, '6683aa75a0095315508cc586a59499b89f728984faa46ab42e4f65432146141c70309636e2f20117dd7cfe030d6ede2926d1d52424226870ba9358cdbea67cf7', 'admin', '2020-10-18 14:48:45'),
(253, '2c73b168041b3ff80f3cc21aee55188d255a8c270cea9621ac9e893d7e7dacc8b4ca5bad204bb748dcc78cb6b4bd6f20626fbbbb4a68949fecf5c793fe23261e', 'admin', '2020-10-18 14:49:23'),
(254, 'b2e434ef41177a8063b905fbcf7bb73260da522fe39cc727657455d26b21473e0828ddd4158f23968129d7328061579304ee6ec41ed9aa5e90886a32d4869b8e', 'admin', '2020-10-18 14:50:49'),
(255, '0c67bb17cb4691662874d0dc948d851611b9444bd6fac6da536c287908d319dac22a62f1748ee27ebce5b607275e1ed35146d96bce5cef0e1dfd91555bb503c8', 'admin', '2020-10-18 14:53:34'),
(256, '3b3bd03df87af1b03c2f85e806f71fdbd2a94345365c4ed0cc4f5b7d8e8d08e87b133d817071a5376982220efc38095107282c4d6950595660d4fecaff9e4dd8', 'admin', '2020-10-18 14:54:25'),
(257, '9c2a623389738eba4907b50f17fa5d2c15caa627adaa19bf428b46377497e855fa31b4fb4f9ecca8ba4ae13f025d15d110bd902a08200d22169523584870db74', 'admin', '2020-10-18 14:54:46'),
(258, '2fb6043146d5204cf9f46a74489352aeaafd0e56a951e73c7b9e15601668a23c28336241de3cc648fe9afb4e6b1ff8c78e539d6625c081d3887b3bc5ac641d39', 'admin', '2020-10-18 14:55:04'),
(259, '24efb76964aaadc10cf14e30e8f1276173b2707e03398e0eb936cefafd1a876ab706457779f1902972aedca75d24143ad6a1a9515bce014529e7c5a705f7f96b', 'admin', '2020-10-18 14:55:21'),
(260, 'b31f8e81500048a983e7a5db3a83444a819cf2e645f36ad4d00c4ce7defd7d5a536d3af69ee1ecc828252f0108853df8254aed915bf453619ba0326e8aae5e9e', 'admin', '2020-10-18 14:56:16'),
(261, 'c19b59efc8cd24af3a8c18636e8b3b538dcc2aa81954f308348a7015584f5dfe704b3f8062fe274d0ea17a73a95178d5cf540e9772de66af76e35f50e88274a7', 'admin', '2020-10-18 14:58:02'),
(262, '09aa3cfa6169bea9a3106d824b8a145fb7dcd1ef1e9e4d91ec6d794e27381751238280ca74138dbe76e9ddd1989c2c0ec9fa8c879ea17d5002ae8ccc7aa930e9', 'admin', '2020-10-18 14:59:50'),
(263, 'a6b0518f8a411a1e8bcb133325bd0fcfec5e08e717eb93929238327564b63e792d4d70b649fee455052971fd91d224017b3500fc6a734fc05c56d2517fb0c2f2', 'admin', '2020-10-18 15:21:34'),
(264, '799bd682b04d0361ad9d0cfa8ec097cf029db0d5e08245b7534461509f6498b035dcd95f4a1d303211cbf68cd6c4bdffaf7ddd79bd580fb908613d216ae63a5d', 'admin', '2020-10-19 02:43:34'),
(265, '27dda1ac8724e81dfc64a8ee36aca62d42a3fd4c7a08985753f1628fbe4d03a7c25f9f5046cc26475fd48aef7d3c82de540411c77e289b3fb5b3b05d415225e9', 'admin', '2020-10-19 04:17:09'),
(266, '49b7b61df604ee9ba6c41ccc09aa6ac52c03faab3c4566475e6f0667179df4d20210d785c8dbf0b2b737a42691b0909a852b8be47a83737b9029261b88b85aa8', 'admin', '2020-10-19 04:33:03'),
(267, '68867a7eabddb6d2f8d6585fd066b9fc219f374435b421244167b086887605abfc52fe939369735ad42886a423f46ebd9b8337afea8b8be3906bacc3e81495c8', 'admin', '2020-10-19 04:35:28'),
(268, '0463ab1e34083dc4dcb2123af8bff8fc4b6c19cf469f6c1f47880491cfe7a041452a96de0e2dc5a3b1243bd33fcc167ed59b6b8ae8be8b49ee3f94890ea91f0e', 'admin', '2020-10-19 15:37:55'),
(269, '968eaee2b7a8e7bd32e402e4d0e19b67c697f99ebc55a3d8b83b4ae76897fc56130566153f8746473805f791acf08853f92368bc6ce26a98aac6604047e0c534', 'admin', '2020-10-19 15:44:50'),
(270, '84edfd25468a968e949f9ae57c3c6f4e21587a80d037bf93eb2149833f6616c682f269847a7d2209782e18402fc16f6e2d9118a79812bbd65ac5929536459781', 'admin', '2020-10-19 15:45:17'),
(271, 'd0c4cabffa6ec0bd7d2dd605de81920b9bc4ec370c0d81991144acb9e40400b3a10400fc106918b54621721f2f88c21c43d9b8c1978317d4d3810112a8a968bb', 'admin', '2020-10-19 16:01:01'),
(272, '58c4de8792498dafc7f0566b262cb2785cfefd4b959fd7c2ec51a027e6cef7e2676faf194a1ee937fdafdf1834d55f960ad9a1071acf6dec5320fb9db1a2bc7a', 'admin', '2020-10-19 16:09:42'),
(273, 'e65e8c8d3f9a2efbc1687c1358b07e4c134d38265ee2ee016ae0f0b5c05bd67a0d1be96beb77cbef0d7687b9f80025d23a3a6e50ea5b7a1f07915ae89c02680f', 'admin', '2020-10-19 16:10:18'),
(274, 'f74ee10ec8007a869945ec28a3aa6c4671a7a4d2ef1297457a4ce2e7be10abcb5816634d0245c7ee35b6213b66d8be1356a88d2a5d8fcb0cd40d452e915a0a90', 'admin', '2020-10-19 16:11:12'),
(275, 'b2e204e22356a82dad3d43d6cba0b80c8d327b3205faf1bb61e76a9f65e5368db1fbdfecabe1c9b37b87eb407c2861d8f3fd050fcd51ed1313a5236ee4695a56', 'admin', '2020-10-19 16:12:14'),
(276, 'e0fdc73a9dfe4884008d82dd0e1d79b1a454b2a986a2b2d1421d83096b29e41d46ccca7f23e0e24f220a311b5318fc10e9d196614943758054e10489157d839f', 'admin', '2020-10-19 16:13:43'),
(277, '123f6dd331a98732ebb66f82167eb2a0267697b2ec3ed3759175286733cfef1da400d69ce54c6b0b0f7884bde14114ba5ad6c864857b4d98a67a2ddb20a7fee0', 'admin', '2020-10-19 16:15:50'),
(278, '57399075d76056f747553faf542755bc77707ec4dc5df63f8112c4c28cedd8ab472b8e2d09e4cdbd994aaa9697a2feb10955d2297518fee5b5959e27faedc935', 'admin', '2020-10-19 16:17:02'),
(279, '35de7ad6545dc0a3a0b2b5006ec8bb5f81efe49303411da9d9e8d109b985c7eabcc34ca86d01fc5a4c393e535b6c33410135426cfca92f27faf677f3d2769e02', 'admin', '2020-10-19 16:18:02'),
(280, '3bd85c166551f8b9b2f69143a42ac2b18eecaa6443e75315e0147980d991f4ccdb1416d941bd08b42d6809991cde73a21ba110b69c8efa40a214545a3422977f', 'admin', '2020-10-19 16:19:46'),
(281, '455faa7dfb9d4d0f8c7d81eeabb2866172066c5e2c33f17d175de118034012b4b6267b50d6255c11fc37f92abad2b8ba25ec2c66eaacd95078227e96ae5fd2b8', 'admin', '2020-10-19 16:19:59'),
(282, 'd21171744942d847a7dbc644796a7f69e61a57be944d62aaaf8532d8ec2dafb00d6edbb878626356d1c22401785c029011c89cba88ec0fb776a944fa188bd216', 'admin', '2020-10-19 16:34:19'),
(283, '066c7ea33ca435e0102a05707f6ed30a909865fa1e3b966a7bdac5956e1872436f7d7bd4d68d68dfe3640e7d2c9a7c2e9d7271eba34dcd691225508c3b626ad0', 'admin', '2020-10-19 16:38:55'),
(284, '96c127ad41f8fbc4e4d4d4ed5dc242f7121841c10f43596f97b2966d6ff0f62755757f44528d54cf1eed18eda52dccc49a19b3c628d850ba9772c7f1f3229b85', 'admin', '2020-10-19 16:46:52'),
(285, 'd5b810af056dd2b84a6628073ba1d818ff52b789818696b3c84f8fb259d34449956a112d6458a251b08c09786f8d821f6ecafec8e9dfcdad2e1539037f805b7b', 'admin', '2020-10-19 16:47:54'),
(286, '9b7219b00a99c9c344f7f8cb07c5bbacd55d5d69b211876fe911be5e79ebfa5ca3c8751c24f3c02ae7cbd887f73ab2c8b7c15575a702e69f27e84b94b7f31f07', 'admin', '2020-10-19 16:53:09'),
(287, '32d5118187d805f4154b150042b0876979adf5f6ef3f42604a224a6627812667afc76110dddfd43548c1c60611ac06d2dcbf6eee3388c6f0d6b604e6f7f8dfa5', 'admin', '2020-10-19 16:53:48'),
(288, '3ef16d6406fe83227b8a4682fcfbafa84fffed976c816a84e6b783525967a5aa87108a72bd2e77855947d59cdd0a47240e3bd477e684f3bd70adea82eb7c94eb', 'admin', '2020-10-19 16:56:07'),
(289, '0df2374a5485580598c3e938ca34eba9ddbc09044d32800b71809be5b242a8aea7a7a587c8f73161c9098b0de475a1eeecee3d08b4ef9b857a779897101bd6f2', 'admin', '2020-10-19 16:57:08'),
(290, '2696aecbec6e7be1e507a454a805c99121e38365b23d20b34728e9499cbcb35c333a1cb9468fe92e44dbd4d6fa499cee628a9753b3c51f3fa935f8ac25c60652', 'admin', '2020-10-19 16:58:30'),
(291, 'f28c8dc217995d17a0f9d57eb333ea81ad05df89731d12ec534a680957ac6d1382572a014c82e1657ab80c3b440bf61a710d641e05dca2ef2f5037213e7ed55b', 'admin', '2020-10-19 17:05:24'),
(292, '5e83eae1baffd35ce6128ec473256a1771912f0893cc45803aeb79db48d18475bbc4b01723eb345511404ee8f8cb7610281b27c68e483f368142ecc94333d247', 'admin', '2020-10-19 17:11:37'),
(293, '336bf9a6b0e2f81956c868fe26d30d1ddbca3fbaaf9ce1216a73f8e5197086596c66d11cf6d59455762df111dc935cc2ce3f86f2fb35f7099b365e1dd619c42e', 'admin', '2020-10-19 17:12:36'),
(294, '932e9dd546efcb29e23f76df5c482fe384901f988506c5f2fba2346ca09e8e1d75eb9db17eb6e0a1b0b4cc107a3ad61b2ed66c42da029b38d87bacabb2409abd', 'admin', '2020-10-19 17:15:59'),
(295, '35baf703eb63d03cc5973e9aa295f3e5da378a0b84ba256b1216619b962e3c509e2bcae2a137bb2aad83e029cadb5cee0433e81b49e70dd172072ba8bbace2cb', 'admin', '2020-10-19 17:19:28'),
(296, 'd3fe4a63f23924d9dd966cc24d1d54fe337ccd6b9505d931c0b58db88ee94ed58baf635737f9286ad0fc74dcaafaaff7b89072bc1815f2d667396281988bd9eb', 'admin', '2020-10-19 17:20:02');
INSERT INTO `refresh_tokens` (`id`, `refresh_token`, `username`, `valid`) VALUES
(297, 'f23814e8b27f43efea113fbba12fcd44ecdd44b89ffbf40353fd4adbd23d49e52d2b3c87853d2ea32dc2c5cb8f33088ca649144c9ef9fe624603e3a18e356418', 'admin', '2020-10-19 17:21:45'),
(298, '826a763c8fe5118fc2382b7b321679e802ce417fe76324faca30a93df8b7dd893550c55ea4393af8bc12296362e7811ab9f1d59ce1c669e760b3f5af269137e5', 'admin', '2020-10-19 17:23:34'),
(299, '9e16330819b5e1c978c0cf85d8845112bace0548bb4ceea84272ca5030761c3132223f1554aa71239efbf23ba2394ae8da85381fc8f95e66afc22ef79e463090', 'admin', '2020-10-19 17:28:03'),
(300, 'e859a274a05e9235d44089e939cce1c86fa9a211a9d7e102a8444ca8bd4c3515e4d7f7c648a6690798a8235da1e84e840e904795029a0dcd29f47e20f54b1e4a', 'admin', '2020-10-19 17:30:34'),
(301, 'a68ca73a08eabed3392500d120d19ed8847082d70592f16ed1371e57f67579b739068cf386c2cd4ccf58f0421e8c28887d6715f352b6dcdba4deb53d0fbc4b82', 'admin', '2020-10-19 17:38:03'),
(302, '027e025f9686e7caa1b9581a3996b8161779383caddd287bf80e038703cf65d14bf39343f606612d04149cd2680ffabbb49294b14bd67ac70e1302b60266c9e1', 'admin', '2020-10-19 17:38:16'),
(303, 'ac189dc992ef49abf7201d81687793d0c02c943b5eca34ee08c6ef6e96847f5aa12727bfa739c7d144e72bceb910ec6bf11a465f12ebd84e24ecb936f449758d', 'admin', '2020-10-19 17:38:32'),
(304, 'c06011bbb33f355adcd473306380eef713016bb1a646c5983273f5755d81e0c2d4b550c3c6a4e222d815a8010177d86f25bfecd09863456b2122d6bfa8e73871', 'admin', '2020-10-19 17:39:32'),
(305, '0b6d14fa44bcae62cfd4b36b288013b988303aa7bb605903fb1a88f51c5bb905b17732062fd6fdfe51c0e2769f826f6bec9d3042a8a92f679bfbd6a083be9905', 'admin', '2020-10-19 17:40:14'),
(306, '9d7141dd35e02c908ff6ed506aecff80dea8eb3d333416083fcd8bb403ac4a014278ba5d68402639dc8a86be0b2a68cc23d62dc7ddb607fdb7aa1ed9a047e697', 'admin', '2020-10-19 17:40:51'),
(307, '29380c33033606a7ac31c0f9c0e422d6ff562c2ded5d80953048fb1bc76f67d44efcf01d511cf8d29a9a2c7a42e21794654985b6b3cc3f15d33d5b5cd4e76b82', 'admin', '2020-10-19 17:41:07'),
(308, '06da4a8373167d8dacd6335db9f8242ce33bf0f2cf40723ea0879616f9ff5596ed19fbfeb1f7c35d4b22f91be1c149dc589a6fb4be1f16fc6189e0ef9bcbac20', 'admin', '2020-10-19 17:43:07'),
(309, '0acb2f44b9461f51c7741ac9376c1a44ce868e905f83199de0d697eb73582d1eef94acadbdf9d14582c991688ee4b2ca54fbf7e3c3214273a9c723da993e8b38', 'admin', '2020-10-19 17:43:53'),
(310, 'dc1200deff2e4cd46bc8d6867083a7655d17d1be67286de9f342202820fbdbc3906799718c0cebaf223274aa38a75f7443bde8dfb2cced9f05313c79255e9e55', 'admin', '2020-10-19 18:44:56'),
(311, '3c8bf360f323b67e414d7d6236c853b16defd91f21cc13206a5f9d2a9b4473d0ad93e6984f4131231a6f3d9014b961497dcb0889044dabdc0fdb037a9753e9b3', 'admin', '2020-10-19 18:47:22'),
(312, 'cfa12c5a00e187bbe3e5ffa541fdb280c5c3ab5086cfd4fceb4b5cc3c3b0cf69c98be3273c470eb16b6804c505ee16bc419f59078c0307d3f2ddbffb94269427', 'admin', '2020-10-19 18:48:01'),
(313, '765223f52db59f3774f02dfe2add5ac386a8a907d41b15a20a966cf088b79208d31ba47686e40b49573c3396119b2d52fa7479e32d42ff390a26ae6f836a3516', 'admin', '2020-10-19 18:48:59'),
(314, '5dcb57fec1d780b2fbf41c0c0a2360bc3e672209807d577bccc7244ce84f25b1989a293107ea5599fbc251585160283100471c40ee67262154aa68fe631f59ca', 'admin', '2020-10-19 20:02:26'),
(315, 'c6d48426c2b597537a2ad60f0f231096803327728b316875ae2f7ba5760eb57d36bf5d89c3ea8c66983e4c583686fc46f8149493a3a604c11db73253958be184', 'admin', '2020-10-19 20:07:26'),
(316, '82d256e374b5983a87c62c3aafcd4d8bec25608ee79f48b92cd99cf84d199f87e641c2fa545fb7037f8b63883c035c690787850927414fdfb2d035e14312a44c', 'admin', '2020-10-19 20:07:40'),
(317, '1d2e1c4d5fa465f5df7b51c0890a6ea04bb94d59b3c13225e754c0aa242d70aa95a287080280b75052550aa664585c228447ebe1aca821bf1f4859464c253835', 'admin', '2020-10-19 20:07:57'),
(318, 'df78cb4d4d42a53f35c8a0f52bd5cee6757db4d473aa63e1fdf92ef57848e33d5909a452e98e049ba1852576e5899f09d43e408334a052cee50383e07a8bdf92', 'admin', '2020-10-19 20:08:16'),
(319, '58aa875dd3b778a1c8f78cb2c2297bdb79bdb7aa8914ccabe9249f318b1b6c5e9592767e9b1664e4523173547765f41bec0ce44d6b4f3ebea0efe9e22dc09dd4', 'admin', '2020-10-19 20:11:12'),
(320, 'a5017bfa587ce534e270c5d730e9c13c1db106b478a89dfb24459f9b6699ccb663337c68ec890e509d7bd362bb6f10ebdc099ea0aea686499a6c1e80f4db2948', 'admin', '2020-10-19 20:11:45'),
(321, '00f4275443ae6127e75b394d074a42f48ac62a7c068e0fd5ee2a615b64a25452d637c20d2254171fa30238cd82881a549d39db695a5ce50c256b9e7021d38144', 'admin', '2020-10-19 20:33:57'),
(322, '4a7d92d17b61e9dfeaeff14f36fff29561d80c3cc9936b8b6c3ec765d125f78792ec1213d8c5a7f93d77c1aedd7a8fe98f9570ba1e3894ce92a64b0db4693d83', 'admin', '2020-10-19 20:35:36'),
(323, '375bc76d347f8ed217a3929fee5ce2a03d362e93f81509d0a49a2eb15e82723678f9b176592c8d43cb189da90ab9a50e7fb04e8687aeba4d50aa73b9ef55105b', 'admin', '2020-10-19 20:48:05'),
(324, '197d793d705214e2fa7dddff91dcd895e3b49fe294467266137c408a94f22b0418f6a0565e535d044b83ec63c23cab18a0aeedf35edd3eca334762363a11513f', 'admin', '2020-10-19 20:49:44'),
(325, '3a7d025beb15d76720bd13c1d1709645105bd67e84b608e05808a800012d4a1807b25ada37559500cd11ff25f55bb9ba48fd930a6fbdc4a1529695f8963d9f76', 'admin', '2020-10-19 20:51:11'),
(326, 'c58efe4d66dce64ed2bb9ad948916d8ad833634fecbef8ee0ea2d5b15d77571b595b3694c4b7b9951788201d414721df707341eb6ce0dfd133c49155cfb67203', 'admin', '2020-10-19 20:57:26'),
(327, 'e3fcfa23df1ed42c43b83f3d6fabe5a4daeca0cae866010a6654f40a7e3df16242a10c8a99f4860d994c8fe732a9817ba56c0634ee028dd57faedb631272561d', 'admin', '2020-10-19 20:58:55'),
(328, '1ea11f201ca057d7f63e3ed1bdbe62732e2d80a9ac2ea8a851ec83ad6ffae4ccf30b5f970140d9d00663c8600dea2ba2ce9644fc03e17858a198c9189d852588', 'admin', '2020-10-19 21:25:07'),
(329, 'b7ded12d1c41a79fa68902a1038c6a57adb1071646984ac2859c3c41e3a1c823e15a13c3d072150ca27799724b6d70c9af6a0d77140d603428dae290de1e1e2d', 'admin', '2020-10-19 21:32:30'),
(330, '87504ddb870ecddd65d429c228d42d35a7edf829e104ce95ef8df0b05a15088148e149921da15ed1c2eecdfd23ad16bd619e1d026c835dd3fa6dde6f4aaf28b4', 'admin', '2020-10-19 21:37:42'),
(331, '4fc845247ac5d2a7eb41a024d8ccb69f96e958d2567e276f0daf787fb50de565b02c94bd2acb7caab8a6183cf2d4245f4fbb7c20ed03429605163bdb283b31bd', 'admin', '2020-10-19 21:39:54'),
(332, '3b567cd2dde0f81e6ef54fde03659eeb85ce3962399614f55e898d86dfa8e40f11e9b277fa4709932f2675fe19ca1012ea563b612ee0f995551748a73510dd29', 'admin', '2020-10-19 21:40:30'),
(333, '82e54c3ef94867691757001dcce21b379834ca2a9f44856d12c8157cd7bd218b5fd1b2fcba46103e52415e0dbe6b177547375deeacd6fb41e58f097acead3fa4', 'admin', '2020-10-19 21:42:08'),
(334, '50784b30f3d47ee990db8ec20a37095cbf0576ac487b0499d93f7128ec7292d50101f98d70f27a78f1573929bf629872d226f58e27f40c88118e25705b62b1f6', 'admin', '2020-10-19 21:49:10'),
(335, 'e72c5e9e001ef5d8bcab976bf697046a5baac04613451caec66333b073dd583740f2d84d3e1e659f0de4331ce1312496ce2790149e8f644194aa1f8902dd8486', 'admin', '2020-10-19 21:50:05'),
(336, '655744b352475cca435973bc5c691b0c7e1cd4a3453558d57c9d2000badacd7c27fa8755a2ef99ce7ba5f650500bea51aa2b999ae3127490d3b09777e7650d74', 'admin', '2020-10-19 21:50:52'),
(337, '8f77adbf35711d3d67e072efac82a76d107b9269b085dc21a30470ff9ba80c47a0c62ffca8301b6870ce4049fd0e9f298a7585e133fd282db82ff7d064425705', 'admin', '2020-10-19 21:51:21'),
(338, '91818a3547503c90fecb54fd78f608a01482492ef312bd89f1b9e2d83bbcd00522772122c49e1ee5e5bb3661b1753023d8d681cc2de9bda48d31e518f4aba586', 'admin', '2020-10-19 21:51:49'),
(339, 'd694382b7920d5c2b18c5ae5a2cd675dfa4f8e2a095b9daa7c61b74972f750a9c668aa7f1f8f9c6c27b2bbbad9593aebbf1ae21cfffd588aaf13364b1b7a3b9d', 'admin', '2020-10-19 21:42:35'),
(340, '3659dc7f5d2de53adb334a37592ca9feaa8c73affc76ce0b715114e9416c67056680f4534b17ad182e085fd60f60a690177a5a7af1977739aca1437b8450780d', 'admin', '2020-10-19 21:43:54'),
(341, 'ffebf8952afeeef68997f82066bc872ee83d2788c8f8d452f03ae7e4ad16d1d6090f3577aa0e67a1ecbc8df420b67cc10e317cc60ebd8750857e827d558f2d2e', 'admin', '2020-10-19 21:45:05'),
(342, 'a11bf368468fcf632e2dc634cda3009ec1c93ce4032ad0f88da2822b08f7de0cdea39b032f1b442fd7d5404e03bab19a48e924136dd6b0754325941c04bba442', 'admin', '2020-10-19 21:45:28'),
(343, 'd129587e7b2c75676accabacfbf5f71b2824707501279e20773c29c3eb355fd8e3bfb96f7778314764994ff2bebbeb82ef699bff40cf1b8f24a3a86af69b4727', 'admin', '2020-10-19 22:16:51'),
(344, '85ae37caa24efba2c0e7807dae7be3017e46e1610b789f959ccc244430fb1102aebaf87fe85c54b56bb7563a925cef3e386e1ed7e2674cd771891b229ef5d027', 'admin', '2020-10-19 22:18:34'),
(345, '5d6ea6d3078571966485d3ff44f446e55e0bbb2a42202d6c6f2eba1a0d051684c29a9e3d1da9a8da697f13ff1a4d59cec0adaee6e3ed946dbc884fa5e1b9217d', 'admin', '2020-10-19 22:27:44'),
(346, 'c752e265ca876f655ce217053949a0c67e6bf87ea64ed3760bc0889b8d4f73a99ba3602e1b463934f860779919499dc988732effb1829fb2d430473e967975e1', 'admin', '2020-10-19 22:31:10'),
(347, '2618b342b116fbe4c1d01af2d4e1dc452396539dafd6d886aef2a20a40cbbd0b9ede582236ad898424d3a705b7e7e935d4fd17beb73c1c4ae343af3149f97554', 'admin', '2020-10-19 22:31:44'),
(348, 'e528bcd2094ad74ba9b4de44ffbc86b990f97fc7716069a8c183c812f1c13970c106d7f938102d7a68d36458aec57e06e6f6bb5262808740f2ae4147d402d686', 'admin', '2020-10-19 22:32:32'),
(349, 'fe2fa7ecff5b08b43569db32fc42ff6ad3c3240da71eb1b62ce1fd7cafb09d3aed0540f423a5a4adfe38175d8614f2f1b4c3488847dd4f013c4c4200f69bc362', 'admin', '2020-10-19 22:33:03'),
(350, 'fe10e482eeb4186a1810dc27c98fd14d4e93e878a20ad961b6094f33f8055524d7d337c17f3c5a10e5b9ec75f3b53e4db408c16648fc21632fecc3cf2ca8adf6', 'admin', '2020-10-19 22:36:44'),
(351, 'bda0e44806b45735a7f53583271166ea40149c0c191218b9bba20918b329206629b266d3e6df082a9bdd5f4e6f28da906804fcf476ff13ac7d770e7249ba1268', 'admin', '2020-10-19 22:42:54'),
(352, '41c24530acdef00511a50182328c170df450a6353ae89121e326df38ee4c2bc00ceb10e724355683ae2adc8002d2645d245a178848af66a5e1aa9369775af9f5', 'admin', '2020-10-24 12:18:32'),
(353, '2f905de7a597445ca1d1543cf9097db7c2ae5ddbcc656904d12682e5cd7272b41e50899531f0c1a058ad8bd7a4237ed87c3e15ce285df5233fc471ad44520152', 'admin', '2020-10-24 12:18:59'),
(354, 'c7089bd5c4dcaa68580e2ef869fd1f1c1b8d41cacf29c75d420cc2dd5b25e9fc38b180017401616f12b90be999e1012a65256abbde3cb93051b9173d073acc51', 'admin', '2020-10-24 12:19:37'),
(355, '2e5f06fd7fd7e4aa1f57675278847bdc530d9735d9ad45e887ad04274fc0b5e4144ca12d4c85140745f8c13835a3640bb867d502171c385ca134db61b250ea57', 'admin', '2020-10-24 12:20:03'),
(356, '5ab81e621db9d83df7cb4f607496200a30d12a6f3663cc44f1143a8b0763d3eacd7c85d5e743111dc8ef94edf339212bc38562b26d80ac3d76a09a71051115ac', 'admin', '2020-10-24 12:21:27'),
(357, 'ce3433b0914e9e4ddb678dd65cfa36a9c5854686c9de31dcef9d27abd68783e24725704925fcb18fb8d0f9abdbfc5c967eabfd14627f50255342b6f9ded6c0d6', 'admin', '2020-10-24 12:23:06'),
(358, '74f258ed4e5b596ea89a0cf2c08672fce04122e54fcf1e1fb6ff19b3d663db7bb1f6fe874eaf7f3b59941cae57d661256b767cf64018b4d10f1ded11d635c052', 'admin', '2020-10-24 12:40:48'),
(359, '96eaa146e47ae53012370cd07a67c65b05ba86fd723f4bc8d1c3aead7f18d4085a285787fd61592e58a2255af6569e76e3893ed181d00180907722f6e6794acd', 'admin', '2020-10-24 12:43:29'),
(360, '79c699c26d25ed78a1360b97b70f2affbfea9f6b6649cddcd92f4515a931951f7323d2d22c2d714c36223980f7ac2aa83aa03e4fc363293f277720a830926e85', 'admin', '2020-10-24 12:49:11'),
(361, '1118b9b5c655ff99642785f3829bbd110b70dc4a117edd10be1668d2c17fec76cc80e5da80add2b6bce6163bb6ce48a247931b2ae386bff5281aceb0a246741d', 'admin', '2020-10-24 12:50:17'),
(362, '32b9f462fcb24e5bb2c33d49df6ec95fd9750804b8174e2c7a7a6a7f09c68b1c488a16cb70a6de5b181470080900210b08cce056a7d2ae5119058f964f2af6aa', 'admin', '2020-10-24 12:51:33'),
(363, '0c17858140a98c50f1bc162dd3def93d4b5a51c3e7e2d02d3405592b8b5e14c34129a30f24da5d3286e41931ddbbef078213d8f2fd74c6c9ffcb856c85166ca3', 'admin', '2020-10-24 12:52:44'),
(364, 'fa70e7a85d04e180f38c24023ab079ef58429fda56dc413beec9016d96a9162d69bd921efe52f928e7688fd2ef05ec5c824fcb19e93571871bb22fa7199b1759', 'admin', '2020-10-24 13:07:43'),
(365, '6fc53bfcaa8c44e10ff93292cb1e39d576b6cbad2a0f336408f6efdb1961d0a028c1c76dffd3239b097d4f68fb7c88772b2473a49e7dea2348ef0763638f3278', 'admin', '2020-10-24 13:09:11'),
(366, '535d2c512f3b0f7ea1791576bbcf429a45854322bf8b281bf612068bfba7f2d737df61ebfc2a6a2137ed81f3a6489cb57fde8cadc998c222e59ce2fbb9d5632f', 'admin', '2020-10-24 13:11:10'),
(367, 'e621f9778280ea5520d6ed586759aca785f527fb3d2787a72b4ac581ebb7fa6f0cb58fb29cb72476db394606026db370cffbb222a9280c973603d2ceb66b77f2', 'admin', '2020-10-24 13:13:57'),
(368, '59528f88386b97edcfad1623eed1a5af34fd1babc03eab4e5a90306438da1a6b2e6e759d62dfa31d6d5b3454615269c0f4aebace4b12bb8faca6646286a2606f', 'admin', '2020-10-24 13:15:59'),
(369, 'ec54adaa26f9010e795f3c00723290bbf4430b3aeafcff86f14f992f57cb22f188e4d7fe805361257ac0a51f76ab51130ee992b03f96f66ecebb783287939631', 'admin', '2020-10-24 13:16:50'),
(370, '7cdd4223f3ff5d446767dcab25901577cea696c1aa45a3b8e085a685891d7a6db5842c34a396f8a87c6bf5af3132f078f0231105d3424b8771b454e262fd9452', 'admin', '2020-10-24 13:18:43'),
(371, 'eaf906d43fc5eb71b5de3979271ee8343257b1339304cfd6b1c6ad0770fe2b94d7cdf50362c76f9e4b0922a3c6c5b2781f36d527e660f73a06389c5e8793b3e9', 'admin', '2020-10-24 13:19:25'),
(372, '7e7998c6445d2cf793368b67c9c4b232c7d9977cb8f07d8812c5ef353520c7c08add5f70b1880b432f89cc72f32a5cb491150da8923e10d02414ee88c11e043c', 'admin', '2020-10-24 13:21:37'),
(373, '0fdc5e079ef041e731202c651de1ee2303111c1e981944b616b8e8c2758770cc5265ebd00ad4c482602b237a81eae0f2ed22e1e110cd29a2edacd32099b07a1e', 'admin', '2020-10-24 13:22:08'),
(374, 'f57430e33a505246488f2c17e8676feedbcd8b241a2dbf98c32be9169257c66921acbcd95408a2ffa06448dbaaf3f05711ce60d5961b25431697e80b54f9c8d4', 'admin', '2020-10-24 13:22:49'),
(375, '6a24b02777402bf12e551f1fc68f1a893806e2d4b841047fed7ee1e94ece7b7844432c4eb482e4c308bf2db1e0dca08d87064df300a283ced3756617ca6552d5', 'admin', '2020-10-24 13:23:13'),
(376, 'c6fadb4cc9c7a8896ff233465d25eae5d59118c50b9bc6cf55d6a9343eddf7cbaa800ba5f387ac3e33a3a886775c60bc497a18ecd16a2cb5629b57110f659cc2', 'admin', '2020-10-24 13:24:33'),
(377, 'b71cc2853bf75f7a3a8d4a390061a1e7b29afab2aa5f96b77193521042df0c897990c41e4c1f4aca0f5daa2c894f7f12754aaeaf8a177977afbc3f8638cc6a1c', 'admin', '2020-10-24 13:25:20'),
(378, 'fd7c2b7a27a0d597f5cf8ae441c0337f08904b5ba4b7adb5f4b182dc96b292d3a61d6aab1856d69ea12ec1c8081b6481e1c7fd7708805f5a15ec7dbe271eca3a', 'admin', '2020-10-24 13:26:09'),
(379, 'c5719e4c7bf70ef23366a7612e5a90f05b8f38dc38203a5cdfb07e49095fb80ee92d6334f9f22db9719ebf645ac4c435042b6d6bb102f7fabbf0871b75929133', 'admin', '2020-10-24 13:35:00'),
(380, '6051c9a85db192f3d890ff1cdcc3a56bd2deb015f87725c489503ca2d73fddd887a74037773ccfa8c18046cd1b820283031e023604e896fe442f49f510e5b64a', 'admin', '2020-10-24 13:38:07'),
(381, 'ee0c32dfd0a41109268da07848eb07e4e6b58f2325a6761002e7ca33666c496327d78e4755b88788e5b1e0a2b63916d03961da05af89a30d760ed1281b6868c4', 'admin', '2020-10-24 13:50:43'),
(382, 'fcd1db118e23dd34969a936aa5c5a1c0674d6a55a68a53b43afc8058fa22e362cc28ac0c24fb2177d87e80fa65a5cf5568892d6d6e667b3c1b15e4ddc85bd249', 'admin', '2020-10-24 13:50:50'),
(383, '1a141e49d31030c06960adea98dadbd0d255cc28ed5ba355484b88cc73a38485188db3d22760183ea61deeb8ae2857c9c8f6adec51849074ccbc75a5d63bbdf8', 'admin', '2020-10-24 13:51:03'),
(384, '53e9298d71ee1075a1fe5a17beff31ed8c91af220fd7849234624056ebccea61922d90e8c4c74ef04e92b566ef19586791b1206e93175e84249a38ec96459005', 'admin', '2020-10-24 13:51:12'),
(385, '60400ac95216f786ff8e045e34b6656356293e7be8fc4bd0b3d9fa7e67dde0849ed3b1ef21e6fccd18aa6cb3c82f59ddd1d7b3ce24b1b19d84bddb9580bc5016', 'admin', '2020-10-24 13:51:31'),
(386, '89e911644fe1fe7ba217d7a08639dc6956670fa2c02e1bf759229fb1f4e355b5cfcda43c46fe9a48e206561d8e0df85222db1ad37df4a5071d0d47050d763a2c', 'admin', '2020-10-24 13:51:45'),
(387, 'b80ff0624951355c6718c36da10a0999679f20b7d750409563f0e0d753ea942a310ff67f5cdd17b33be653c37cba854d0b568f7857fa16c4e73f1164ce523c25', 'admin', '2020-10-24 13:52:48'),
(388, 'c017438c4b75a6e6e5acf7a3a94cc9289886e5cd7ec70e95260976518a3464a91810a91eccaa7f3b7ec81b495dbbaef342e2d143902056615cfc20e536d18ce5', 'admin', '2020-10-24 13:57:26'),
(389, '2b00efdee9c0d9a4c6b989bc45346af0e7415dd1661c4a7d931f71c596d4e72aa36b398d276a6ec3151188cc9d25e285bd8d8185cbe024d507c3300d71438081', 'admin', '2020-10-24 14:02:31'),
(390, '8dae24d1d61a4f805f1ce0f490172df25c9fc04bfdad67a89b08c79a7200e267a848290d5304d5fbea850be78477265add45290f53e050488529e440c9b87683', 'admin', '2020-10-24 14:03:54'),
(391, 'd098602723388094b8512986e977a9c78503cf345ac98a029ac28bb1480eb8bf81c1627f2c675ac7100fee7ed4d4a753d8576917ef9838097026d4b4d0a7b152', 'admin', '2020-10-24 14:28:21'),
(392, 'a5fc5064b452617add7ff8471a4516041e2b2d3b1995269375f9a73362c4b7489badf71f6b6e5c636aa31a6754367a9780eb5c254a75f58f535837c20275efaa', 'admin', '2020-10-24 14:29:20'),
(393, '23308585dde5bbd81ea8e1ffe885cb6387d1f7b46384194cad01598e88acac7b2ea2e2229034068dc9c298425f9dbd793a70a5060acd2aea5972f3ecced59c87', 'admin', '2020-10-24 14:29:54'),
(394, 'bd8d1a82ff98bc7773399aa22a4b45ffcef4623e71b6d3ad9c192f34a749674b8e200969162dfa530ad6ae13954a84f06ee0f133f753a43a4cfb42180e708135', 'admin', '2020-10-24 14:30:17'),
(395, 'd85cd215785e3f872496680513c95eceacb3f42464e09735e93d2218c77b1926751efb021bee892564a1b6bc150ceca8dc02fc063186c457d96fb988b4e09811', 'admin', '2020-10-24 14:31:32'),
(396, 'd33df6bd7168204433922e5ff03dc7baf43c75f9f7e8a1d06f1ad205f5bb71638393455c705aed4a42fb27b05b6b3f9b9e4711a2c40a8120ac7342298d9b7eff', 'admin', '2020-10-24 15:22:42'),
(397, 'e6b7e6b538215df6b1a1f0a6d7532586502823b4a0f407568dfbf33b2cf24520a128996771c4d7bb495651bb5d813bbb0e22ea72ed9c0ea0f2b47aaaf4d0ac56', 'admin', '2020-10-24 15:23:11'),
(398, 'd0a35fc56b55a10fd7502f01e06c20603a7c5928ca16d9be60f5e857b48c2a2cbf527c1a113ba7c7a752c45586b618039bacaca68dadceb0042957d1e8d020dd', 'admin', '2020-10-24 15:23:42'),
(399, '988233d04c43c5a2b9075312db6712dd728bc231f19296c2546aed888c1a96807f4ba0a579ab6c5010910c00530bd68084612d000d8da1a46cb8b99c7d6b2137', 'admin', '2020-10-24 15:24:05'),
(400, 'a52f0bdc56734a8b431454c6b6564170681e71700efb732fd6322aa076cb67404e00e8ab109777cb6c14f5aa24d7a49c142050392afa227719ba4bb7a75bb8ad', 'admin', '2020-10-24 16:00:23'),
(401, '4c8450176ab9a64b3cb03476e5b171aaab6e5a69ef2c815309adb4768b386c10eea55290de18fe370bae2bf7d4f5c067102353d8a4b1edc47f14b58fb1da3ea9', 'admin', '2020-10-24 16:01:27'),
(402, '0f07559afba09420bbc49cbf4c3067ced6e047bb5df26c589b461f9b7a1300ba010f564637dcdcab669efb14b09cd18941c938b89fa0fccb2705023d34958887', 'admin', '2020-10-24 16:02:35'),
(403, 'b5ce2548711269d938b43067a8dbc8e0bf13b0dda7a4bfca6f85b4275b91640ef52fea7eecded9af3afc1a638aef7682384ffbb550badac1d967828ba03ab377', 'admin', '2020-10-24 16:03:20'),
(404, '22d2444aadf2b9c2d69db55011eb317a3df59d19e8fa6cdb2caf405746330b81853e351f2a74b5e42a97f7f91c3c5fc99be1e7ebf38c5794c0c68bb46098f2a2', 'admin', '2020-10-24 16:04:15'),
(405, '9d9101e6bd716ef569725bd22606183571fd3e003f69faac2dcd22913b63f0a994ffda03fed630b74b875b838c2ad2ce0f762498974251df32c750667c56ba67', 'admin', '2020-10-24 16:04:43'),
(406, '82ec4a4800644d24b0e840947a0f9436b22c0685b9ffa594ad9384a925df21bf44bd40345f43da80c9b697318af4c6e8dbacbf3325ae406a5c960a53f3ce4e9b', 'admin', '2020-10-24 16:05:54'),
(407, '85fadae03f531a3f102d9e7b13db75de29e4e51717a7188881f8797d667df32672c7be26d033e51133a88a421a8c41b057a2e555484c2a6a03979d94788b1070', 'admin', '2020-10-24 16:07:17'),
(408, 'd57f02be39d946afa368ceb9bcfcc3d40e4496310986586ed3289f2d28ed06ca55510c444ced01149a6c3e03ce0e40e6954142d3e1ff49dff9240560f35d010f', 'admin', '2020-10-24 16:07:36'),
(409, '9290ab2bccaf6bbd80da9b1ce5284a982d36161a202fb2b97f592d7ba10015e7df0b040bb5d25eae956f93cab5da3406f11c2c2a9a58e249198080538e242be4', 'admin', '2020-10-24 16:08:35'),
(410, '27ec83e04cb555b20c87568ddfa5fc7dddbe6a07c1aac6af91e3a1f0f72f43bb9234a0673d4df0942799597b559c627d68e0772e2cb550a47fff5fbf62bc15a0', 'admin', '2020-10-24 16:14:44'),
(411, '2c095e92e248b61ac25f129cdd5db33d365cd6c201047c03451d4756ad0a8cc39aa015e3325cb22d17dd7311118545019b996296495af087fb5a0555398fe13c', 'admin', '2020-10-24 17:57:16'),
(412, '96a1210f2a3fbc693e4e8c545d1e4257fab7db23ad0c5b0ecd04c57b0e79427d56382d12efc4fefbc27673421ffbb5404a1b9f4544f44f8d6b247bd89e6f230a', 'admin', '2020-10-24 18:00:01'),
(413, '9994226b3c17ee7337754c1d5e9430b4e7cfc0fcd3fd58e9c9d8c7c7d267c56850594e8af7e8f33726c1d9c3be722da9aaceebb15444c7523b959eddb4c36347', 'admin', '2020-10-24 18:00:45'),
(414, 'e6e3576f90d7961b0b4c38574cf5f466d32bb630756382ee87becce71d8f2165ed7c2d85c4bc60f8b98e03421eb262007521ab6c63c362eafe0f32af49edd95b', 'admin', '2020-10-24 18:01:08'),
(415, '67edefda32df580b2caaffe6a74eda42fb9198b1211dfd610852b789e7f5f71a892c9359f0ea528b7a9d81bdaf1cf8e76a3750a88193f72badb8897a62ba79f5', 'admin', '2020-10-24 18:01:18'),
(416, '092488f85320d9f37d8e96216bf672dd95f8ec2fb23a9aebc1fa754507d397de30bef4ae8dab85f1839b707e28aa2dd7712d0ed9fcb1a3f7f0c40befb29ead28', 'admin', '2020-10-24 18:01:52'),
(417, '3fffa92c4d6d127b902f1fad4c2b3b7577286b6bd08dabdcbce999d3567e81799308904ff04290a4fbabbc824f1d247c245a2060e62487ec9fca75b600b3b2c8', 'admin', '2020-10-24 18:02:41'),
(418, '4fa881fee289291af00c9e82ca660346df8b4038e47db33fb4a64860b02f7f2f0929dd2e84209852b265d07ed7f2fc64f938c55bb3bbbcc77f311742dd68a891', 'admin', '2020-10-24 18:03:05'),
(419, '0f906eaed9445c4137d54409db2b8d2bb1d75439a43e09f8726ee89a63eb503e84eda3f62517875dc0607f580c606433433d86ab9ad995b9d1b96a33c4f0c4a2', 'admin', '2020-10-24 18:05:04'),
(420, 'e0bcb4a2749d269d9b5d370e49066b97e92cbc99edc7645e96d714e9d278ce87f627de7f50617492f513f3e43e96c07b2a75722cdd4b1e565e0c84b46e640659', 'admin', '2020-10-24 18:05:49'),
(421, '76d1e8d742fc3e6fc8221a5020a766c6f57fac547db43432091f57cf2dd80a60c79fd68fd744459baf54445fe59c94dbbc28750c5c27f092bf3a9f541c607555', 'admin', '2020-10-24 18:06:25'),
(422, '043b1a0dcda5f606f6adae9976e84376cf74776b806c908ebaf4e08a1be95c6a0f9d9043ef5660a0ba05af58a3abbacaef05bb3ed729afc03acd0cf1bbe52c09', 'admin', '2020-10-24 18:08:17'),
(423, '4825b58d9847c364b1be1677c3145cc4e77f8605ce830e2140a23d9f17d2341222979f7661e9b96f8a12d6040d3adab9b7f329340d0cd886d7c3e9b4cbbf4e7b', 'admin', '2020-10-24 18:08:57'),
(424, '84f8d1ac232f8dfd9062b23cb2040ef83bcd862869702a6a86329d135665df5d44183e8f74c2f0c81ce3b6de5da16689057730a455a6275bcb1550fdd98460e0', 'admin', '2020-10-24 18:12:11'),
(425, '984823b2765deda0bdfd3256e82dbfc4f11ed60dbf2e0a70a7acfb5d7726529a56307ae37c79bc1e26218474a418ed67bfbb0edf88d45562e119a12b08755f54', 'admin', '2020-10-24 18:18:11'),
(426, 'cb396cab8e86f6a147286757fca567f1a46777bb61a744eeab39513bfb18fbc0fe9425b938cb84293527e324d0ed63ff3726ca4b08c8e710ba47243ebe275ded', 'admin', '2020-10-24 18:18:40'),
(427, '3bd2574d1ba5bbdfcd05d900dd30993d48c5f21df8133d9ca7c47ba4627221991a5801c96475adc39ecb527b66155ef92a6a060086942172a656c19556a87c1c', 'admin', '2020-10-24 18:19:33'),
(428, '55e24b4b66e5326177866d628c6d6386fcb1baebaed7edce987f26547937bf0f4738e831ddd22e553a44be92fdedc445ffb607ce9bfe498dd2f5076c77bfb714', 'admin', '2020-10-24 18:21:03'),
(429, '673081d0f7bf655afdfa5adc11407f5f757c79e3116251e27a3cc3e59a81bd2ce23fcb4b1fe7fc156db693ce370c0fcf66aa70a93b22f64bd62b9f0a1a6db9ef', 'admin', '2020-10-24 18:23:23'),
(430, '5f8d34316f73459c67b5b9956ea7dab17f37cce4309ac2df0b83424d1b13b60695f9ae29f58767d72ff8457f700a003c63dbfb5d894354568e0c63e21b1b8d83', 'admin', '2020-10-24 18:23:45'),
(431, 'f6e80569d1ec1d33e9e65289d9954c611a3ed40e08669fcd8dec718b53e17e2edf1c18f3b39177d10ec99dbd2a9206771bf76bd92b48be74d208c805b641d7a4', 'admin', '2020-10-24 18:24:50'),
(432, '4a1f236711b834e33f769e95fd9c1a5684fbadde599fee662813b037c5682da837809734f871ca6c72fa8c655ca45f26be8eec82a01b8b83bea51baa9dc3e1bd', 'admin', '2020-10-24 18:25:15'),
(433, '56b0b577e604e5dad862f74976857528be3a041b2fe50ccb32148115edb82a478a9e53111226a2908536874d4ee82981fc94cedb0dec5751e36628325c1027ce', 'admin', '2020-10-24 18:25:52'),
(434, '7963136534bf3d7ecb67079d82104590fb0518baec23aa3794f14a2b29cadaf0096150c198cf2c7810b7613b085432fe12daf06f6eaef568ac618d6b7c652726', 'admin', '2020-10-24 18:27:52'),
(435, '48204f5d232e7d7341cb563d5027e959f026c9941b4c4d76ebeb599214b0168a70463d989d0f482526c9939a725edbbea9a1c41ed1869d7b8dd3957b28d2f88d', 'admin', '2020-10-24 18:30:41'),
(436, 'de5701e2809bdf7ad648f1c992ddbcb5d1c95aeb11231f573164c2ff4bf70d53b63e53c2c39bcc62e6a08711cb8d97194039bb0d8abc637f146ca911cd1e8ed8', 'admin', '2020-10-24 18:32:21'),
(437, '08b15021c8ce7795beda7a5a40ecfcd964a00f9196ec1c2b49dde8fee83308ff66379687799fb2527e0a84cbe66a2bb200a8fc31988ba787a283d940c5a40a1d', 'admin', '2020-10-24 18:40:10'),
(438, 'c16d7c46498257c7a3e797ae4c441c4c4951b772f69b09037f26b3921643a8cef485df2b38003c42509f96d15f4cf5aa206b757111036e71a6ea05298eaf34f8', 'admin', '2020-10-24 18:46:44'),
(439, 'f850b8a78132d25ed7cd4cc124899315f7683871e2b959f45041129b9b07588245d5c794f7d0fd7eaf589f3d651f994033ce469472538d3938f40d1ba695f72a', 'admin', '2020-10-24 18:46:55'),
(440, 'e03cf65d373fc6c35a954f0b234c96faff29950b5fb74305796db150bab2b0eabae0edc8f4e318043b7e1f913e99580e036ae28922916416ba42e0c07e526c41', 'admin', '2020-10-24 18:47:17'),
(441, '043952a767d5f9778d4d19379e50b7d152ce6969beda3b99c7fd6f7be89184f82f7b249c9eb5e299b036f0ff31768c441f8879d18f185b68e613f6c1a6f32c38', 'admin', '2020-10-24 18:54:26'),
(442, 'dedc375da7b77646a65b0a834db44de1623ce563b40e8720ec8caa2c46bfc006d8cacb8bca922045e744aa07c11b3874d2d49ae6b11adf1cb2523b7e6caba742', 'admin', '2020-10-24 18:57:28'),
(443, 'e6981c63c938a253cd4a857a84b5e39dd60135370826cce06c50d06362cb9b7747bb0900cffadfd9287a909818b4ab02ffdc29845b572af29cd0d3a6e2097189', 'admin', '2020-10-24 18:57:47'),
(444, 'a015f3ee0116ee40df5c53ca540882b48e3e4bbdebe429c5e2f10a0c1e977febc9b62d0b12f2999304c7b41d8e3ea1e580eb053c4886909965f316dd43741f71', 'admin', '2020-10-24 19:02:06'),
(445, '6d1598e9043dd93e6747bac516d3df71d018d382aa608cfa0f73b3d8577de07a0c0ffb54a1cb9409d3b91e5e7422420a75661db555697b41a4fcfd1a1b30bc16', 'admin', '2020-10-24 19:04:49'),
(446, '965c430a4a002fc7269b3a4402c3184c6547a355d68467e1b226c429ec8ac6830625ef2733ba8a58f2eb5b9f74cb7c951b81ffec779be485cc4dcccab58f8590', 'admin', '2020-10-24 19:05:58'),
(447, '15dd8cdeeebd56b92a87b7a45142ec703d4f1a3180d50385d8ee5eee2a73435a5816d6a5a7cf8868f9e66ddb4a04ef906ea9fe773090a6680fb9ce586ad0598e', 'admin', '2020-10-24 19:06:24'),
(448, '9370f26aa2d69f54ce4cb5d8e251c5ea34266871521b41f52955a45c7dc9b4e9a6e99247d8880ead799b6f5d465eea32a8919452d3c40a9d7a6f340b4c29ee63', 'admin', '2020-10-24 19:07:16'),
(449, 'ce94a7d20f2ad52d84182872176d11449767680bce2a2ad739a1c5d731e0ce1bf6c79f421ee7f82cfd5669286f9eccf3fcd2fc7cd689a65544d5fba30663d781', 'admin', '2020-10-24 19:07:55'),
(450, '48b1f3f27f5e5565d6ebce417741db309af5c4c2577bcebaf7d96c0577f2dea5052ef891f678ca330c434c33dbf75d2c54e58813b1126bcf869e562267608bc9', 'admin', '2020-10-24 19:08:34'),
(451, 'd1dafed1d66bb47ed3f95dc8f273e8b8308a50848c8a6c930051bb462210b480ad786d6bdc5c228833dcbaf795f1e055766df7479c094e156bed2a7fe9af4535', 'admin', '2020-10-24 19:13:54'),
(452, '6b543ec6aed4a81f90665666db338aedf5edea721aea11f2204b3abfa41a1ce1f21e56826832f004b9771089f5a632abc54966c02238686ebffcf44ed01f11b9', 'admin', '2020-10-24 19:14:24'),
(453, '9afcb125650988e34d477fe3e654b98f6f734ecf7b5e9763d335fdd35b8a5387d1f43d512ae7b0b1852f93ab4cd47e1ed512a6e6d64d63cdd115b7b7d6b5509a', 'admin', '2020-10-24 22:20:20'),
(454, 'dc86001622429f2be7bf647f80c289712e69404ba155a2bdb8f15224a34cd6b3731590bc2c6580f58ba635c8c6e53b1df270e8248149178325eb41b6f970b967', 'admin', '2020-10-24 22:23:11'),
(455, 'e94db2bce9881446ae084aa0258cd1b41d50c8cfc033a0b7a15ffb4b7c3c3431b262c36d60f781a8306ef430bcd42683513ee3523d7354c78a7b4a8be173da68', 'admin', '2020-10-25 12:44:41'),
(456, '2583c5f4568b6209e9ebeb1b80f6f90fcd6621ec6483db4a73ac2ac19b08ec07935c34a54d7b35c1e2e814094adcb6edcfff301ace11223ef79d4e28c0802ea2', 'admin', '2020-10-25 12:45:12'),
(457, '00c04be03e4088d63badf9235021a449f0c04e7a3bc087a5abf0b52c6b02e64581834f303880575073a1b381568a9cf493e1121b93f65efc2b3757be0565cbf7', 'admin', '2020-10-25 12:46:15'),
(458, '534eed9352aa0e8214cd1319d57ceafb0314efe6359a634abb2580fab96a7f5a56f5dfac750c94fb1c3e1f166f2f18ccdef32dca04b542350cd4ec3b1985b458', 'admin', '2020-10-25 12:47:01'),
(459, 'dfdf0913f08669e1441b2fe11b911f8af7f0838b731a52ef11cf4963a2f1eba6c1041f53ccdf7bc9c51dfb034858b7f500343fdf36de8c57ff2bab0f74c67f5a', 'admin', '2020-10-25 12:50:18'),
(460, 'b00b245416c21c57a54b892551dfbd7040f0cb379f485fc55e717ecb2f312a538da1ead2ec31ff40f396a89619d11da47fb1a8912a5bbad7ae180c9e9a5b2f97', 'admin', '2020-10-25 12:50:38'),
(461, '4320d4d1dc08ea28ecb84510325f1184c57c9d759a933a6e8e23dbb499ee2ca16f287e036b152a79395837400692d6973a7f615466e9a9af11a2be3b0325c41f', 'admin', '2020-10-25 12:54:41'),
(462, 'add324a55af814974dac867669f4db5818840407e2b18e01422e3a536756d5ebeb524b84a0e8bc200de2e2126c25f957de8831d36ef046bfd7fcd396df93d81b', 'admin', '2020-10-25 13:04:57'),
(463, '52e021848ff61bf78758518ff22ccb281d0224f8943f7fbed9fa99431bdd1c7345cdab6befb87943a0379c74516ade34f08ece6c568895d5a0e864d85d092c51', 'admin', '2020-10-25 13:06:19'),
(464, '84b5f65ecb0c864e65c8e7d77ef34db48b81eedc6b4e803c256daff5cd14e085a2da53e7f39c4d1330a24b1c1b5c9b5ff0e54c866892738d9303be90eebf9c76', 'admin', '2020-10-25 13:08:47'),
(465, 'bbeaa190082f5e37fdd7e493e07cb136cf129f345bbceffedf6326813f716faeb7600a0ddac0008832cf1d9823ed9400d871c33899a29dbc81c395dba65bf8cd', 'admin', '2020-10-25 13:10:16'),
(466, 'a75f503b5421b221544aa7ec8ca8cf07679e7b61f62a0a26592cbf730f95c356005f651cc943e166db9bbadce4242a24057d10ef6001a093ad26c75bd958dad9', 'admin', '2020-10-25 13:21:18'),
(467, 'a63d59aa91d26961bec58f058098df9ed6c0ed094ec9ad071d5ee157239f34237ccd1fae5376aec25504e51bec6458417aaa4d388ff94e459f3b1f088f61f2cd', 'admin', '2020-10-25 13:22:38'),
(468, '21df8b361349db3aad239507b7f379c5563d5aad0d5f1fec32deaedf911320813d7dce737a39759c0a131cd18fc20f69537bc21a1f17b2e193b84830ec1a0726', 'admin', '2020-10-25 13:45:35'),
(469, '65aad82007228d46657eef7bc19da6a3c260ec44d204bceba9c7f1ab5a83fc44b48316ba7776d7328194c064fbb957f460a393f9b1474c2d0665c9ce72e82862', 'admin', '2020-10-25 14:34:08'),
(470, '3f60bbd7352ee7a257b78cae61a736eebc7ddbbaa7837eb7e905c261afe025c57cd10ef03c8d8e0d401787d540c97d9abc6db85a7e512cf85610ee07aee1adbc', 'admin', '2020-10-25 14:53:39'),
(471, '3fa13cc2393f4e001ac12e357fa024d4779c8e43bae8640fcc240b991e747ab4c985aa074179a0f88ab397047d5e5a1b587539ce8170788faf98b4f534279573', 'admin', '2020-10-25 14:54:10'),
(472, '2fb3486ffdb2dd46b72666d0421531c85279616fc11f3f1bb4cc7728b7a8ece4df2bc4d4f7429ae15056ea307c6cf1c14613d35e451113488b9872c9d2863d73', 'admin', '2020-10-25 14:54:34'),
(473, 'cfe66ca6a8f5afda93626343b784e12363cf70bc26fbfde7a803675d76a7395a91f8d71609b6cdec427db2db022f978cd99ddbfd76aa577e7c4cf8eecbcb3e99', 'admin', '2020-10-25 15:03:41'),
(474, '5d461ac687efa5e58b2c15368b18c358e8e2facc13b97b93f9a9b56243aa9d2d050e2e7536330e6c9142c900bf6e767bba3a7d9a0b9e22cc63775ddeeeb53e59', 'admin', '2020-10-25 15:05:26'),
(475, '69b80f40a72c4c52672f1c63123c7be9fad0954f04c4f7f38d80752637db25bbde2da1979ef0493cb13460e6d15ae03b6fef065648fdac7b759332402bd489c7', 'admin', '2020-10-25 15:06:34'),
(476, '2b2ccb0345b09827a138cfe784bb8d445f08a019c1839aade66aa01c704f4cebddb33102a1d602312fad1b7dee1a1f3022ce3c0ebf871f846dad67c2a2577063', 'admin', '2020-10-25 15:42:33'),
(477, '155e4d2ecc0b53a3c45332699555d8a84a2a448df7a9264922ecac83fb1c089fe58106c75c6e756742a21c713fc67586c9dbea58b7db1035467aba84eeab1cc5', 'admin', '2020-10-25 15:45:57'),
(478, 'a0441ccdefda49359f4547aa17a74866722730ba3ee07bc70c95fdbff2e161ceb7ba792285a6cc7a0563d57c329f42ef60aa2bc56b868d4a0c6a9b535da62ac0', 'admin', '2020-10-25 15:46:22'),
(479, '2c0a4af17938859ca5c065f1c586f07d69c1cdf53744bb1e74a8685cbcb25f72e08d0114f107b5d0bc5ea58a2a5e9fcc2b13709f9bf751ece1551ed37ed99680', 'admin', '2020-10-25 16:00:40'),
(480, '3baeccb29fb38d85e00897f602f30e8971ed942ba665ca7649400eea9ef9adfc3071362ebc1c077ca54aa0e1922a45cfc65d8830365d4edb828366b66fadb701', 'admin', '2020-10-25 16:02:57'),
(481, 'b15af340fb8ae374c4c2d3d2668b6c695a5f79a0d794cb7d9218d57fa57b5ad6a077e42d0b5c980044b61350253b7449006eaaea2a11f16482e1fdf940e2e767', 'admin', '2020-10-25 16:07:31'),
(482, 'b8e3df060d47b3e019db7387e6ceeb53da13c5ebfd8aa17abf502a4dfbcf1e6b50c6df4f7f48475fc459c61e9a903a2c74db946a8d00f86add4fb4201f2597a9', 'admin', '2020-10-25 16:08:41'),
(483, '88ba786ab0e3f517071d707faaafe64681bee0ff8d69d303ee071433ffdab8d10b993f84ecde9298fd9cd3e7bdfa63b9e8d26cc0da0c864c020349dc424b7ac2', 'admin', '2020-10-25 16:10:17'),
(484, '10063ea0fd2618b66b5805e629dcb46a24490ce57eeac1192166ce8fb6f1cb8c457e28d0e7099cc84ff739bf2cef7aef2d75f6582b6cbbb66836fd9de04aae70', 'admin', '2020-10-25 16:12:23'),
(485, '8f1e8fb72f54065d2a5004084f3cd3e427012204df3a2d9f29eb8e7093f3babff2cf1750db865d726499aeb988bbf4757c5aa56d1045ad94b1de04786a9314a9', 'admin', '2020-10-25 16:16:42'),
(486, '9b8f3c52f52035c78dacdc59affd4ee31786a5bdedcc58e41e23babda8338e760552fb53aec584b3d47de14a770ed31b4701a671480b67969f443d8189053e93', 'admin', '2020-10-25 16:32:20'),
(487, 'c89f9d6a5500951376e8749a74894f657742b773a9a680e41b7d32b012f188ff014f9b2dacdc9591b0f67a3019f48fddb40a7bc92869524012636c1cc2593c6b', 'admin', '2020-10-25 16:43:08'),
(488, 'c0081898c224d2ceddf26014b95be94373aa81c977ba0c6ae4b74ef0866a619f7818e4c38e3013e527ec3913efc1420acfede76a593fb808a6a15b36af0892a3', 'admin', '2020-10-25 17:36:40'),
(489, '873427c20e7214005d567d1b950af2f29667c909b8c9d93226edd565f4cf6e409207bbe2fc143f26d02b0d78227f69903c5480c9629835cc35b4380f9b1adff8', 'admin', '2020-11-07 17:34:19'),
(490, '6221b04877aa135e638e2c396dd61397ddd0fd4270ed3c0ff28145f0b8b563fa01301d277fe12ff3b3319d213ce8dba6c7e789d8e4cdf5bba0b39828a18f04f6', 'admin', '2020-11-07 18:37:43'),
(491, '5c8ac92d557a93d46e7ff76f9f70559903df404a57d2bccd6cb56e665c0ea67e88635a16ec047db673240fa994b9ac47a5e315fa9cff384a00e21c2de8e3c121', 'admin', '2020-11-07 19:05:51'),
(492, '63af287a99677edc25548eb05031449a2d62302b57c08464a34c57fe140c756dbdd26e7cf569f5526e0d471db49b8a3043c16b46e3075d6da049d3347586c54f', 'admin', '2020-11-07 20:21:59'),
(493, '631f3c49a3d0ffc587911daf59a0359805aed7ef684159ea45203f766fc4e5d6cae95045df1d4f16f1ae64eb8814a426324c4d77ed24c79875ea091ed4f7d91d', 'admin', '2020-11-14 02:53:45'),
(494, '2fba123ea17b82f4e5b57158c742437c69ea8b037ed7237f19df2cdd2d75b640cc3d7dd0a14508f5f14bd47124cf9e0c15d5d0a788cb312269842e5c2234bdc4', 'admin', '2020-11-14 15:05:08'),
(495, 'db4d2641c8dd19a2de5a4f27e9feeebbb532dcb49278a05a06c23442ae4a0b1cc086cd5e3c50634eb27747125b4955e383f04413a9c396b47023ed68ddcc2997', 'admin', '2020-11-14 15:20:08'),
(496, '16666f51fb0e11aa1e433253a1e00f55ed75dd0ff5d9720586d13f75a7110819eab2f50ef1217e04b659687ba36158034d7166319b431c7176c2b1724d176901', 'admin', '2020-11-15 04:20:22'),
(497, 'b1588475d5248582d08c6742ab6e6796504155acd106ac19868eb3d4ce7bf067e33f924c8cb4b277e54eb35a18a4d72febb0bbc766296b8848ffa7094a43c02f', 'admin', '2020-11-15 13:45:08'),
(498, 'deca5ea794f3239ebd89b6c101699975c328dd4f62fa281e6912305229fc70e921ff06e632ecd343ba4d79e0bf8d65f1d4f05c20bcabc4e3906e1a4e144fe4e3', 'admin', '2020-11-15 20:42:12'),
(499, '35cd7ecae5237038db2ff9d15a73e4c11340b61092ac1153ea3b708440f94a4088a0ec2b559dc17b49f0db3a8827d19106f9bd4bdd9a087971fd7917b70fb289', 'admin', '2020-11-15 22:03:50'),
(500, 'c5ee06277f713e03f754244e870789246e8af4e346414e39d23a9965bb07189232057c1a7aabd273047eba422588f11276f8c3bce72addb0f90d5d5f0ff47152', 'admin', '2020-11-15 23:38:06'),
(501, '07c1ff52c9873d909736658244527d8277ed4b131f3b1a1108ba5dc0d5a2a3de68d4d0637875a7c8fafbbae2223d82c836462cc0f5c148ffe3561802a9cfa14a', 'admin', '2020-11-16 00:44:11'),
(502, 'e879ab966300b4219b92db7007f0fa468570e4564ecf13c7dd6a008766b609aad4a2733a1cd12d0de085f30516552cc7aa0a22729e64644c37e567988b60ab18', 'admin', '2020-11-21 17:59:14'),
(503, '54da1b62d38f4a3ea406017e912a9b084a746afacaac16c8d9416caaef64d4bd970f17bb51cc0b82d153f76bf999f361260c27bf3c2db11ee6d3068be81d00c2', 'admin', '2020-11-21 18:06:59'),
(504, '391d2548fe45ae338b30ba8234b61d0765e68002f002bb489958e1a3912dc8f60ee37895d461f22502e744571efcd902d7d5678050627ccbc309407bfc5cd358', 'admin', '2020-11-21 18:10:40'),
(505, '62fd46502d6d6d72fbf847589e1442db4eb9b6a2d4efee390920f7f1a9009e3b525ed50449be9ddb5773b1e5c9c553d6b82a73d02bfdf3579ab5b4e2591ba429', 'admin', '2020-11-23 21:27:50'),
(506, '56547f647cf2f1a35f57b0838968183441bc68106a4558d6dbd79a59f788d34b1fbe106d7411c03b82d0167e1982fa308a4adbaac86e5ee766c0607b6c84e143', 'admin', '2020-11-24 21:40:32'),
(507, 'c245a4cb0401a70b333383ead596e136c9263c7baa3a62c43f5df53581cc95147ec9aef8b8121ad9a3a933776df6f449052600129b7d0589f2b3c7e058493fd3', 'admin', '2020-12-05 00:49:27'),
(508, '67496cc414a829e30b20c60a8d182cf7a0088b5510071da06a5bac4b25381a9368338cbb7030d4a4b6b482902c111ee955a4ab40c0d90b37273a31e3e86e0030', 'admin', '2020-12-05 00:53:47'),
(509, '28b966f05e1009743dc13f3109b5c2f041227b12ab3d8dfec41c6f940974cf907b5ac691ded5cdab0d586e1156c487e8f68916ecd3a8692b3c997deffb310117', 'admin', '2020-12-19 19:08:48'),
(510, '5f1a8ac8d40b940e97c9c0254c8adee1b56132ec54eff0bb41c7288deb4a40a1bd72920c9b10b842eeab26b0e57f45cb63f028710218c15309cc89bd59b3ee84', 'admin', '2020-12-19 19:27:58'),
(511, '79ab84f5324e99a59a9e18ed0c9ceffd21914372af438e944ad7bbaa8f475dd15373d652ddbcb8d03185743bbe46dca22cfd9366a514b3c914db82e8a92ab94c', 'admin', '2020-12-19 19:35:18'),
(512, 'f0fbe848b020b423649a50c09be32335c71c1c6b67e0860675b62602a9592d18b705de0e44361e06fa32d29651e2cbbdb408481f6d0b4266db0d08509e559ea5', 'admin', '2020-12-19 19:38:40'),
(513, '1a4e858149b54cce46d718a4262c11508f1636f2afc01bb725dc443dfdc5f5a6e1141f8efd34a7a292df48aab331109f310bdc02e1e6cd0451a48c433e4a3fc8', 'admin', '2020-12-19 19:46:36'),
(514, 'cc3c490f47fd20b56670a76cf3d3d868f665c541dd4022d698d3a2bc3e795670b09a1d60df0306cf83aaaa362071cfcbac58009875eefbf5e71a04053efde449', 'admin', '2020-12-19 19:54:31'),
(515, 'ea0aac16e34a342651d040cc040f38f6413dfe9e42eb76abd8b3926814e78bbb9e351b57aa38e92461de74f57277a79cddc876a783121bb347da97bad9bb9de2', 'admin', '2020-12-20 16:13:27'),
(516, '71da32be0f227136c5f551c18e4f3c155f3bf3d4b73d49d192f94590ea856b4794048b2f343f74dd3504d03f5a7f00415ea377ef1dd7baae71066d6506f8ff41', 'admin', '2020-12-20 17:07:25'),
(517, '0354671f32c828cf7b4bf14b8c6f1c688c2a7e33dfb5232d51d6c960ab39afa6b627421c7770a4e742683e0bf9a0f96b8ec490e8a25cb7ae9f907fc0e9e74734', 'admin', '2020-12-20 17:34:48'),
(518, '74e23d818d3253efbb643ebae6c36f3bab54d259a3fcf13b63508e4f5a1bff0b728430c78554829956e625db1e20d39a1ef3ad750895620a9b02fea818be9ef3', 'admin', '2020-12-21 02:41:12'),
(519, '5e277b9594b050597938e8dbd294f81bb799bccb484be2dfdd9eb3c2357f357fd7da495d179b8a7eb0e993829010476c9db20f2c1c355fac74f3450c9fe3d9a0', 'admin', '2020-12-21 02:51:44'),
(520, 'b9bc9bcf450414340eb016fa81af6da9e2b81a1a2df1b555f5a0b93191748ddb5add4e201739985177d27826baa58bbdf7149d2e5746960f363076e53808f998', 'admin', '2020-12-21 15:02:27'),
(521, 'cad52345fcec84b535b6fdce16ad656d93852bc92f8e5203362bd6215bfab9b414ee61e3b11c77c862b1255db933a486c0e04deaeda53d1002eece7c194c22bc', 'admin', '2020-12-22 02:10:31'),
(522, 'ca006588584b54eb003588f2040741ef340ddbb7b9ac8e28e71e128dea1f51b36e4a0ceceb740bad7e2aa329963ca48c83a91ac68500ac62b6fd34b289002844', 'admin', '2020-12-22 02:26:12'),
(523, 'aafc2a81ae79c5c05e70d493c7595afb01cd48ddfbcd32c76e4edd0a8b4378633bf64dee3cafb397afe980a59ab8a355cd168c9fa68bb6f33e0a980ea8f34b7e', 'admin', '2020-12-22 16:42:50'),
(524, 'fa7a57a7095aa0361f48b2de762390dd0e981c0317b1f48ba4dc9d139e0cfb31709227e0468dd5426bca520c8372bdaf3004df5d147be8c5c92f1a496a6d1cb4', 'admin', '2020-12-23 01:04:06'),
(525, '5b574c05a59eac88ca58965c6b0c026fda17a8e2224facaddd6427eb2ba26d9611ca03b2662a8d5430bb873c666050881debe63780d8a3aa4c21ae636f334c80', 'admin', '2020-12-23 01:52:02'),
(526, 'd87d484b3a17d6f953f0e923efeb1f61ce963bbe5e65e598171064f2b94d640aedcb24c4cdb916276ed340ec55770224ea8bf1c7e6cf602741e2a8473261c3e7', 'admin', '2020-12-23 01:56:05'),
(527, '4b54f76ecccd511a5d0e6ef814551271634053719471cb8ed8eaebe11eb1ceac5f174e99b8d7142e9e54a49f56d6ac7ff9002763c31db8c646dfb4be70a68bd7', 'admin', '2021-01-23 13:34:52'),
(528, '34dbc3bca2a431db75b91a5f3fbe0a35b66c82dd81f65cf3aae34a3440e64405a9290b253fb90596bbf0e54133c3379431d6235bcd133a9639ac0db156524f51', 'admin', '2021-01-31 14:16:26'),
(529, '035c8ef5f3d2dec4c322a59d12232eddc343961cffe0f3648613a89526e69c152f3e5c4add35874822117f0e16709f94e83e55aec8882a4b89f1240988c58037', 'admin', '2021-01-31 16:50:41'),
(530, 'c30a5a4ffeebdc519d75017bcdc4072251a735dd6f991071d3d15b7a0be739ffe97a478e1bfae6381cd9c227fb6deaa9d78c0b867048d289ddbf491dbe42a25b', 'admin', '2021-02-01 22:23:17'),
(531, 'afbdf016e898b605893d094910a26296efa1d3bea2fbc96db7004ce7f27e2b6f005a36e167914b9f315b4660c64cb69338e38b336a2094b3836fa740e6c146e1', 'admin', '2021-02-03 20:12:00'),
(532, '5c4d7c18eb4c056f299818116d543161390b2ef6b43cfe1c89ab50b5b80fd7b5e405bb30d2870cc1348c893664878bbbabbc5b6951c79ba038304cb2358c1af7', 'admin', '2021-03-01 00:57:23'),
(533, 'e6aa6bb72200ed92dd77f84a6ac1fe160c60dbf10eb8a1d0fbec5f874084bd1f93e61d04c3c1ca3a1a538259ad1e783987e66e3cd00610fa4e39e4cbc983f741', 'admin', '2021-03-01 01:21:10'),
(534, '784b4e611fc1193bd8edf9e77ffde493928b430ee7090e03a00ba570fea82350294221189f51f0160d6381e76933f8b34da750ac50b547c5d11d895aa38f8f7d', 'admin', '2021-03-02 19:09:55'),
(535, 'f667ca0c0092749b829337e58cfd7d97a9d4ad9a81c91bb436969ce37a53be77462c4fc4b02287967e44e8763248cdd9691224f57318cdaf9bdb6f67edf44499', 'admin', '2021-03-02 19:39:50'),
(536, '0f9b160a5e8e4abd819771dcfd70794c31f4ee12f87ee2378b59e4026a9375ece44a320901eaf4025d6719054e5c13f5c934af5095d699f868abfcd9ea731e71', 'admin', '2021-03-07 19:17:51'),
(537, '9c0f7c44baddf3f64ed0a97edd6844eef5ab416a700d03619fe6b3ce1f65b12b34752436d59f4981c9d12c28994b28982ca0c3492c3a87da8a35903b0c2e200d', 'admin', '2021-03-17 00:57:49'),
(538, '40399211376e85d15a78f53f9dad3832f53aff20d3676015a8c71c416a795a16eeb59cca13f1dd4fdcc19e3fc43bfef40bb471efbff97b2a0bf8b6b3a3c93b66', 'admin', '2021-03-17 02:54:26'),
(539, 'a3ebac8f2ab0c3863879e10d54c11b1febd7f3198b7649dbf204929a435174ba63087f4371ba142ef9a3a7b365fb3f2655c83c7094b8ceb31f579a50805873c2', 'admin', '2021-04-02 13:14:09'),
(540, '921b22e2e1ffcabf9dbc9c55cfc5d4192207452d6dc65ff17d1fade65835f4379588a097c2fe5c12f8e45078b65199c12f40e94201c12102055d037687b9ba7c', 'admin', '2021-04-02 13:17:29'),
(541, '5302e6ee3066ece612e2ee69ba37b4c476e09362b8217afc42201a351526a64736b476ef0ec3ecf88e170a2501f6feb11f290be6c6e33475303eff0f276c5386', 'admin', '2021-04-02 14:00:55'),
(542, '7a44b9efb93b721b75e31c8ab595e0ab803ba4d4e7bc7dfbba8f21c224aaf4096482c25668aca50f171a423df1768b3b2c827a6b876744f94375a4cec5dc66ae', 'admin', '2021-04-02 14:02:27'),
(543, '84e60f5f1b0338e4fe4c3e600e464d74696ff0f1f4ed1d9a283eefa25dabb970a284476fb6c6a2bb97c316ca46121c5fb0df44bb41f84ba3397b8b684aa64950', 'admin', '2021-04-02 14:04:16'),
(544, '6513faf1ab0bdbe8c2c9caa0f5d9a3f2bcb9b6312735a4bfecfe6ef2f1989b5c4700fb4d00e8b7c58c8b94becee3ec0531848f9c8e2e4b96ef32656d2cd01c6d', 'admin', '2021-04-02 14:07:12'),
(545, '575bbbd6fcd443a27231909c159e3c13b769a1c82296e4144614189a0b48406032de809bfa5db8215f143927f03f6dcbf268d16c753d10ab89d7dca31d2db518', 'admin', '2021-04-03 16:03:46'),
(546, '01676b7c651eb13136957d4854dcc12e65666e7164fcf3c36a1030aa6cff1cae29aab581bcda04294646299e88a5ed95acf7d932e6fd86b7c5558473fcd77be4', 'admin', '2021-04-04 20:20:42'),
(547, 'a72f480492eb680563c2854057e4fc6b19ef6b478f723ce64a2b205940334e13b0f95538a9ddbab7ced9c18fa09a9cdce8b1f2373ca858930f143425316979e0', 'admin', '2021-04-04 23:10:30'),
(548, '5119857d51ad7914b56363562a33f0cffeb657f9773d6e154958e58439d0d453427790c56ea00466be731f3c23dceeb4b0cbf21251add81a9042b9612aad9efe', 'admin', '2021-04-04 23:37:24'),
(549, 'c6c593be96790af7d76c75ef5b4cabe55a504a95c56555c633a117b4f653f9e39c7823b7c7a94aec2d026f770950fb3b567adbd4cdebcf4e6d982eea303842e6', 'admin', '2021-04-04 23:53:24'),
(550, '5b57ff3e8e45d8c07739d1e855ced698affee0dc0a63a84fb183909968d438445dcd386db00764dd8643724eec534aa73bfece5f56be606ad61464d19ea2564f', 'admin', '2021-04-05 00:14:38'),
(551, 'e0eb327c1c529bd8087c65b2a012d18abd2ecf9d483032ae4d213e7d8ff775acd8370b5e313236a197614c906dd782e73636a240d12509e4ada9c0d5fc8ca5d2', 'sudowoodo', '2021-04-05 00:28:49'),
(552, '82454b88b401235604b0d3da80c619ea71ff4d2130cdf5f78b0741837aaf89c64b1a52b9144dfcf50e4c1799da0f5f3fc749cb32b9bf85e00d03a40612b67bb6', 'sudowoodo', '2021-04-05 02:56:53'),
(553, 'e46feb2c08325935a514be4846a13834f0065addeed0ff4af5012199b5031a6c59204e059cb0cc33c2b6f856d7bcff9eea66c70fc3335551c5b15b3630a5209f', 'sudowoodo', '2021-04-05 03:12:34'),
(554, '2b43350ec024a5eb7a96f13aa48a97e16e476a8eb460557a55b4e11a879abd0a146b794beea141e86940ab66e048318fa143360c2d324fc438934ea3b07c9177', 'admin', '2021-04-05 03:15:22'),
(555, '40e33fcef1c7072cf4778fe4537886defd93f58ae0c7006e1d996388e0726e26e9e0c0a2269c8140ea1487795c129f3ef36d7b2d039f88c6e730a7821e5df54f', 'admin', '2021-04-05 03:17:43'),
(556, '8663845c84cf269528e65ad5650691339a693c74eb534a3210cdaab9b15acbf4dd081686f1e3a0f84624261db8555620e5437d3170da3a247bb4681afa3caa61', 'sudowoodo', '2021-04-05 03:19:26'),
(557, '7e655f814cc2b29288da7297238ec8c1040ac7d5cb8f42e9f86fc4efedde2c334e9a91db7a39fcce686c77fb2d3e6988f466e1897ead97b2d0f8820432ea8c0c', 'admin', '2021-04-05 03:21:19'),
(558, '3f576515a954a8e3d76c931b1703f13d9720f3679fb911b3a76c652ee4f23491b413c9203cfa329df1a744f57b71c601777335e675433ba138026bce0692a94a', 'admin', '2021-04-05 03:23:00'),
(559, 'ec90bcc5b41c01a4cd746a29ad3ae2d5936f621bfa5940f7bce0c893b877022651ca8a4423c4e31e6e98f010407f3522ef2e2d231fa78094cabbdcc948f8ce26', 'admin', '2021-04-05 03:24:27'),
(560, '3341daf98089b2b9095629ec727edc23efb24b8c859a90c1271759b6a7a01d2fffdd0f4d2b48d93f4c153d3aea242bec15695fc5f88ed412b737a0b5db22352b', 'admin', '2021-04-05 03:25:39'),
(561, '7fb31ca5098993eec24e2fa02208dbaaa94c9703541c379da1b403a607657131f0031c78ce584fdc7848e98b75e5db33f2de623433390b72f6f10dde57729604', 'admin', '2021-04-05 03:26:46'),
(562, '34ac8ae810618b583687a6b8f7ce164c655937ad1399d22b48952f472a2d0fe7bb9fc1449c35e0e532d5548d2a5c4bf1e18c7ee3fa4dc63093eef48af7f253b8', 'admin', '2021-04-05 03:27:41'),
(563, '92c3729bb7b742d3e4283e46f18e7d7e02aeaf6756f29a2765cf0b59cf06784aa62aae50f004a1f30732eb2f177d46138467fc4045d1c5f51678c49494d3f756', 'admin', '2021-04-05 03:29:04'),
(564, '0bb6717c7549a790f311be7966754bf6fc564513d50f3b875c1d8f88afca18272273afbe2ce37f7b7125ebf30d7488520bde0bc953e7af768db6f0e9704674e1', 'admin', '2021-04-05 03:33:12'),
(565, '7ce3dc16d9716d88c94f4fca3b5deb57d5391599c57d0cdf642ae33128d9729ac690f75837ef62f7d7fd075867e311f4b2314a4fc90b5e96624e0630b14b0931', 'admin', '2021-04-05 03:35:44'),
(566, '3625f836bb2c22daa66ea09c0a23784e858eadd825eb6513ad02ce8d8110b77975fd6f6233cac97db3de71212a2a63e66fabf948dd66753b4add3fb0712ff667', 'admin', '2021-04-05 03:36:46'),
(567, 'eb4afcaa968899a4d3b08ac14e67ea7eb80ace6c5f48f3ef098f05d34abba980925d12d6b3fac9c8273952eb0949e8701f5ca179a55f46fdae0574b3beec7a64', 'admin', '2021-04-05 03:37:14'),
(568, '8f608b108f65556211f896881b120b160b950e9355fb91a2144eb582b578df3b2e610decbfb3d9b4bc5265391bcdb149d582a420a8fcddaf3262940222d7a9d6', 'sudowoodo', '2021-04-05 03:38:01'),
(569, '67e08bf0f2167c59007f1b3849644f53511574a5171bdaaa9abba69f6752e3a11ca387487d33a996022384786b2856b8c55c7ac96ea6195c6c2f372ba553a7de', 'admin', '2021-04-05 03:38:03'),
(570, 'b9203224361029a307ce68f48d3a888135c33a633a8b62ce6299a04db7fc2c2d8c8f4d082522eb6136ed603239c03a8ddd537b3f4008c601a71f9e07ccaff61a', 'admin', '2021-04-05 03:39:24'),
(571, '629cc0a61a5a69e8277b5ebed0a0b970f6625d8ac25e88b8b9d5fe15dccb8e5cd0ccdefc45b414319bc7b275eac8f50009967416aecf76ab3240da1b5184782d', 'sudowoodo', '2021-04-05 03:39:32'),
(572, 'd099946e38e54e63a72ad194b48ac4c33629c8a343bcb9c38469c230142b0ed3136022d7a4bd09089efa5933142e7c98b217b6c6e6cadc232e14cb153a4d0a33', 'admin', '2021-04-05 03:51:17'),
(573, '1bada252bdaae8d851a2f7860c4eb919edfbf798d1d4a347fc497e95b78b8a088627c581ffe11175dd5ce0d9000aff722a422d45be8ac92f0d6a5c5f53a3c4d8', 'admin', '2021-04-05 03:52:47'),
(574, 'c609d9f954e8e521f311723a3396e261e0bbce1c3605825bd8b26ab0b3853df1e29664266d4fd005562a40a30cc516e74ed129fd5e86035ef033045136868c2e', 'admin', '2021-04-05 04:04:51'),
(575, '4b9263401422fbe4b26782a8d228b60d12e18588c24b4b481f91954721f7419e40ba480fffddcf72d6213e25a3180aa8d646e847218c2a164e8c5a9f2215d9a4', 'admin', '2021-04-05 04:09:10'),
(576, '1ae2395093595d095bbdc1fe88e06361fb7236802c811ef872e34e4a40b807c415a5299f6182dec23521bf422cc2de4efc1023046d4f6a38e768986f364173d5', 'admin', '2021-04-05 04:26:28'),
(577, '4b4d23d8ab67a344118302c79d24f460254f0b2af6b294c99e006136e16ebb2b8fdf278ffe69e4440387da30e7d411aadcc4600263e2e239711701e521d89bb8', 'admin', '2021-04-05 04:27:13'),
(578, 'cd1e8d68280009eba1a7459798b9d124947d4873e01a5ac565f1863b8768e42b74fe4480e096970a96d1426f2789eec1e3699ea7b7eabc4f9f4c18b70b345768', 'admin', '2021-04-05 04:30:10'),
(579, '5a6146d3e5d23e67d2f9ea3a09984ed362883a0b5c5dac768d9794bbc7c68aff0b2853a853d83006d20a599f94e887c521eb8ecfa9d15e434d9d6acbc923388d', 'admin', '2021-04-05 04:33:55'),
(580, 'f24319763ba2184db8179cd149b0bde2d07b9d9063add2244f04a61742ef4fb2f0b255d986e435e0a522ab1c41e0694f0a79e90b8244219d1a4fb6986e49f4af', 'admin', '2021-04-05 04:41:13'),
(581, '7b3db0f666b8fbabab6cee42678df0b9832b5d6e58fa5b90928d5e2f2cb7e0c60f5af9088439652eab4cec1c69863349006d88cd8c7834d92fb1ff8d483f8436', 'admin', '2021-04-05 04:43:05'),
(582, 'f047d5114961f2bd16c581652026dcc7f60cd7164b183a3837f0b3f3872a96082b8fd3790e54127607c082ebd7366f496323398698f06ebb1d29830a3535b31c', 'admin', '2021-04-05 04:43:54'),
(583, 'bae4ee46834df5e6994b99e5c09a659af297915cbd08da0283bb3aa1f331f747151997b7c02dc1fd4b740bf012d7d005ba92d0777bbf0f816d1c78b624532940', 'admin', '2021-04-05 04:44:43'),
(584, 'a6ffcdf1364fc7e67940436dbe209f1c97ec27f70262a9be04cea7ee279ed63f6cded45f1fcf6ae7263f097c010e8306eb70524398ea476fcf602ba1d95c4f4b', 'admin', '2021-04-05 04:45:46'),
(585, '766f3bced37f8f7c988714a7ad16089d06141afdc8679b271c2cadc6a843b8cd5f4612a38758deae493e8d7edf73b78ef90ee9f13bbad90611ee04aae0f95f38', 'admin', '2021-04-05 04:47:42'),
(586, 'cd81de4e986f53aa7b43aa1fab651a9859276796b6cf55f626c13328ae0f97f6d0c157a822a3d115c37a98d5e449a5d174c4a011cafd68a38c741362d01d3242', 'admin', '2021-04-05 04:49:17'),
(587, '45627263563dd56f9e1def3be64bcaf5d8e3c75d7dac3bf3e32918dab4c93c414fbbc6cc0e932ce6e465d452b8e0f9235a0bacf54c7648ea9b73409232db6b42', 'admin', '2021-04-05 04:57:11'),
(588, '9cb34e8cfb7a3e3c2228617213fc6217d3a64053de0f9207dbbae7bfcfa883108cabdb4d346d63df1f8816cc3d3fb007dae89394cf0f691708b84e9f8750a0fc', 'admin', '2021-04-05 04:58:56'),
(589, '2625160f476d8801500bb015cfe3918bd6800a6ac7ff4a1b45d586c69d8963f83b7ea9edcb7c25deb358fb8ef35585dd4a1ea7137adea4b92c9fee96488a2614', 'admin', '2021-04-05 05:01:31'),
(590, '056baf16c00a8403a97ee55966b7f232cfc5ca99dd50eaff1462f66afd2c2fb3ca933a707d12848689ce8b68d670f63c35a43ebb06f460687f02a1956247e2f7', 'admin', '2021-04-05 05:04:53'),
(591, '3da75e9ad42910dfade28dc72ed9afb622846cce3f0e7b7b8f0c51759086c5a47994ecfb990efbb8af14ee287c2f9d12706c10a1d410f9df37a51aeb1a009a37', 'admin', '2021-04-05 05:07:03');
INSERT INTO `refresh_tokens` (`id`, `refresh_token`, `username`, `valid`) VALUES
(592, '1f844bb0c0bdd5b22e29982faa2b8bd197c32c0aca3d6513fced7741208b25ae60e5f840d2a646f1e36a0bf2dc9fc14cd6a267c3869f07271a8178a816f23092', 'admin', '2021-04-05 05:08:05'),
(593, '4ca5d09f5b3462de0e8e7be79fbb5dc6ffa036f7e583cc7d9cfe08bbbac34620f40aaca9802ee1224178777f0ff7bd1763ae383eaecf99aafc39b82cd63f3567', 'admin', '2021-04-05 05:27:32'),
(594, 'dd785a659dd7d7e2bade101e3bb4584fceabb6b074742ae0b8bd4cde1168c10c471bdb6069d62bbf362fae504744556dfa14b5f12069188ff41d8ee214900c19', 'admin', '2021-04-05 05:28:53'),
(595, '548256551334f7f24d5ded0f91028a16df9201d88f56368ff82e72ef93c976249a63f2f73f53499de66254d73682f2ea183a79342cc70343352b6b0a9d9771dd', 'admin', '2021-04-05 05:30:24'),
(596, 'e8b04462b8cd5810b38ef7e56b29fbf3d87c880b77c0e5fa7dd82306f594e689c9c773e779a2311ac47867e6c9af1edc3104ec2480a358e8dfd2dd789ca0711a', 'admin', '2021-04-05 05:31:22'),
(597, '2309dd670c5f90a1b313332a25b23a4d518c0165e3aa704ff179198a21d68298daf04c8365439b11eab10603ae7f4b5eb608e56bfbbc80a4345b66d5a37d5eb3', 'admin', '2021-04-05 14:05:37'),
(598, '456a665cada6117d73e792d2d3692ad47c21fe1638444bc7202a18bf2ed00364c1e3fb534f673e0a55863b2e51557811aa9b132be83e426ba5f2698783bd3ce9', 'sudowoodo', '2021-04-05 14:05:46'),
(599, 'b0df89990d167c89ba948007bc80c306d23c054812758bc7ccd1d8ff791441e4dd1088317a2684d49ef5a68294094ea904cb07964c5be3b68929db18374f5e12', 'sudowoodo', '2021-04-05 14:31:52'),
(600, '941b6c31becfd5cd59a596ebfdf8d0b897ef81c0d3638a77a481fee2f54fb7caca1ecd6d48b18f849f9402997848aff7bf6d94dde2ae12c2d6da083ab1da71eb', 'sudowoodo', '2021-04-05 14:32:14'),
(601, 'cffb10139b71e9d542e8edd335df441f1271dd393436206c5fcc93b40c40e09b62de80762953ab1b276dad8f033b08bb22301c822e1f35cbf74834efc2557e2b', 'sudowoodo', '2021-04-05 14:32:34'),
(602, '9b039225d0307643b7a89a5a5a58f22b86bec7d3d042bc59f6c5d36e755ed7310e947262299c8d5780adcbc0f47e53b8ac9b58d08581b63dc9553291fba17d6b', 'sudowoodo', '2021-04-05 14:36:00'),
(603, '12c424a41107dcb57cba914ab7116102fea2a1d10b15b1faeef5e7acb86c30c2831f7f302ab25947857fca788a92e8e879275d2727625d3d3072a13f0b6e2bb4', 'sudowoodo', '2021-04-05 14:38:39'),
(604, '0929101a2294e4c0d4eca8e7f7c241fea84d95921220152a3cc4cb730887c6ad94c2008b0f9b3c7260db6fe1ee1352d01ae63e4149f5e4f7d9c87b2ac6007ce0', 'admin', '2021-04-05 14:42:11'),
(605, 'c2f43930b2beb50682c91f3ba2cd8a136de3045b808a11c80bf5b072f27234477910e897e7c8a631fa870b8ebb7449a51e013043ead6234bf90c2cc8b9f3248c', 'admin', '2021-04-05 14:48:18'),
(606, '42a7fdf418e51f289dfc6a8ec1e9d492b3a86f3a6fe29362dfb55d6fb31c5fde7b3ba0cc3d652c22ecddff1d5695f30ebd80c91d98e4eb86033f832fc9c7b420', 'admin', '2021-04-05 14:49:01'),
(607, '9f738a021fe1b6b5dc04b7549eea86a625e5582c0e2a7d93a63507bc5413cc7d5928679ffdb30562b3453ed3842274cb15f8a20adc3d76df2926b345d61c9ed9', 'admin', '2021-04-05 14:50:32'),
(608, '420040aa1401a40d7e7575437c1e073d54e4216758674c7a793506b603c00cbb0e5261c1821e7529055a96d4e703f78ada83066663ba54c7f610350795985049', 'admin', '2021-04-05 14:51:26'),
(609, 'cbc29d83f5f14a6d0da7423d968fb1607421b220985a06a72bc7c9fec1bf57895339bb95fed3d76520ba935e5f7266e2b85c5b0473ff9043aff9c06fd58acc23', 'admin', '2021-04-05 14:52:13'),
(610, 'a8cf94bfe1005a8d4e91576ab791e182a78d840df66c6930f18c3429eeb6179125f086f9835732e9e12ec23e505ea9b8c76a4dec8d6330292d7ed54d02f8ae17', 'admin', '2021-04-05 14:52:31'),
(611, 'c86e0b49ea45e8b7cff8f718843a0807a3c52eea365b375fc9c5278258bcc4917be00634ab69e2b2e4e14e9acae6fc9af30586bde5b13a350e3e8c2817b7edbd', 'admin', '2021-04-05 14:53:33'),
(612, 'c18146bdce38229508e04fb3bc099e3bf951a4c686a0f8850de5f057e25f9c39792b09ecf615ba12f4dd97c3652860a8058d17e3b4b8f5a380af07265700b557', 'admin', '2021-04-05 14:55:48'),
(613, '1e1a22a4d5996a83ae1be043c97cfb057fd64a895e6b4cdc3709b86865d19a6ccf51342cb49e41c56aa4b2f4b65c2222316734292054b18209403a1104cfdd34', 'admin', '2021-04-05 14:56:20'),
(614, 'c45962441f7a33c80c3f13d1807c6ee23a35271cbb61c94ec76425234081cce65059b60fb34bef23bb884b3b4fb1b89e5a9e223b8b64bf7317d86d7cc7abec75', 'admin', '2021-04-05 14:57:30'),
(615, 'faa5762945eba53d1b9e596971d158cf162b3c0eadb1982b3f98db78a75a9bfe5b586505fc55460c537140bca57cdcdfa306d17a0a538e11a72bff09c69ee059', 'admin', '2021-04-05 14:58:02'),
(616, '51e934f0c1ebf66200319952a44b4f117fe1506876255883700f65189f34a980a1e483bcae1fc19bac837af293f6fb206488d21947f1c8b4d4bbb62829f76ffe', 'admin', '2021-04-05 14:59:07'),
(617, 'b499494cf712f3055eef8fa0327d590ddac266fcf826e53302bcf6257db7f633337f989b4d8b86cb1cb51af43baf46326b725fd5767e2bee958bb15e216c4e8b', 'admin', '2021-04-05 14:59:50'),
(618, '222cc2d4868bf2d34f2e71977d74d39e07f79956ea6c1b005e24ab829ecfe4b9f31f2e82b00c2719b2ab70209f93f03ea1ea7cf6ee9681b1f431e150e4b2516e', 'admin', '2021-04-05 15:00:11'),
(619, 'e722e703b89ce3202c11917047d9a0c6196356b56369664bdf13bafae3218e9dcff82dc6fe8e1d79426b4bb8f79d2ea99535b12942b465b89b5b0811d7f8c2e5', 'admin', '2021-04-05 15:00:32'),
(620, 'ebab85c7b737195a95bf23e86fe1436364a0521950d9aa9e7828a6fc4df8add2712168d25b29880e5a6374694f628c236eaac50a7af02508cc4729e2a7e95f7f', 'admin', '2021-04-05 15:02:53'),
(621, 'c77bc729c95d112f6681a4cac9d7f95c2a44abe40cee494396d1e83aaae6bb42ea4e6af0ad8e2be8d87cc1b4cf997fa48df9b568b12f4799c0ebda9ee48636e0', 'admin', '2021-04-05 15:03:43'),
(622, '1956a65fa7d86f298a26d8eaa122104fbdc92b5d76cfab429fa92bef3f72adef6a372a72c7c2ab9190165e5f6c6e3c96bceca5054f251954a2f8378b6e41657a', 'admin', '2021-04-05 15:04:52'),
(623, '5ef5149046760324cf5d2a3a4d17cd8e7ff8869842b8bc9f907628bef2d25c0159fea68d67b96b3a077d94ffcae65d3745e51d410b0245dcd0d3896090d25d95', 'admin', '2021-04-05 15:05:28'),
(624, '4649fe1cd5b30b40e7ec06ba3d6fe61eaa23a24e6d8ad719465a686279243e00d9fd4cdd20aad6cc75a6bb408da895722277919574b445582f931992699ff13d', 'admin', '2021-04-05 15:06:00'),
(625, 'b5f917c4e4dbaa39343b0fe1526cc9344ca54414fa4c2d86843a724fccbf8c8d5152ddea4b95908df1f1a9b4ee153cfd6f221d0b6626e9071b899ec2113a2cab', 'admin', '2021-04-05 15:06:13'),
(626, '662c00ae87f419c71e5163e82ee51b64b9e63329394d0326e138fb0d4afa5d737b7eca68332d64a70a76f9d4d9931cbb0b6070843cda994f2c2bfade0d88b33a', 'admin', '2021-04-05 15:06:59'),
(627, '08a324a02fe6836b86c75cf17683b0743742057860b42d730990defece7ace44c86bd706adf8d53e1300ecbf13a075a378d40dec1fa157068986e6cdd7847f71', 'admin', '2021-04-05 15:07:53'),
(628, '9b2ded3ac2129be19040e5bf0f8fcc6c95ea4c04e061cdeeae32f48ea2cccf58e2cfb624a45808c5fe23d0b4b97c832b62f4e1bb2386668663954c3ae25aad4b', 'admin', '2021-04-05 15:08:17'),
(629, '4b54af9b48493b6575e9ac830f2bd46533aad7c5d9ec534f6fd9203bc1ad79ce8acf6b6ff2340510bc8c93c32f6db85ad8b6b2da94e0015e3fdb586a00f07fc5', 'admin', '2021-04-05 15:08:26'),
(630, 'b05c7cd15018153686b92372365b33a8aeeff4f45ddef435843a2915415ae677d2a62e26620a4dbc3827a54e877b8284f0324885326f836fe22df22ccadb3977', 'admin', '2021-04-05 15:08:53'),
(631, '6e80a7642a0fa19d231721d1e0d4c4b556207cda71d82a1c8560b2697691050bfd909f43bcd788406b2b451ffc3ce47ef422bd99ac257f0a83d8798a5a5b6806', 'admin', '2021-04-05 15:09:11'),
(632, 'eac7d0ebde56985e407f37657a0fead1a442fa8ad91b56bc3f334fb8b56e73aa64c8a4a6036a806b4a7feecb8782353abfdb285f7a500da7d909275dd570c03a', 'admin', '2021-04-05 21:57:57'),
(633, '87825cfc74f135aa1354b490b513bb0c6cca2dab4fde91c0589e623af75a86f331e176e53dad7fcdadfe3e52a8db6dc824c91d242413526c781da578c69728b3', 'admin', '2021-04-06 02:18:17'),
(634, '41629ea993d03a419fabdce7de90f81310d20b03e47ce9d24ae2e9f3f145fb354661f79cdecacbde1e4819f36c367f527573a78a1303ecda2b5d3ee5b38a1199', 'sdfhnn', '2021-04-06 02:23:51'),
(635, '3cfdd1f2fa6dd275686ff8f16aaec825233e21c3de3a78fca4855ba88f40b9b85d388729a699697a4f19e0f0b282fea72d37be869c9a0c8771cdb26fb6b5eaae', 'admin', '2021-04-06 23:11:13'),
(636, '40a727c5f64a6b7f75dde27e0335725641ed5e9fb4e03dc3dba81982a505997112d937fd3cc19ad9293ecde6adca214f1555fba8e3d42188eb74f925f5613671', 'admin', '2021-04-06 23:48:51'),
(637, '0552453fb0038500a461bbef415c40614e847785bfc64d4b5f1d00b0f37fd7c4a850178eee456b33cd0db6eb174eee7ea0078e4cff417cea62514d5e6c7c2477', 'admin', '2021-04-07 00:17:24'),
(638, 'f1c3ed177b10bc6b2c2c7cd736cecb95341dbcf4fd61b26a7331354fd8db9e0512bb86da4ff4132c115bd49c0998c0ec11e9c4b52bbef7a8ab9022c9f256ec29', 'admin', '2021-04-07 00:26:13'),
(639, '74e8c75fb852142fa3dc3aa2654bd8ed1bc0b078cebb361ca0ea411503da3f87e04b8886bcc0b911cd9d057332fbcc914634449b3c004de9940fc9efd2aac746', 'admin', '2021-04-09 13:05:21'),
(640, '0faecca2b68b75583fcb88236ff65c24d4ff28ec768e0565455a1d5b8cc0d48f07a65229b4f0bfd6a08d78e434b18e50ed65b6e36b40e4e8219bc93121a9380c', 'admin', '2021-04-09 16:40:20'),
(641, '1c02b076dde2ecfe17ed616be0fd8c5110da8c199eaf90aeec68e8e250a23400e133414c24121a5718098e509a052d62db2e2dfaa166daf181cca6243df03db0', 'admin', '2021-04-09 19:05:22'),
(642, 'de480d19ef9946d656b9a32fa097f33c0984a2bc4063d77e9d031b9f10e0bdddfac497d7866ec6c63bf9a0b8fac52bc0e3cac78b845a96612d8684c22a94728b', 'admin', '2021-04-09 20:00:10'),
(643, '2f260beec48bcf84e32edf583f66a5cb16c4a81f4009b49c268c2409b800d66a40f8b3fc731f50a8295e1662620ecc943fa8a057085fb55510a592ecf3603b28', 'admin', '2021-04-09 21:02:44'),
(644, '626dc38c945e2b088a94e13c84ab6ab04071def73364768665866f1891ccce1ce0023b5b0fd51c9518a346cdccfbc7b7712bd5dce303f1523bd34800ed88060a', 'admin', '2021-04-09 21:03:08'),
(645, 'd87e8be58cd0074d9ed856383a83ef9b87473562a084143e41c95e4eaa17852d0d8c5a053559bd336c6a0aabec7d7289cb737e57f780670dc2642652c73f3d37', 'admin', '2021-04-09 21:03:22'),
(646, '311c15e7fa6b0f0a6ab34b1b081b0ab4ba9574f8b870d81a67c8591bc9c3c7a993e315bd92f7a2b13ff503486d7dd128eff45b9896f7df92b638f289ec5b2287', 'admin', '2021-04-09 21:03:40'),
(647, '9d4a509fbc8324fb66316f889e3bafb59ad55a0d01e2d2155ec44839d450673017b2392d18c326baaf0b10f28a3bde2f4b10d06573d5b88c9fea778c1cd031fd', 'admin', '2021-04-09 21:04:06'),
(648, '37b2bdcac196fb5ab781e9430a9185398de3c40176267ac07be9ace3ee516fa5dc43682b103f93a301f980096f47ec6ccbf6d659363422457f4ccf0aa153ca99', 'admin', '2021-04-09 21:04:26'),
(649, '9ebc3d915f6383e24e10873b1ae0dfccd1323ad61150d106bfc87a4e95fe9371e53491af74ac3e10be9405e9eafc0516c2c2166eff3602978a66fd61a9288a6d', 'admin', '2021-04-09 21:04:40'),
(650, '490f8716cff39170fe98aae08774d32023ef7cfdf0e8bf08983e101f9d5bf61474aeeeb9546a9c80629aae7f6ab7ec39fb12fd16df056b2516a15b92412599aa', 'admin', '2021-04-09 21:06:15'),
(651, '5f59bf4b3334fa8e924dc6400f8b6c661609cc38ac84e03b0b87e6447c3c9a3e148a9f59228c5b335944096881116aafcdb45ce7d635eb3a53c26fa51c942f2a', 'admin', '2021-04-09 21:12:34'),
(652, 'de34e24e13eb84087ed50a5ff0d6f126086debaf7ac4e19c8ee6fb027954f53c2eee3721118c01a8931c02dd9388b2c0bfec417a817ff6dbf6f969a76aed43de', 'admin', '2021-04-09 21:38:37'),
(653, 'adb5b59bb8520a5c12475742d47595edf7fad80b7e0165f429adb5bcce9d3915928a835d8427b57186b65f11fd3ed99a9a782704ec6db831aa8f8f26da965656', 'admin', '2021-04-09 21:39:02'),
(654, 'c7481e90c1e395f45d17865db296ffa79460aa39de65f01bffc445b8906aa38fc3fdae7be786ea7d58154d4be14445a1eb70a00e143300f81ff3d39ac83c4f24', 'admin', '2021-04-09 21:39:37'),
(655, '1858d41c46656fad445aa3c207e56e378d7c3856adff322b5575ce2e54866a86243e919a8ab306574d73add005f74958da1f4793f2a2d9e06bb81c4f6277552e', 'admin', '2021-04-09 21:41:03'),
(656, 'e07388a7ad48476cac27f56c8e4992134599f6675b5c4e47ea5f39474aebd5f13873b612a97eb6983e3b7ce4a8508f16fdefa1dbab8cd9d3aa35593654cf6790', 'admin', '2021-04-09 21:42:30'),
(657, '15f09836bee81125fbb48d6761e1b6e962290d0f4b4c89f02b59d117076f4bad1f857888d0fa532433f1e77446767aae7867d7f498f2f1e08f5ed4ea71c151e8', 'admin', '2021-04-09 21:43:06'),
(658, 'b2ba9f62e7bfe68dad73c056f11bc910c1f044a95104fb88cc84ac8b028ca9cd6d1bb8f468d1cf268b5e15cc6af6f49315a5d374b9c6a303da2ebf1a76fadf60', 'admin', '2021-04-09 21:45:10'),
(659, '01fee754a7317fa73112697cf2f8195d80323479cc5261c61cec9c096686b1e8a433e2709b3c230d8173b5d37f2088fcecb0817fed2f77a16bd735751e710765', 'admin', '2021-04-09 21:45:28'),
(660, 'd17a4663994b7862090c809c68c566d4978eb73e340da9cdc24990f6c3382a9046b3ad3e26b0e546838b1103d4869ee21ed900bb3ac64f2992e01485d218b892', 'admin', '2021-04-09 21:46:06'),
(661, 'd1ade50be2f8d2dcef1a8574baa0efb51d87e7a72bf10bf76bb80aab64705e9952e9a3a35da064d749ad72d0164bd76699bcbc59ebd7b8e013f95885c67973f9', 'admin', '2021-04-09 21:46:20'),
(662, 'ac18b087efc59c5ce0bd02e15f3bf5d47c73501efcc71459f7a36fdfd54ab03aec3f1afb353bfbb8ac7aed4715617f0fe6a5b21c4563b0b2804455ba281c1257', 'admin', '2021-04-09 21:46:31'),
(663, 'a166573be642a8d7b3e3234981103f370feafede2f5fd8cf1714daa27bc032b2ecc166ede03c3bd6cc93fc30e200714ce002f68559737ff343f4f862505106e4', 'admin', '2021-04-09 21:46:43'),
(664, 'f66c9f73fdeb9080397c05342a3b42b854111eecbfbf2142fbc734d578d199ccca91ad7acfeb3f9e938b531a64211b91d7b1d94135b66fb166d61fe4f4d0c44a', 'admin', '2021-04-09 21:50:18'),
(665, 'ff14624602a45cc13a4766cc3c75691b69f91566680e852b7c813be23be17424f11f5387521a351b5dfa403407fd3e0b9f43c013d7326cd839d2b40d6697248e', 'admin', '2021-04-09 21:50:23'),
(666, 'bbf04d44f2d7847d8c8c9a55b683445a8ac81c0995d245b750495240744bba51f2e719ecc9591c44f3d12b611460afbd73ebc5ab240331f5ad84fd976f5d2c96', 'admin', '2021-04-09 21:52:40'),
(667, '43bf54d898b02c1a153f3698a08ee3df73fd65b42543f0c70d597772239b72c4e05ce5e6da47e53c10f301aa99370cfaf3f2037e1fe66c0ea88c0dcb20cacae9', 'admin', '2021-04-09 21:56:37'),
(668, '2169343881685871ad2cbb396c991f0e3e4d80b83bceb93a5ceb23f9313f1cece433b016162019041f6db60da5a8961780c0b57c08ab3d4fe2675537f8eacb2e', 'admin', '2021-04-09 21:57:00'),
(669, '91a86d8914243f9fe3786828081195ee13121d93579373d7f0120df6c2647579a2f59ad88bbaea8357d6885fb99bb0cd4d1ba6d3bb770439762bdbefb13f8b14', 'admin', '2021-04-09 21:57:25'),
(670, 'dd7956038e6d0ef7c112c20ae945023e4919baef523d28119b6fa8caa4bb241022ca7b808ca671586ef99966a1f16436b502e7040aeb4d2b583f9b41158c9959', 'admin', '2021-04-09 21:58:33'),
(671, 'bedc821ed9fb770795a2747e05b7c3bbddaf864ae5558cbca75792edab89568aa1c4fa3d801442d8300a37d48c5789d28a7d54b1962827411c88e1a04c7f43ea', 'admin', '2021-04-09 21:59:13'),
(672, '597c335c8b46b47d74b841dfbfd643377781045def8527eca91285922185d74aa62cd7ed94b9e8f3ad22a0c99d578eaa34953d3e6185c4bc55e16d6b1ec2a0d8', 'admin', '2021-04-09 22:00:15'),
(673, '1e437f656b94e0a3ff9c6e500408eb87c668888c5ae2267b7dbe8c6ed8ef1b2a50f2d968c4163e3f73d7b017eef123ebbfbce677c3b20fb23d733594279dddd0', 'admin', '2021-04-09 22:00:40'),
(674, 'dbd3316f23ad8cbec1c6fe029e9429ef4ea1d73bb9a83c75144102a0825170b2afd7887c3254d6ddc51613db7f1248b5726b1a41f483dc7486394262847b4cad', 'admin', '2021-04-09 22:01:02'),
(675, 'b688a19aaa32e9b31e995e367c7951c525db5b0c369d53dadcc59826d8a9831f999831525f3bbb1cfcd88c2ac7db7c0c8c978ff1bca65a16d9f2f1e9ad4097d0', 'admin', '2021-04-09 22:01:58'),
(676, '0e5d382437e572dc688f0f5b83178219b5946f4eb928b9016ea0a3681f1deb64a5ee369d2705e215e93eb17be8c67655fb02068d461a03f73e13ca2543966364', 'admin', '2021-04-09 22:03:53'),
(677, '41b03810d89a8f8fff4e2e34c446457cf2ac0bd0c39bce784f9742b4971a6339acd1c9c473e4a15bf01a5996ee12c82ba573320429ea5e95e352ac314460fd5c', 'admin', '2021-04-09 22:04:31'),
(678, '902b2ff43629e5bcce5eff87d0b36183ee1797f68502cf9093e386d7bb33fe9a22faf73288f60163907634b5dc1c8b7ff32a2a8d6953ff7bef25fba56513169b', 'admin', '2021-04-09 22:09:13'),
(679, 'f1fbf48a0e889cf00a46c36fd1ab17f18e6040e299db022070e0d3fe212879619cae15a04519b18f79d5bb23661707ab2c02e06028b9deb7305ff2462ae658e9', 'admin', '2021-04-09 22:09:55'),
(680, 'f92d552b08418d1d21545eb35fe428d89320f68302ef734366ee8bad87349ac9c6a008cb94b9516da56d1405143929f26956594add0df8d97258878e6535946d', 'admin', '2021-04-09 22:11:43'),
(681, 'd96f0cd52a5c9ee9f62f33e87953c123d73b219cc054e65cf3dc19a0f51813dc39d0e976200bcada000166d02bd5c6cb2bfe10dfdd81cd7c4a93e9967bc487e4', 'admin', '2021-04-09 22:12:30'),
(682, '286986975791b97b88bce18adc73b0b8f7fa113b3a3136d0844c2d7dca6510785647fe6bf75f87d0572d990361d71ba784a03cf62291cbc49e95efe153f7de59', 'admin', '2021-04-09 22:13:01'),
(683, 'dac769a518d52b5854c24e71d8890a11d62e98e2a43567ff166dcbc423cd69b9a08e8861c12e23f203497dd802fb706e713382e00fbdbd74d14808a6419f39dc', 'admin', '2021-04-09 22:15:09'),
(684, 'dcbd63b74abb488352f97e10a7a1051ebe29279d9b270eb7e7eb502b08a9113ea6f889f646dace595889c0c460a688305980c86efff19b5537481bf8553fdf90', 'admin', '2021-04-09 22:15:33'),
(685, 'e8be69ee6d079c15242a63a4bc08e09bfe1cf936fdf61d1649a286b24ff2b1a70bdd0f4a4f1030131b6e8f2710c3c482540957e73f53b882882555a86425596f', 'admin', '2021-04-09 22:15:48'),
(686, 'f71a45ece39bbff12d36158a371afae7a6dc621af8f76b6568452ef40cf0d34a4d256a93ff1c4c31bdf5c9f7ba5a10795038ee20572a1cf8cf1717e9ac0ef111', 'admin', '2021-04-09 22:16:17'),
(687, '44790053f55219289d6037e67b620ca1c59304a9d1d0e68a69cc271e98bdbdbe2a1bd01728cdbfbd39723a78452613d6efb3a172f621bb4eff152fb7b337bae2', 'admin', '2021-04-09 22:16:34'),
(688, 'e7b303ef495ed277da9ebb029175cd63c3b4ba29892e5fb1099bd0f53d0f4f73a997d77db2383cadf34e6d65298587d30719df9d73a9ba04232133d6b2563eae', 'admin', '2021-04-09 22:32:43'),
(689, '0d0e169335aef9a0296bf708f9ffce830d2d32324e439a006cd890560eb8a73dbab0362c01590b8137cc3f869521cf5de8687c428e42b0bf5e6d2aef09ba0c8e', 'admin', '2021-04-09 22:35:17'),
(690, 'bb88d47c30fcad72fbb86b50d668c06d1996ed4d065fa02c3f9840667b8e14e732e7d29799ab6d2ae14b2ce89ed00656030cfd0a087c7524a93c5ba0b9b94b87', 'admin', '2021-04-09 22:38:36'),
(691, 'ebc7ba440386fa60716aaeef95a54f3181d87a2c8008b316c6eddc730b196822b6595515582d81f519a97bc409d781e06f42b97a18d1a8cc1be618523d02ae85', 'admin', '2021-04-09 22:39:35'),
(692, '3ed4a23f27e5020611166a4519b6d76c5ff350da6be31e24241227cba990a53aa77eb8c1dc791f6083cb2ac16246420feeb69ac33190dea471d6543900c035d9', 'admin', '2021-04-09 22:39:42'),
(693, 'b14e3b002d5e2ca4f700c5f30483572a0297a39957c8beb724f11d7c9042d066f1a4547f450a68209bcd0308324ea1326a629db3dba0c903b1e3aed6be245b29', 'admin', '2021-04-09 22:41:26'),
(694, '8c1b390965581f3acb75428dc28dd2178d58a38195bbf0172f0f0d0fb5839c0e6a6d5c8b46f184bad890df935aeacfc9ab7935a102626193031d15531eef46f5', 'admin', '2021-04-09 22:41:59'),
(695, 'e2f9be4f55cadb80b0eb353b6daa3737b5cab00ff3972ed57c50761e60f709bfe40a236bfde9bbe912a1ad98451fbb7a590b91a0ee0ed0311fa608dbe40684ff', 'admin', '2021-04-09 22:42:19'),
(696, '25231dfa0903164d0f6a369f2b51437b309966264f0b434e1599c6057fc575ab2168c92b4c9171949c9e77783a7bac9557440b04136f9f1c6ba0ee048fe4bdab', 'admin', '2021-04-09 22:42:50'),
(697, '9d6963a4d20033be0a047bfbd158e396818740c78f28f7be16fdbeaa649e2a83c8400c96bd9fec4c212005649436f4f2cb82c8ed0212d2e7124676560c8ea074', 'admin', '2021-04-09 22:43:29'),
(698, '94c17910eee01e21613a6fa011589ca35d5443cac15c4946b3f519cc67030bfe9b7f332d0519ccbf7b19ad549f6e47f8e8b560628215087dec3ae8174899138e', 'admin', '2021-04-09 22:43:47'),
(699, 'c7a115358228c7568b13469a624a63059b2839372983900443f150e77aa910098f15f53035bef5395543a070f9b1412c7bfefcb83a377b3555b7075a25aded98', 'admin', '2021-04-09 22:44:46'),
(700, '5a90034a9239913afd4a9f7271623e7e98f45d58fa2083d1aec6e1dee0a314698f4205772900789f75768d7f959ff5bad774b82542ba5fb2c2c056864d6fc2b4', 'admin', '2021-04-09 22:45:03'),
(701, 'ee258d2b98023a0a5d6c969becceeb764edb7073a146d4976eec39c0e9732c2c453b14304b1935d5fe52176fb8b3ba5ac62bcd4195536e90dabcde8207187086', 'admin', '2021-04-09 22:45:33'),
(702, 'f45f3e0aedf01e6a470fec72ae02369f15ca15cc7f24f9e32986f6f764bd387bc971bdce3066fc993311022bc792981e6c7df8f935784afb90e46be9ba2a21df', 'admin', '2021-04-09 22:45:50'),
(703, 'd91c23a1e4e4afb1c5a4aecf7fa78dc492e9d30f6efb8c00c6829cec45cbc373e59a819623be8801ba834b71f5ec192aae2de07662aa5c195d49b14f6713314a', 'admin', '2021-04-09 22:46:50'),
(704, '6d81d875df6e3984802e3ed7dafc3cb6418b619057a516e9980569a1d9126d0309e7103faa3ab2604c883b945ca117ab993aebf087b74b5e0556c627bf44003a', 'admin', '2021-04-09 22:47:06'),
(705, 'c1cb9b29c75d339908307044ec23e67433e712a7947bae7906b0082d4aa65ef8e3671872b0c25499caaea8ba972fd3766269904e62de74f9c5b60a2cb3f7761d', 'admin', '2021-04-09 22:47:25'),
(706, '0a007fb47fd9ccf36cdb3fc962963a396e3703c101238087e2a2b420cd68a0b41c919ed1c7e44cc76f00df99ef1a442e5839e194913c197db22e584c04bea410', 'admin', '2021-04-09 22:48:22'),
(707, '15c4cd4b2e4f730074000f634e54ee12dbe8867cade0a3f8389de06614efe2d2d910dbb605d14e0063251f59e786590eaa40255077e54ff0481d4e5459e834be', 'admin', '2021-04-09 22:48:23'),
(708, '26bee8d2075f6e06999233ede1dc6c773e49e2174f32a3ecac8f78a0625a78cf55b5e7aab2021d69d083f1ddaf61a864018a460444e8bd42cd888743ea08a348', 'admin', '2021-04-09 22:48:38'),
(709, 'dd47139765af104f35f16a07751a58cc634a3768f5e78efb6e56276a1f8e4ef2b7adae4cfdd32282e2b684b2026eabf77d0c8de536cd3c14276484215856fe4b', 'admin', '2021-04-09 22:48:39'),
(710, 'c6f208ee5d812fc9132c3d99760a7dd66dc336fdf5f51024fd4b0043cd54fe38216c990f51c4c569b81679e5d34c86db3e972d6a13fd680fce24903a20066ffa', 'admin', '2021-04-09 22:49:15'),
(711, '19a3fcf7c03c977f823587e6dc2b5710586de6f7810ceaa1e0cce14fb4c65d5a3349a0fedd95fc254572e1bc150bfa3c1ea7c579c10e5fed5fc0f9aa2a5408ea', 'admin', '2021-04-09 22:49:16'),
(712, 'df16745695604bf238a52de67b10bf68a6ee3a4f2d030f2fab24620237f3c147db3376774ef2bff12b94122925bfd8272bf07a88d45654bc77b34adda02e069b', 'admin', '2021-04-09 22:49:41'),
(713, '11e806f331288da5541eec08f18f948d0cb4d57b59437e9ab94377f37ddc362e49d9bfa9d0b056245b0af66efd383909c326fb4e426f0665ea97da3587702bcf', 'admin', '2021-04-09 22:49:42'),
(714, '5694f730e10af637469199e694d80a3559b6b73857c9264312aaf4d606b1fdb716ee313f7b406e6b94b0c84e1db5253bf633ef6d16cdabc96ce1a4a3c671620c', 'admin', '2021-04-09 22:50:04'),
(715, 'd5be1068e23a29272d64def562a6d06664d03ac3b40df4eb952430b96f3febc9f0dc47e5f98602f95ba4370302a0a5f79ba48318ed9c60e01bccb12dc0989bb3', 'admin', '2021-04-09 22:50:05'),
(716, '66ba202726a3abcc47eb830ee6aef8f32d1038b92721f6943226c52384e31d3fcbe192bc63f4bc793f578a3c5fe22808716b00fa354eae4137a8d4e3318cedd0', 'admin', '2021-04-09 22:50:22'),
(717, '066425eeec6428fc7d53a8127eab447a85edfa52bee025e5e66c175bdd9626027f42ea6f4b75d4abae539abd0998ef358da79eb6347ae685aea8f787e0464764', 'admin', '2021-04-09 22:51:05'),
(718, '0733eb25b62f4e6aa80b7db9f68aa035bf5af889e24532b44e0b478376528233365efe4d286304467e087c1fbe46e25a9eca30bede3cd5d673f1803dd3149b91', 'admin', '2021-04-09 22:51:27'),
(719, '4712e37e1413d9fc8336d8887745467492e51688ec83321a985124657b03ded53646ccac541f0a46ca9705064eaa6211ec0eeb9c14eb93134c078de898e3ce42', 'admin', '2021-04-09 22:51:43'),
(720, 'efe011d076b7cde9e53752c988926f171184cfc3fd138132ee43d278d71a74f9a4c31c83b1fcae15aa1229e54fdd0d0a8c2ce412de1fc49f12acbf49b19bda23', 'admin', '2021-04-09 22:52:47'),
(721, '40b19f9de0332ab2e7017e43f7e0caeb6168c860644924658e904f05970602a89b7674421d8f71a1f093bad4b67f75ad380b82a7875e6b3d471ecc9359fe2a3d', 'admin', '2021-04-09 22:53:24'),
(722, '68f3556938acec21a29f1efdb14b19a96bcccd9b7df5e9d95d7003ca8d08d658d4a7f371a8a003625209450e389cc6531ad019f1bc8476e8182ee4a3cbd042b3', 'admin', '2021-04-09 22:54:10'),
(723, '36a817ec710f7401775f52c24cd076278925f585dd14ef24c4ffcade499935cb25ab4143c14345437c8488bb5e5b50c72bb129650836fea472982c87ef6b2bfa', 'admin', '2021-04-09 22:54:31'),
(724, '49b326d139f45a19d342b652c23061ee71b362f4c6d71cd437f64f4f183f7153ea349cd763d66fe931592dc0593329fcc7b5eb01c23e94d6dbae3394b45ae94c', 'admin', '2021-04-09 22:55:25'),
(725, '3320e332e27e1564a78f405eab12caf8eb8401fe9ca5ecb97fc33cc784599c84618992e65d15e5ad08376cda8a3bd0955c7387f0b8cfda86cf7633ed3bc17392', 'admin', '2021-04-09 22:58:11'),
(726, 'acf06c53481c170767a685638e74d3788bf13111c5ce10c820e6cd8718be737933e313b0add429066f2e83019175c68ca2eeeb2bb91383933749e24ebd86b945', 'admin', '2021-04-09 22:59:18'),
(727, '7e5ec3fe2b16ef3138e88bc0bef59c59cad2d3032c36a3e51ec83589f68053d05387200f1461640b2cfcf34dd78008475a620ee5134726144732984e56b26aa9', 'admin', '2021-04-09 22:59:19'),
(728, 'a4b512397fe1bfa507282759f2b1a46190180d7a29ed2b9f3d3a33539eacdd06a4046c121191d42ce71399e64af41a762cf7100f81b399f281ef065f4af94ee0', 'admin', '2021-04-09 22:59:33'),
(729, '59cac039123d0f2243e8aa7aa14090af3173267567eb19e5710a7046266fd022e61165001a6e958c3a0b32023480915332a80f385ada098d130e8775759995b5', 'admin', '2021-04-09 22:59:35'),
(730, 'f86967be0a1a7c937ccb7937043f46f0b3c3c275b3d5a11df6d7ce705256c597b9263aca144b2db5e9fabe5feb021952c73cafcb5d1c80ab661d8264f11766ab', 'admin', '2021-04-09 23:00:13'),
(731, '526f8341da5e2e8568e6d0d370cb047094e74b0a5fb31142334aec71a48cd1c02b16247e8976fcecb9d61fda9c20565cca9fe844d2655b1a621058aef75c84bb', 'admin', '2021-04-09 23:00:14'),
(732, 'd581048880f0980e1d7d90fb6db4b834013536f01602cb44dd049780e304ef081b0d7e9cd0d58671de75cf448261b3dd8313817b2c6aecaf89ea29562473cf8e', 'admin', '2021-04-09 23:00:36'),
(733, '9e52061fef50ff8bd9a36b6a056158315bebb9021a20dd5a512879bf1410a9399919985634576b323117f87c13fffbead1c173aed5ad588b81b40c2d1a5f9b66', 'admin', '2021-04-09 23:00:53'),
(734, 'a70b15b7690785d72ed2678fa131bb1f826345eaf1e33adeda4476b2a960a260ab993b3834cf3522cc3c0eba2b723687434e03f0f7683103a8812189b943d756', 'admin', '2021-04-09 23:00:54'),
(735, 'd45f5caf9a8c5c80ee677f6dc2c3161b5c735f8b14584f84170c14d84d84b318f806e29f84784b2c8ab38467b35bb9f15d1199e4221fe3a6cbeecc8e84007564', 'admin', '2021-04-09 23:01:12'),
(736, '62cd382178c6aa95c34678384b609a426ca4897b8c48ac8270d2af792f4babf940fd19b898f6cefbe49ac8955a9c09c374d2c1f6f74125a553fa5a6de50af45d', 'admin', '2021-04-09 23:01:26'),
(737, '6694439e94db365262aec685c8b3e283d9b8575541d71ee73e9c30e6455dfc69c855e805912c8089e970e03c5346bd43308bac1d43549b8e409e70a039a528ee', 'admin', '2021-04-09 23:01:27'),
(738, '896ea856b41731bebf307072ee8dde5f84a984aa666ad85d7a56c5204bedddc984ae4ddfa410e0fc86cd69c7e181150734aaa883a07a8d90c1f0acdbfda3bc20', 'admin', '2021-04-09 23:05:46'),
(739, '3c00075af9f3493b417e2e1993f178e0a2bcb93aa00b2ee6d877166ae821297eace382fa0a095e2da1ac545887fab8f4ab424b8288d750fd775bb2bb650a50ad', 'admin', '2021-04-09 23:05:47'),
(740, '6c2453b83ddd642a70e59379d0c31a9a59367e752568812a59c88a90bf9815c496c2c8195c74c8095bfd12e7c69f40facb54490b9c7e733ab7781feae781d235', 'admin', '2021-04-09 23:05:49'),
(741, '1e8eea51129d9a9cbb520eed5ea427c9852cf71c4c9e923f725ab6c86461cada41165237955ebb9ba5a80ebf0e5af2e6734446c0a4962cc067774da9e60590e7', 'admin', '2021-04-09 23:06:24'),
(742, 'd0a7b83495ae01a3463da0f9ebb0b319930945388c9b8ba5fb71982f21621c3278d5a27831c90a26d669494ef4f5952ff078e6db82fe48872a1c1ee4fc2b119a', 'admin', '2021-04-09 23:06:25'),
(743, 'abf07051dc34e0b91fce3788da3ca655790e2333dff289df50cd12ad384bdc45aa8559093ff93edfd92a24d7b323045210478ce8ba493cbbe3d3f902ef64a124', 'admin', '2021-04-09 23:06:28'),
(744, '298d301ed206dc64eee07f61ca194058106bed80793a5e5445555f5d20bd88bc6d04180ac120eef88e5b6078b647c264cd20817dd79ed69788f6b3637ef997bc', 'admin', '2021-04-09 23:06:35'),
(745, '6fb1eac617046f7f82b540a83aed1cce4a1ed232193f365c18aa2701a698e7dec8b33c015e1709e012a0e7ecb03aff6c8f615c13aea38b943cffc996587cbffa', 'admin', '2021-04-09 23:06:36'),
(746, '95ee0e2ad3cf6883da6843856521af606fe9fc881fcd44f02e8094ddfe57b4b2fbe7fbbaeb451bb821575568afae1ac27359e9d93a605016e33a7f3dc1fd6d94', 'admin', '2021-04-09 23:06:39'),
(747, '5ec48ab1ac87e8c5eaa6d5a5e2852ea33222f41588356c43207114271929cc4f4da2ca62c64ff9dd483ba38403dc5ab925026eb174baba2c234958cff3f55abc', 'admin', '2021-04-09 23:09:26'),
(748, 'e177d58225fae8b0357804defb88111711bee3d506636c464950004074874927c6aed9c45c4538bd60784a8762a604cec4572375e8f8f6f2928b5d2a568da798', 'admin', '2021-04-09 23:09:27'),
(749, 'd992d671ab09dd88b394f44b52a5603f5f29a457d7e761a98ceb0cde8505cc6a5cb2a4d05d950f641be7410db3049421418b477184f7c65ac35aa43a7a5235fa', 'admin', '2021-04-09 23:09:29'),
(750, 'bfc3d15e0b40a92e4687fb667ba3527a5dfc0f3900b0f25286a1f33f2b822be1290ef757e5e767cff8b587373c0b15de7916c4bd8ecdb0c47cd4289e633d02f4', 'admin', '2021-04-09 23:10:32'),
(751, 'de105f4c00b287168081e08f524b95b52abfb8d7b42ed58695a4dfc4deee4b9cca3511a5698602448be0e49a85ce4046add4b8143b55b65c97551ce334b0d6e4', 'admin', '2021-04-09 23:10:33'),
(752, '35a98b717fffe524b00f41ec26625f60642aca5bcb0bca3795fb4bded156ba298a6fb13a3c9c27635672b3191ff9c605acb9d389691d4ac934d9a59277c18967', 'admin', '2021-04-09 23:10:35'),
(753, 'e634275e0e85bea4bf175491aba595eae8b8372156f91f11da5f5762bc448caab0ac7c81e72bf24fba012bcdc5b839d12adb8dad326f6694e9be948660e89045', 'admin', '2021-04-09 23:11:29'),
(754, '39b9dad9cf44d963218bb65e92a0059804b97e9aabf8d146445722df49ae7ebd4fa8302a1018bde99db1f6526486a5baf3bfe5f448c85887e98e21722f724a3b', 'admin', '2021-04-09 23:11:30'),
(755, '3a90644520006569f2d14c9c6bebf25d3401a8b7fc7e7acbe33e76f3188a87bb29fac812c8ad897143fc57caadec34da7b3a6157cefbf1a38dcf04d8b2925012', 'admin', '2021-04-09 23:11:31'),
(756, '33a189f714310be039749c28b297caf862e6fd93f2077cc59c9d6641d7790edc0bdde11ab2169db2340798306ac9745518f5cdad2cc6b1fd76f67baca7a63f36', 'admin', '2021-04-09 23:12:28'),
(757, '20151ba2d856eeb45ba91752d1d2fda777fba6ed41128fcad3f0c07a520a256577477bd89b2808b4dbe95d9d8902a29ae4d7eba612eac6c5892acfe8098b65ad', 'admin', '2021-04-09 23:12:29'),
(758, 'f04b6e4ffd548ec5b51b5b828660390946bd130ab6da0a94f387b4cbe3a785fd4d15c203c56bd30ea1195581f309fe678daaaff2c6a573c34f926f73513ba625', 'admin', '2021-04-09 23:12:31'),
(759, '2e24322783994b3da9f71f62004c8e286367c99c2b841eae3d74892a940cbb0b1d2acf83f16d23933827b37481c364aebed95c0bdbe740b85f520afa8a893b95', 'admin', '2021-04-09 23:13:22'),
(760, '3f31d5702ee725027b9ae6cf00502715b128b560bed71ac11ce28aa85091b492e048495716639035d225c809e462701b2081bf5b695e10c1f6f22fb2d94b4efc', 'admin', '2021-04-09 23:13:23'),
(761, '39670cfc42f5b8b2a7a414de81103277fcd48c39e4c316e00ed363637e4b3760b0681738f276d4fd835ea278161f786d0f72bcb7d51985402cbf9171153cbca5', 'admin', '2021-04-09 23:13:25'),
(762, '24c7663416194a944e66df3057c997a7abaee6cbce25f64c9a63816f528397b477bf5510272e94b7bb74b6fda312ab8dd8186193c59c4ce9dc76d7ebfbbba5e0', 'admin', '2021-04-10 17:13:15'),
(763, '475b32cee8e840d4024c8c481dc5344c8ac019b7172342b138f5f670e4f9c9de062adde1cc8a16460250fe62c32809c0a20ec5102f8997714c450d811fee71ec', 'admin', '2021-04-10 17:13:16'),
(764, 'd66681f175650a0200d401bdbc1620e848c1866e06377cc386b9feec642f1eedfd3ec11446cacb76cd9798538a8500b1c0ecf045d2bc11721a20eda3a5efa25e', 'admin', '2021-04-10 17:13:18'),
(765, '1360927ee568efc9b7a02a61289590af37b437f0d6480babb577a1432cdd147738f92faac808bbee4855e15cbad8fb82f0fe8e3273d420ba84bdb7b66385d29a', 'admin', '2021-04-10 17:13:31'),
(766, '47e8ee04fff0bddc37dda36cb7ac180d9eb87cfc61bb184b48b9b59a039fa1eaad585bb2acd0f905c508d6a2532f21c74b17ada2ace407cac8b33a8dbefeeb87', 'admin', '2021-04-10 17:13:32'),
(767, '5af209112e7c9fe256591a3123455007fd3ad0a1742fc2bf39b03befaac2099623130aebcdf6adfb597afa5d864eeaf38ed184f154a7489d084a7a7c4c2c477a', 'admin', '2021-04-10 17:13:34'),
(768, '325d9abed7273a4cfb33bec6ee443a665bd074fd22cb87d465631198cb089d084f57f0a093fb65116edf805d5bd1820848fc4881faed514039d4f06a72a4d7da', 'admin', '2021-04-10 21:51:36'),
(769, '9efc4c1b93dc040b2c2bdf01c253ca618b3547035ca2c96c2175023e93e420e03117c88e1cfc89b080cbde3cba86a91791f64f9c5ea4b620a8e9c3ee294fe331', 'admin', '2021-04-10 21:53:00');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `roomID` int(11) NOT NULL,
  `room` varchar(20) NOT NULL,
  `groupNameID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`roomID`, `room`, `groupNameID`) VALUES
(1, 'LivingRoom', 1),
(2, 'Bedroom', 1),
(3, 'Office', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sensornames`
--

CREATE TABLE `sensornames` (
  `sensorNameID` int(11) NOT NULL,
  `sensorName` varchar(20) NOT NULL,
  `deviceNameID` int(11) NOT NULL,
  `sensorTypeID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sensornames`
--

INSERT INTO `sensornames` (`sensorNameID`, `sensorName`, `deviceNameID`, `sensorTypeID`) VALUES
(4, 'testtt', 78, 1),
(5, 'test22', 74, 1),
(6, 'test22', 74, 1),
(7, 'test22', 74, 1),
(8, 'test22', 74, 1),
(9, 'test2244', 74, 1),
(10, 'test224444', 74, 1),
(11, 'test2244445', 74, 1),
(12, 'test1', 74, 1),
(13, 'test1', 74, 1),
(14, 'test1', 74, 1),
(15, 'test1', 74, 1),
(16, 'test1', 74, 1),
(17, 'test1', 74, 1),
(18, 'test1', 74, 1),
(19, 'test1', 74, 1),
(20, 'test1', 74, 1),
(21, 'test1', 74, 1),
(22, 'test1', 74, 1),
(23, 'test1', 74, 1),
(24, 'test1', 74, 1),
(25, 'test1', 74, 1),
(26, 'test1', 74, 1),
(27, 'test1', 74, 1),
(28, 'test1', 74, 1),
(29, 'test1', 74, 1),
(30, 'test13', 74, 1),
(31, 'newHuy', 81, 2),
(32, 'newbyy', 76, 1),
(33, 'newbyy', 76, 1),
(34, 'newbyy', 76, 1),
(35, 'newbyy', 76, 1),
(36, 'newbyy', 76, 1),
(37, 'newbyy', 76, 1),
(38, 'newbyy', 76, 1),
(39, 'newbyy', 76, 1),
(40, 'newbyy', 76, 1),
(41, 'newbyy', 76, 1),
(42, 'newbyy', 76, 1),
(43, 'newbyy', 76, 1),
(44, 'yeeeaaa', 74, 2),
(45, 'newtestbaby', 81, 1),
(46, 'whadasasd', 82, 1),
(47, 'YUeaass', 82, 1),
(48, 'YUeaassdd', 82, 1),
(49, 'yeeeaaalklll', 82, 1),
(50, 'yeeeaaalklllh', 82, 1),
(51, 'yeeeaaappfdd', 98, 1),
(52, 'bitcvhine', 96, 1),
(53, 'reeeaaaa', 96, 1),
(54, 'reeeaaaardd', 96, 1),
(55, 'fdddd', 96, 1),
(56, 'fddddsss', 96, 1),
(57, 'fddddsss33', 96, 1),
(58, 'fddddsss33e', 96, 1),
(59, 'fddddsss33eccc', 96, 1),
(60, 'dddd', 98, 1),
(61, 'rfdff', 74, 1),
(62, 'asdsasssssss', 74, 2),
(63, 'asdsasssssssccc', 74, 2),
(64, 'sdfdddd', 87, 1),
(65, 'sdfsdfdcccccqqq', 87, 1),
(66, 'hahahah', 98, 1),
(67, 'hahahah3', 98, 1),
(68, 'hahahahjj', 98, 1),
(69, 'hahahahjjfddd', 98, 1),
(70, 'hahahahjjfdddss', 98, 1),
(71, 'hahahahjjfdddss4', 98, 1),
(72, 'hahahahjjfdddss4v', 98, 1),
(73, 'hahahahjjfdddss4vd', 98, 1),
(74, 'hahahahjjfdddss4vdvv', 98, 1),
(75, 'dickheadddd', 98, 1),
(79, 'dickheaddddblahh', 98, 1),
(83, 'dickheaddddblahhss', 98, 1),
(89, 'soimebee', 74, 1),
(90, 'soimebee1', 74, 2),
(91, 'soimebee13', 74, 3),
(92, 'soimebee134', 74, 3),
(94, 'soimebee1346', 74, 4),
(95, 'soimebee13464', 74, 4),
(96, 'soimebee134642', 74, 4),
(97, 'soimebee1346423', 74, 4),
(98, 'wq22', 74, 4),
(99, 'asdzxxz', 74, 4),
(100, 'cxvgdf', 74, 4),
(101, 'cxvgdfty', 74, 4),
(102, 'cxvgdftyf', 74, 4),
(103, 'cxvgdftyf4', 74, 4),
(104, 'cxvgdftyf4asd', 74, 4),
(105, 'cxvgdftyf4asd2', 74, 4),
(106, '21', 74, 4),
(107, '213', 74, 4),
(108, '213s', 74, 4),
(109, '213sd', 74, 4),
(110, '213sdd', 74, 4),
(111, '213sddf', 74, 4),
(112, '213sddfd', 74, 4),
(113, '213sddfdc', 74, 4),
(114, '213sddfdcf', 74, 4),
(115, '213sddfdcfccc', 74, 4),
(116, '213sddfdcfcccd', 74, 4),
(117, '213sddfdcfcccdd', 74, 4),
(118, 'dsd', 74, 4),
(119, 'dsdf', 74, 4),
(120, 'dsdfg', 74, 4),
(121, 'dsdfgfdf', 74, 4),
(122, 'dsdfgfdff', 74, 4),
(123, 'dsdfgfdffvv', 74, 4),
(124, 'dsdfgfdffvvccc', 74, 3),
(125, 'dsdfgfds', 74, 3),
(126, 'dsdfgfdsf', 74, 3),
(127, 'dsdfgfdsfjj', 74, 3),
(128, 'dsdfgfdsfjjxx', 74, 3),
(129, 'dsdfgfdsfjjxxcc', 74, 3),
(130, 'dsdfgfdsfjjxxccb', 74, 3),
(131, 'hasbdhasassssss', 74, 3),
(132, 'hasbdhasassssssxxx', 74, 3),
(133, 'hasbdhasassssssxxxx', 74, 3),
(134, 'xzcxxxx', 74, 3),
(135, 'xzcxxxxc', 74, 3),
(136, 'xzcxxxxcc', 74, 3),
(137, 'xzcxxxxccc', 74, 3),
(138, 'xzcxxxxcccc', 74, 3),
(139, 'youux', 74, 3),
(140, 'youuxx', 74, 3),
(142, 'testingcheee', 74, 1),
(143, 'testcheee', 74, 2),
(144, 'asd', 74, 1),
(145, 'sdcxcccc', 74, 1),
(146, 'xccxxx', 74, 1),
(147, 'testttmnbm', 74, 1),
(148, 'khfgmhvcnvv', 74, 1),
(149, 'jesusss2', 104, 1),
(150, 'fdgcc', 104, 1),
(151, 'sdcx', 104, 1),
(152, 'sdcxsds', 104, 1),
(153, 'sdcxsdscxc', 104, 1),
(154, 'sdcxsdscxcmn', 104, 1),
(155, 'dsdcc', 104, 1),
(156, 'dsdccxx', 104, 1),
(157, 'dsdccxxx', 104, 1),
(158, 'dsdccxxxx', 104, 1),
(159, 'dsdccxxxxx', 104, 1),
(160, 'bgflkmfd', 104, 1),
(161, 'zxcxxxxxxzzz', 104, 4),
(162, 'zxcxxxxxxzzzs', 104, 4),
(163, 'zxcxxxxxxzzzsx', 104, 4),
(164, 'cfv', 104, 4),
(165, 'cfvb', 104, 1),
(166, 'cfvbfdsddf', 104, 1),
(167, 'unittestrequest', 74, 1),
(168, 'apitest', 78, 1),
(169, 'fdssss', 78, 1),
(170, 'apitesting', 101, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sensortype`
--

CREATE TABLE `sensortype` (
  `sensorTypeID` int(11) NOT NULL,
  `sensorType` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sensortype`
--

INSERT INTO `sensortype` (`sensorTypeID`, `sensorType`, `description`) VALUES
(1, 'DHT', 'Temperature and Humidity Sensor'),
(2, 'Dallas Temperature', 'Water Proof Temperature Sensor'),
(3, 'Soil', 'Soil Moisture Sensor'),
(4, 'BMP', 'Weather Station Sensor');

-- --------------------------------------------------------

--
-- Table structure for table `soil`
--

CREATE TABLE `soil` (
  `soilID` int(11) NOT NULL,
  `analogID` int(11) NOT NULL,
  `cardViewID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `soil`
--

INSERT INTO `soil` (`soilID`, `analogID`, `cardViewID`) VALUES
(5, 7, 98),
(7, 9, 101),
(8, 10, 105);

-- --------------------------------------------------------

--
-- Table structure for table `temp`
--

CREATE TABLE `temp` (
  `tempID` int(11) NOT NULL,
  `roomID` int(11) DEFAULT NULL,
  `groupNameID` int(11) DEFAULT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  `tempReading` float NOT NULL,
  `highTemp` float NOT NULL DEFAULT 26,
  `lowTemp` float NOT NULL DEFAULT 12,
  `constRecord` tinyint(1) NOT NULL DEFAULT 0,
  `deviceNameID` int(11) NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `temp`
--

INSERT INTO `temp` (`tempID`, `roomID`, `groupNameID`, `sensorNameID`, `tempReading`, `highTemp`, `lowTemp`, `constRecord`, `deviceNameID`, `timez`) VALUES
(9, NULL, NULL, 42, 10, 50, 10, 0, 76, '2021-02-01 22:33:23'),
(10, NULL, NULL, 43, 10, 12, 1, 1, 76, '2021-02-01 22:35:26'),
(11, NULL, NULL, 44, 45, 123, 10, 1, 74, '2021-02-01 22:36:53'),
(12, NULL, NULL, 45, 10, 42, 12, 1, 81, '2021-02-01 22:43:15'),
(13, NULL, NULL, 46, 10, 54, 12, 1, 82, '2021-02-01 23:02:08'),
(14, NULL, NULL, 47, 10, 50, 10, 0, 82, '2021-02-01 23:51:11'),
(15, NULL, NULL, 48, 10, 50, 10, 0, 82, '2021-02-01 23:54:21'),
(16, NULL, NULL, 49, 10, 50, 10, 0, 82, '2021-02-01 23:56:35'),
(17, NULL, NULL, 50, 10, 50, 10, 0, 82, '2021-02-01 23:56:46'),
(18, NULL, NULL, 51, 10, 50, 10, 1, 98, '2021-03-02 18:03:27'),
(19, NULL, NULL, 74, 10, 50, 10, 0, 98, '2021-04-02 12:15:37'),
(23, NULL, NULL, 79, 10, 50, 10, 0, 98, '2021-04-02 13:02:59'),
(32, NULL, NULL, 89, 10, 50, 10, 0, 74, '2021-04-03 12:04:33'),
(33, NULL, NULL, 90, 10, 50, 10, 0, 74, '2021-04-03 12:04:44'),
(34, NULL, NULL, 123, 10, 50, 10, 0, 74, '2021-04-03 12:47:20'),
(35, NULL, NULL, 142, 10, 50, 10, 0, 74, '2021-04-04 16:36:19'),
(36, NULL, NULL, 143, 10, 50, 10, 0, 74, '2021-04-04 16:42:15'),
(37, NULL, NULL, 144, 10, 50, 10, 0, 74, '2021-04-04 16:46:26'),
(38, NULL, NULL, 145, 10, 50, 10, 0, 74, '2021-04-04 16:51:55'),
(39, NULL, NULL, 146, 10, 50, 10, 0, 74, '2021-04-04 16:53:24'),
(40, NULL, NULL, 147, 10, 50, 10, 0, 74, '2021-04-04 16:55:08'),
(41, NULL, NULL, 148, 10, 50, 10, 0, 74, '2021-04-04 16:57:00'),
(42, NULL, NULL, 159, 10, 50, 10, 0, 104, '2021-04-05 20:27:04'),
(43, NULL, NULL, 160, 10, 50, 10, 0, 104, '2021-04-05 20:27:47'),
(44, NULL, NULL, 165, 10, 50, 10, 0, 104, '2021-04-05 20:34:57'),
(45, NULL, NULL, 166, 10, 50, 10, 1, 104, '2021-04-05 20:35:20'),
(46, NULL, NULL, 167, 10, 50, 10, 0, 74, '2021-04-09 12:41:51'),
(47, NULL, NULL, 168, 10, 50, 10, 0, 78, '2021-04-09 15:06:07'),
(48, NULL, NULL, 169, 10, 50, 10, 0, 78, '2021-04-09 16:48:46'),
(49, NULL, NULL, 170, 10, 50, 10, 0, 101, '2021-04-09 18:15:13');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `firstName` varchar(20) NOT NULL,
  `lastName` varchar(20) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:json)',
  `profilePic` varchar(100) DEFAULT '/assets/pictures/guest.jpg',
  `password` longtext NOT NULL,
  `salt` longtext DEFAULT NULL,
  `groupNameID` int(11) DEFAULT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `firstName`, `lastName`, `email`, `roles`, `profilePic`, `password`, `salt`, `groupNameID`, `timez`) VALUES
(1, 'admin', 'admin', 'admin', '[\"ROLE_USER\",\"ROLE_ADMIN\"]', '/assets/pictures/guest.jpg', '$argon2id$v=19$m=65536,t=4,p=1$YZtgDPbw/cXStIttSFXAgQ$+S2z//XpDmqAvprgfCC1MonuFoMUBMzD4iZ7E0xdE64', NULL, 1, '2020-07-07 23:00:48'),
(2, 'liga', 'lase', 'liga@gmail.com', '[\"ROLE_USER\"]', '/assets/pictures/guest.jpg', '$argon2id$v=19$m=65536,t=4,p=1$RWovZmpCWlFjdFczVHFEMw$3/v5w7k8stIyCskaXT3DOgshO6pjpsUDFqHxo4B9AT0', NULL, 11, '2021-04-03 11:38:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analog`
--
ALTER TABLE `analog`
  ADD PRIMARY KEY (`analogID`),
  ADD UNIQUE KEY `sensorNameID` (`sensorNameID`),
  ADD KEY `roomID` (`roomID`),
  ADD KEY `groupNameID` (`groupNameID`),
  ADD KEY `analog_ibfk_3` (`sensorNameID`),
  ADD KEY `analog_ibfk_6` (`deviceNameID`);

--
-- Indexes for table `bmp`
--
ALTER TABLE `bmp`
  ADD PRIMARY KEY (`bmpID`),
  ADD UNIQUE KEY `tempID*` (`tempID`),
  ADD UNIQUE KEY `humidID` (`humidID`),
  ADD UNIQUE KEY `latitudeID` (`latitudeID`),
  ADD UNIQUE KEY `cardViewID` (`cardViewID`);

--
-- Indexes for table `cardcolour`
--
ALTER TABLE `cardcolour`
  ADD PRIMARY KEY (`colourID`),
  ADD UNIQUE KEY `colour` (`colour`);

--
-- Indexes for table `cardstate`
--
ALTER TABLE `cardstate`
  ADD PRIMARY KEY (`cardStateID`);

--
-- Indexes for table `cardview`
--
ALTER TABLE `cardview`
  ADD PRIMARY KEY (`cardViewID`),
  ADD UNIQUE KEY `sensorNameID` (`sensorNameID`),
  ADD KEY `UserID` (`userID`),
  ADD KEY `cardColour` (`cardColourID`),
  ADD KEY `cardIcon` (`cardIconID`),
  ADD KEY `cardview_show` (`cardViewID`),
  ADD KEY `cardState` (`cardStateID`);

--
-- Indexes for table `constanalog`
--
ALTER TABLE `constanalog`
  ADD PRIMARY KEY (`analogID`),
  ADD KEY `sensorID` (`sensorID`);

--
-- Indexes for table `consthumid`
--
ALTER TABLE `consthumid`
  ADD PRIMARY KEY (`humidID`),
  ADD KEY `sensorID` (`sensorID`);

--
-- Indexes for table `consttemp`
--
ALTER TABLE `consttemp`
  ADD PRIMARY KEY (`tempID`),
  ADD KEY `consttemp_ibfk_1` (`sensorID`);

--
-- Indexes for table `dallas`
--
ALTER TABLE `dallas`
  ADD PRIMARY KEY (`dallasID`),
  ADD UNIQUE KEY `tempID` (`tempID`),
  ADD UNIQUE KEY `cardViewID` (`cardViewID`),
  ADD KEY `sensorNameID` (`sensorNameID`);

--
-- Indexes for table `devicenames`
--
ALTER TABLE `devicenames`
  ADD PRIMARY KEY (`deviceNameID`),
  ADD KEY `groupNameID` (`groupNameID`),
  ADD KEY `roomID` (`roomID`),
  ADD KEY `createdBy` (`createdBy`);

--
-- Indexes for table `dhtsensor`
--
ALTER TABLE `dhtsensor`
  ADD PRIMARY KEY (`dhtID`),
  ADD UNIQUE KEY `tempID` (`tempID`),
  ADD UNIQUE KEY `humidID` (`humidID`),
  ADD UNIQUE KEY `cardviewID` (`cardviewID`);

--
-- Indexes for table `groupname`
--
ALTER TABLE `groupname`
  ADD PRIMARY KEY (`groupNameID`),
  ADD UNIQUE KEY `groupName` (`groupName`);

--
-- Indexes for table `groupnnamemapping`
--
ALTER TABLE `groupnnamemapping`
  ADD PRIMARY KEY (`groupNameMappingID`),
  ADD KEY `groupNameID` (`groupNameID`),
  ADD KEY `userID` (`userID`,`groupNameID`);

--
-- Indexes for table `humid`
--
ALTER TABLE `humid`
  ADD PRIMARY KEY (`humidID`),
  ADD UNIQUE KEY `sensorNameID` (`sensorNameID`),
  ADD KEY `Room` (`roomID`),
  ADD KEY `GroupName` (`groupNameID`),
  ADD KEY `humid_ibfk_3` (`sensorNameID`),
  ADD KEY `humid_ibfk_6` (`deviceNameID`);

--
-- Indexes for table `icons`
--
ALTER TABLE `icons`
  ADD PRIMARY KEY (`iconID`),
  ADD UNIQUE KEY `iconName_2` (`iconName`);

--
-- Indexes for table `latitude`
--
ALTER TABLE `latitude`
  ADD PRIMARY KEY (`latitudeID`),
  ADD UNIQUE KEY `sensorNameID` (`sensorNameID`),
  ADD UNIQUE KEY `deviceNameID` (`deviceNameID`);

--
-- Indexes for table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `outofrangeanalog`
--
ALTER TABLE `outofrangeanalog`
  ADD PRIMARY KEY (`analogID`),
  ADD KEY `sensorID` (`sensorID`);

--
-- Indexes for table `outofrangehumid`
--
ALTER TABLE `outofrangehumid`
  ADD PRIMARY KEY (`humidID`),
  ADD KEY `sensorID` (`sensorID`);

--
-- Indexes for table `outofrangetemp`
--
ALTER TABLE `outofrangetemp`
  ADD PRIMARY KEY (`tempID`),
  ADD KEY `outofrangetemp_ibfk_1` (`sensorID`);

--
-- Indexes for table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_9BACE7E1C74F2195` (`refresh_token`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`roomID`),
  ADD KEY `GroupName` (`groupNameID`);

--
-- Indexes for table `sensornames`
--
ALTER TABLE `sensornames`
  ADD PRIMARY KEY (`sensorNameID`),
  ADD KEY `SensorType` (`sensorTypeID`),
  ADD KEY `sensornames_ibfk_1` (`deviceNameID`);

--
-- Indexes for table `sensortype`
--
ALTER TABLE `sensortype`
  ADD PRIMARY KEY (`sensorTypeID`),
  ADD UNIQUE KEY `sensorType` (`sensorType`);

--
-- Indexes for table `soil`
--
ALTER TABLE `soil`
  ADD PRIMARY KEY (`soilID`),
  ADD UNIQUE KEY `analogID` (`analogID`),
  ADD UNIQUE KEY `cardViewID` (`cardViewID`);

--
-- Indexes for table `temp`
--
ALTER TABLE `temp`
  ADD PRIMARY KEY (`tempID`),
  ADD UNIQUE KEY `sensorNameID` (`sensorNameID`),
  ADD KEY `Room` (`roomID`),
  ADD KEY `GroupName` (`groupNameID`),
  ADD KEY `temp_ibfk_6` (`deviceNameID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`),
  ADD KEY `GroupName` (`groupNameID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `analog`
--
ALTER TABLE `analog`
  MODIFY `analogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bmp`
--
ALTER TABLE `bmp`
  MODIFY `bmpID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cardcolour`
--
ALTER TABLE `cardcolour`
  MODIFY `colourID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cardstate`
--
ALTER TABLE `cardstate`
  MODIFY `cardStateID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cardview`
--
ALTER TABLE `cardview`
  MODIFY `cardViewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `constanalog`
--
ALTER TABLE `constanalog`
  MODIFY `analogID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consthumid`
--
ALTER TABLE `consthumid`
  MODIFY `humidID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consttemp`
--
ALTER TABLE `consttemp`
  MODIFY `tempID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dallas`
--
ALTER TABLE `dallas`
  MODIFY `dallasID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `devicenames`
--
ALTER TABLE `devicenames`
  MODIFY `deviceNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `dhtsensor`
--
ALTER TABLE `dhtsensor`
  MODIFY `dhtID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `groupname`
--
ALTER TABLE `groupname`
  MODIFY `groupNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `groupnnamemapping`
--
ALTER TABLE `groupnnamemapping`
  MODIFY `groupNameMappingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `humid`
--
ALTER TABLE `humid`
  MODIFY `humidID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `icons`
--
ALTER TABLE `icons`
  MODIFY `iconID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `latitude`
--
ALTER TABLE `latitude`
  MODIFY `latitudeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `outofrangeanalog`
--
ALTER TABLE `outofrangeanalog`
  MODIFY `analogID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `outofrangehumid`
--
ALTER TABLE `outofrangehumid`
  MODIFY `humidID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `outofrangetemp`
--
ALTER TABLE `outofrangetemp`
  MODIFY `tempID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=770;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `roomID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sensornames`
--
ALTER TABLE `sensornames`
  MODIFY `sensorNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `sensortype`
--
ALTER TABLE `sensortype`
  MODIFY `sensorTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `soil`
--
ALTER TABLE `soil`
  MODIFY `soilID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `temp`
--
ALTER TABLE `temp`
  MODIFY `tempID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `analog`
--
ALTER TABLE `analog`
  ADD CONSTRAINT `FK_A78C95C12D8C0469` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_A78C95C13BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_A78C95C19F0A2316` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_A78C95C19F0A2419` FOREIGN KEY (`deviceNameID`) REFERENCES `devicenames` (`deviceNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bmp`
--
ALTER TABLE `bmp`
  ADD CONSTRAINT `bmp_ibfk_1` FOREIGN KEY (`cardViewID`) REFERENCES `cardview` (`cardViewID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bmp_ibfk_2` FOREIGN KEY (`tempID`) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bmp_ibfk_3` FOREIGN KEY (`humidID`) REFERENCES `humid` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bmp_ibfk_4` FOREIGN KEY (`latitudeID`) REFERENCES `latitude` (`latitudeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cardview`
--
ALTER TABLE `cardview`
  ADD CONSTRAINT `FK_E36636B53BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_E36636B53casrdState` FOREIGN KEY (`cardStateID`) REFERENCES `cardstate` (`cardStateID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_E36636B55FD86D04` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_E36636B5840D9A7A` FOREIGN KEY (`cardIconID`) REFERENCES `icons` (`iconID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_E36636B5A356FF88` FOREIGN KEY (`cardColourID`) REFERENCES `cardcolour` (`colourID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `constanalog`
--
ALTER TABLE `constanalog`
  ADD CONSTRAINT `constanalog_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `consthumid`
--
ALTER TABLE `consthumid`
  ADD CONSTRAINT `consthumid_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `humid` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `consttemp`
--
ALTER TABLE `consttemp`
  ADD CONSTRAINT `consttemp_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dallas`
--
ALTER TABLE `dallas`
  ADD CONSTRAINT `dallas_ibfk_1` FOREIGN KEY (`cardViewID`) REFERENCES `cardview` (`cardViewID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dallas_ibfk_2` FOREIGN KEY (`tempID`) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dallas_ibfk_3` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `devicenames`
--
ALTER TABLE `devicenames`
  ADD CONSTRAINT `devicenames_ibfk_1` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devicenames_ibfk_2` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devicenames_ibfk_3` FOREIGN KEY (`createdBy`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dhtsensor`
--
ALTER TABLE `dhtsensor`
  ADD CONSTRAINT `dhtsensor_ibfk_1` FOREIGN KEY (`cardviewID`) REFERENCES `cardview` (`cardViewID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dhtsensor_ibfk_2` FOREIGN KEY (`humidID`) REFERENCES `humid` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dhtsensor_ibfk_3` FOREIGN KEY (`tempID`) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `groupnnamemapping`
--
ALTER TABLE `groupnnamemapping`
  ADD CONSTRAINT `groupnnamemapping_ibfk_1` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `groupnnamemapping_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `humid`
--
ALTER TABLE `humid`
  ADD CONSTRAINT `FK_8D6EB6E32D8C0469` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_8D6EB6E33BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_8D6EB6E39F0A2316` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_A78C95C19F0A2317` FOREIGN KEY (`deviceNameID`) REFERENCES `devicenames` (`deviceNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `latitude`
--
ALTER TABLE `latitude`
  ADD CONSTRAINT `latitude_ibfk_1` FOREIGN KEY (`deviceNameID`) REFERENCES `devicenames` (`deviceNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `latitude_ibfk_4` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `outofrangeanalog`
--
ALTER TABLE `outofrangeanalog`
  ADD CONSTRAINT `outofrangeanalog_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `outofrangehumid`
--
ALTER TABLE `outofrangehumid`
  ADD CONSTRAINT `outofrangehumid_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `outofrangetemp`
--
ALTER TABLE `outofrangetemp`
  ADD CONSTRAINT `outofrangetemp_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `FK_729F519B2D8C0469` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`);

--
-- Constraints for table `sensornames`
--
ALTER TABLE `sensornames`
  ADD CONSTRAINT `FK_82F2A8F46B4A071A` FOREIGN KEY (`sensorTypeID`) REFERENCES `sensortype` (`sensorTypeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sensornames_ibfk_1` FOREIGN KEY (`deviceNameID`) REFERENCES `devicenames` (`deviceNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `soil`
--
ALTER TABLE `soil`
  ADD CONSTRAINT `soil_ibfk_1` FOREIGN KEY (`analogID`) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `soil_ibfk_2` FOREIGN KEY (`cardViewID`) REFERENCES `cardview` (`cardViewID`);

--
-- Constraints for table `temp`
--
ALTER TABLE `temp`
  ADD CONSTRAINT `FK_A78C95C19F0A231` FOREIGN KEY (`deviceNameID`) REFERENCES `devicenames` (`deviceNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_B5385CA2D8C0469` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_B5385CA3BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_B5385CA9F0A2316` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
