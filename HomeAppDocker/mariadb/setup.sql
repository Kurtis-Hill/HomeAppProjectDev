DROP DATABASE IF EXISTS HomeApp;
DROP DATABASE IF EXISTS HomeAppTest;

CREATE DATABASE IF NOT EXISTS HomeApp;
CREATE DATABASE IF NOT EXISTS HomeAppTest;

use HomeApp;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `HomeApp`
--

-- --------------------------------------------------------

--
-- Table structure for table `analog`
--

CREATE TABLE `analog` (
  `analogID` int(11) NOT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  `analogReading` smallint(6) DEFAULT NULL,
  `highAnalog` smallint(6) DEFAULT 1000,
  `lowAnalog` smallint(6) DEFAULT 1000,
  `constRecord` tinyint(4) DEFAULT 0,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `bmp`
--

CREATE TABLE `bmp` (
  `bmpID` int(11) NOT NULL,
  `tempID` int(11) NOT NULL,
  `humidID` int(11) NOT NULL,
  `latitudeID` int(11) NOT NULL,
  `sensorNameID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(1, 'danger', 'red'),
(2, 'success', 'green'),
(3, 'warning', 'Yellow'),
(4, 'primary', 'blue');

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
(1, 'ON'),
(2, 'OFF'),
(3, 'DEVICE_ONLY'),
(4, 'ROOM_ONLY');

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


-- --------------------------------------------------------

--
-- Table structure for table `constanalog`
--

CREATE TABLE `constanalog` (
  `constRecordID` int(11) NOT NULL,
  `analogID` int(11) NOT NULL,
  `sensorReading` float NOT NULL,
  `timez` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `consthumid`
--

CREATE TABLE `consthumid` (
`constRecordID` int(11) NOT NULL,
`humidID` int(11) NOT NULL,
`sensorReading` float NOT NULL,
`timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `consttemp`
--

CREATE TABLE `consttemp` (
`constRecordID` int(11) NOT NULL,
`tempID` int(11) NOT NULL,
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
  `sensorNameID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


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


-- --------------------------------------------------------

--
-- Table structure for table `dhtsensor`
--

CREATE TABLE `dhtsensor` (
  `dhtID` int(11) NOT NULL,
  `tempID` int(11) NOT NULL,
  `humidID` int(11) NOT NULL,
  `sensorNameID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- Table structure for table `groupname`
--

CREATE TABLE `groupname` (
  `groupNameID` int(11) NOT NULL,
  `groupName` varchar(50) NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `groupname` (`groupNameID`, `groupName`, `timez`) VALUES
(1, 'admin', '2021-06-06 02:54:58');
-- --------------------------------------------------------

--
-- Table structure for table `groupnnamemapping`
--

CREATE TABLE `groupnnamemapping` (
  `groupNameMappingID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `groupNameID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `groupnnamemapping` (`groupNameMappingID`, `userID`, `groupNameID`) VALUES
(1, 1, 1);


-- --------------------------------------------------------

--
-- Table structure for table `humid`
--

CREATE TABLE `humid` (
  `humidID` int(11) NOT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  `humidReading` float NOT NULL,
  `highHumid` float NOT NULL DEFAULT 70,
  `lowHumid` float NOT NULL DEFAULT 15,
  `constRecord` tinyint(1) NOT NULL DEFAULT 0,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `icons`
--

CREATE TABLE `icons` (
  `iconID` int(11) NOT NULL,
  `iconName` varchar(20) DEFAULT NULL,
  `description` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `icons`
--

INSERT INTO `icons` (`iconID`, `iconName`, `description`) VALUES
(1, 'air-freshener', 'Christmas tree'),
(2, 'warehouse', 'warehouse'),
(3, 'archway', 'archway'),
(4, 'baby', 'baby'),
(5, 'bath', 'bath and shower'),
(6, 'bed', 'bed'),
(7, 'cannabis', 'cannabis leaf'),
(8, 'camera', 'camera'),
(9, 'carrot', 'carrot'),
(10, 'campground', 'tent'),
(11, 'chart-pie', 'graph'),
(12, 'crosshairs', 'crosshair'),
(13, 'database', 'symbol'),
(14, 'dog', 'doggie'),
(15, 'dove', 'bird'),
(16, 'download', 'download logo'),
(17, 'fish', 'fishys'),
(18, 'flask', 'science beaker'),
(19, 'fort-awesome', 'castle'),
(20, 'mobile-alt', 'mobile phone'),
(21, 'php', 'php logo'),
(22, 'Playstation', 'ps1 logo'),
(23, 'power-off', 'shutdown logo'),
(24, 'raspberry-pi', 'pi logo'),
(25, 'xbox', 'xbox logo'),
(26, 'skull-crossbones', 'skull and bones'),
(27, 'smoking', 'smoking');

-- --------------------------------------------------------

--
-- Table structure for table `latitude`
--

CREATE TABLE `latitude` (
  `latitudeID` int(11) NOT NULL,
  `sensorNameID` int(11) NOT NULL,
  `deviceNameID` int(11) DEFAULT NULL,
  `latitude` int(11) NOT NULL,
  `lowLatitude` int(11) NOT NULL,
  `highLatitude` int(11) NOT NULL,
  `constRecord` tinyint(1) NOT NULL DEFAULT 0,
  `timez` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


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
  `outofrangeID` int(11) NOT NULL,
  `analogID` int(11) NOT NULL,
  `sensorReading` float DEFAULT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `outofrangehumid`
--

CREATE TABLE `outofrangehumid` (
  `outofrangeID` int(11) NOT NULL,
  `humidID` int(11) NOT NULL,
  `sensorReading` float NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `outofrangetemp`
--

CREATE TABLE `outofrangetemp` (
  `outofrangeID` int(11) NOT NULL,
  `tempID` int(11) NOT NULL,
  `sensorReading` float NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `roomID` int(11) NOT NULL,
  `room` varchar(20) NOT NULL,
  `groupNameID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `sensornames`
--

CREATE TABLE `sensornames` (
  `sensorNameID` int(11) NOT NULL,
  `createdBy` int(11) DEFAULT NULL,
  `sensorName` varchar(20) NOT NULL,
  `deviceNameID` int(11) NOT NULL,
  `sensorTypeID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `sensortype`
--

CREATE TABLE `sensortype` (
  `sensorTypeID` int(11) NOT NULL,
  `sensorType` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `soil`
--

CREATE TABLE `soil` (
  `soilID` int(11) NOT NULL,
  `analogID` int(11) NOT NULL,
  `sensorNameID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `temp`
--

CREATE TABLE `temp` (
  `tempID` int(11) NOT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  `tempReading` float NOT NULL,
  `highTemp` float NOT NULL DEFAULT 26,
  `lowTemp` float NOT NULL DEFAULT 12,
  `constRecord` tinyint(1) NOT NULL DEFAULT 0,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

INSERT INTO `user` VALUES
(
1,'admin','admin','admin','[\"ROLE_ADMIN\"]','/assets/pictures/guest.jpg','$argon2id$v=19$m=65536,t=4,p=1$7zx+pasSn547DYfLgO9MuQ$ACTjDqrmJDgB9KfoZUOpESDZn/071R/Bmfju9o+R1Zw',NULL,1,'2021-07-15 17:19:32'
);
--
-- Indexes for dumped tables
--

--
-- Indexes for table `analog`
--
ALTER TABLE `analog`
  ADD PRIMARY KEY (`analogID`),
  ADD UNIQUE KEY `sensorNameID` (`sensorNameID`),
  ADD KEY `analog_ibfk_3` (`sensorNameID`);

--
-- Indexes for table `bmp`
--
ALTER TABLE `bmp`
  ADD PRIMARY KEY (`bmpID`),
  ADD UNIQUE KEY `tempID*` (`tempID`),
  ADD UNIQUE KEY `humidID` (`humidID`),
  ADD UNIQUE KEY `latitudeID` (`latitudeID`),
  ADD UNIQUE KEY `cardViewID` (`sensorNameID`);

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
  ADD KEY `UserID` (`userID`),
  ADD KEY `cardColour` (`cardColourID`),
  ADD KEY `cardIcon` (`cardIconID`),
  ADD KEY `cardview_show` (`cardViewID`),
  ADD KEY `cardState` (`cardStateID`),
  ADD KEY `FK_E36636B53BE475E6` (`sensorNameID`);

--
-- Indexes for table `constanalog`
--
ALTER TABLE `constanalog`
  ADD PRIMARY KEY (constRecordID),
  ADD KEY `sensorID` (analogID);

--
-- Indexes for table `consthumid`
--
ALTER TABLE `consthumid`
  ADD PRIMARY KEY (constRecordID),
  ADD KEY `sensorID` (`humidID`);

--
-- Indexes for table `consttemp`
--
ALTER TABLE `consttemp`
  ADD PRIMARY KEY (constRecordID),
  ADD KEY `consttemp_ibfk_1` (tempID);

--
-- Indexes for table `dallas`
--
ALTER TABLE `dallas`
  ADD PRIMARY KEY (`dallasID`),
  ADD UNIQUE KEY `tempID` (`tempID`),
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
  ADD UNIQUE KEY `cardviewID` (`sensorNameID`);

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
  ADD KEY `humid_ibfk_3` (`sensorNameID`);

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
  ADD PRIMARY KEY (outofrangeID),
  ADD KEY `sensorID` (analogID);

--
-- Indexes for table `outofrangehumid`
--
ALTER TABLE `outofrangehumid`
  ADD PRIMARY KEY (outofrangeID),
  ADD KEY `sensorID` (humidID);

--
-- Indexes for table `outofrangetemp`
--
ALTER TABLE `outofrangetemp`
  ADD PRIMARY KEY (`outofrangeID`),
  ADD KEY `outofrangetemp_ibfk_1` (`tempID`);

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
  ADD KEY `sensornames_ibfk_1` (`deviceNameID`),
  ADD KEY `sensornames_ibfk_2` (`createdBy`);

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
  ADD UNIQUE KEY `cardViewID` (`sensorNameID`);

--
-- Indexes for table `temp`
--
ALTER TABLE `temp`
  ADD PRIMARY KEY (`tempID`),
  ADD UNIQUE KEY `sensorNameID` (`sensorNameID`);

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
  MODIFY `analogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=337;

--
-- AUTO_INCREMENT for table `bmp`
--
ALTER TABLE `bmp`
  MODIFY `bmpID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT for table `cardcolour`
--
ALTER TABLE `cardcolour`
  MODIFY `colourID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `cardstate`
--
ALTER TABLE `cardstate`
  MODIFY `cardStateID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `cardview`
--
ALTER TABLE `cardview`
  MODIFY `cardViewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2598;

--
-- AUTO_INCREMENT for table `constanalog`
--
ALTER TABLE `constanalog`
  MODIFY constRecordID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consthumid`
--
ALTER TABLE `consthumid`
  MODIFY constRecordID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consttemp`
--
ALTER TABLE `consttemp`
  MODIFY constRecordID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dallas`
--
ALTER TABLE `dallas`
  MODIFY `dallasID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=267;

--
-- AUTO_INCREMENT for table `devicenames`
--
ALTER TABLE `devicenames`
  MODIFY `deviceNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1111;

--
-- AUTO_INCREMENT for table `dhtsensor`
--
ALTER TABLE `dhtsensor`
  MODIFY `dhtID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=368;

--
-- AUTO_INCREMENT for table `groupname`
--
ALTER TABLE `groupname`
  MODIFY `groupNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT for table `groupnnamemapping`
--
ALTER TABLE `groupnnamemapping`
  MODIFY `groupNameMappingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=320;

--
-- AUTO_INCREMENT for table `humid`
--
ALTER TABLE `humid`
  MODIFY `humidID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=828;

--
-- AUTO_INCREMENT for table `icons`
--
ALTER TABLE `icons`
  MODIFY `iconID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1244;

--
-- AUTO_INCREMENT for table `latitude`
--
ALTER TABLE `latitude`
  MODIFY `latitudeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=298;

--
-- AUTO_INCREMENT for table `outofrangeanalog`
--
ALTER TABLE `outofrangeanalog`
  MODIFY outofrangeID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `outofrangehumid`
--
ALTER TABLE `outofrangehumid`
  MODIFY outofrangeID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `outofrangetemp`
--
ALTER TABLE `outofrangetemp`
  MODIFY `outofrangeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6868;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `roomID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `sensornames`
--
ALTER TABLE `sensornames`
  MODIFY `sensorNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2339;

--
-- AUTO_INCREMENT for table `sensortype`
--
ALTER TABLE `sensortype`
  MODIFY `sensorTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=317;

--
-- AUTO_INCREMENT for table `soil`
--
ALTER TABLE `soil`
  MODIFY `soilID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- AUTO_INCREMENT for table `temp`
--
ALTER TABLE `temp`
  MODIFY `tempID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1285;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=276;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `analog`
--
ALTER TABLE `analog`
  ADD CONSTRAINT `FK_A78C95C13BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bmp`
--
ALTER TABLE `bmp`
  ADD CONSTRAINT `bmp_ibfk_1` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bmp_ibfk_2` FOREIGN KEY (`tempID`) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bmp_ibfk_3` FOREIGN KEY (`humidID`) REFERENCES `humid` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bmp_ibfk_4` FOREIGN KEY (`latitudeID`) REFERENCES `latitude` (`latitudeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cardview`
--
ALTER TABLE `cardview`
  ADD CONSTRAINT `FK_E36636B53BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_E36636B53casrdState` FOREIGN KEY (`cardStateID`) REFERENCES `cardstate` (`cardStateID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_E36636B55FD86D04` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_E36636B5840D9A7A` FOREIGN KEY (`cardIconID`) REFERENCES `icons` (`iconID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_E36636B5A356FF88` FOREIGN KEY (`cardColourID`) REFERENCES `cardcolour` (`colourID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `constanalog`
--
ALTER TABLE `constanalog`
  ADD CONSTRAINT `constanalog_ibfk_1` FOREIGN KEY (analogID) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `consthumid`
--
ALTER TABLE `consthumid`
  ADD CONSTRAINT `consthumid_ibfk_1` FOREIGN KEY (humidID) REFERENCES `humid` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `consttemp`
--
ALTER TABLE `consttemp`
  ADD CONSTRAINT `consttemp_ibfk_1` FOREIGN KEY (tempID) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dallas`
--
ALTER TABLE `dallas`
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
  ADD CONSTRAINT `dhtsensor_ibfk_1` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
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
  ADD CONSTRAINT `FK_8D6EB6E33BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `outofrangeanalog_ibfk_1` FOREIGN KEY (analogID) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `outofrangehumid`
--
ALTER TABLE `outofrangehumid`
  ADD CONSTRAINT `outofrangehumid_ibfk_1` FOREIGN KEY (humidID) REFERENCES `humid` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `outofrangetemp`
--
ALTER TABLE `outofrangetemp`
  ADD CONSTRAINT `outofrangetemp_ibfk_1` FOREIGN KEY (`tempID`) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `sensornames_ibfk_1` FOREIGN KEY (`deviceNameID`) REFERENCES `devicenames` (`deviceNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sensornames_ibfk_2` FOREIGN KEY (`createdBy`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `soil`
--
ALTER TABLE `soil`
  ADD CONSTRAINT `soil_ibfk_1` FOREIGN KEY (`analogID`) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `soil_ibfk_2` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `temp`
--
ALTER TABLE `temp`
  ADD CONSTRAINT `FK_B5385CA3BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`);
COMMIT;


use HomeAppTest;
--
-- Database: `HomeAppTest`
--

-- --------------------------------------------------------

--
-- Table structure for table `analog`
--

CREATE TABLE `analog` (
`analogID` int(11) NOT NULL,
`sensorNameID` int(11) DEFAULT NULL,
`analogReading` smallint(6) DEFAULT NULL,
`highAnalog` smallint(6) DEFAULT 1000,
`lowAnalog` smallint(6) DEFAULT 1000,
`constRecord` tinyint(4) DEFAULT 0,
`timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `bmp`
--

CREATE TABLE `bmp` (
`bmpID` int(11) NOT NULL,
`tempID` int(11) NOT NULL,
`humidID` int(11) NOT NULL,
`latitudeID` int(11) NOT NULL,
`sensorNameID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(1, 'danger', 'red'),
(2, 'success', 'green'),
(3, 'warning', 'Yellow'),
(4, 'primary', 'blue');

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
(1, 'ON'),
(2, 'OFF'),
(3, 'DEVICE_ONLY'),
(4, 'ROOM_ONLY');

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


-- --------------------------------------------------------

--
-- Table structure for table `constanalog`
--

CREATE TABLE `constanalog` (
`constRecordID` int(11) NOT NULL,
`analogID` int(11) NOT NULL,
`sensorReading` float NOT NULL,
`timez` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `consthumid`
--

CREATE TABLE `consthumid` (
`constRecordID` int(11) NOT NULL,
`humidID` int(11) NOT NULL,
`sensorReading` float NOT NULL,
`timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `consttemp`
--

CREATE TABLE `consttemp` (
`constRecordID` int(11) NOT NULL,
`tempID` int(11) NOT NULL,
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
`sensorNameID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


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


-- --------------------------------------------------------

--
-- Table structure for table `dhtsensor`
--

CREATE TABLE `dhtsensor` (
`dhtID` int(11) NOT NULL,
`tempID` int(11) NOT NULL,
`humidID` int(11) NOT NULL,
`sensorNameID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- Table structure for table `groupname`
--

CREATE TABLE `groupname` (
`groupNameID` int(11) NOT NULL,
`groupName` varchar(50) NOT NULL,
`timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `groupnnamemapping`
--

CREATE TABLE `groupnnamemapping` (
 `groupNameMappingID` int(11) NOT NULL,
 `userID` int(11) NOT NULL,
 `groupNameID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- --------------------------------------------------------

--
-- Table structure for table `humid`
--

CREATE TABLE `humid` (
`humidID` int(11) NOT NULL,
`sensorNameID` int(11) DEFAULT NULL,
`humidReading` float NOT NULL,
`highHumid` float NOT NULL DEFAULT 70,
`lowHumid` float NOT NULL DEFAULT 15,
`constRecord` tinyint(1) NOT NULL DEFAULT 0,
`timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `icons`
--

CREATE TABLE `icons` (
`iconID` int(11) NOT NULL,
`iconName` varchar(20) DEFAULT NULL,
`description` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `icons`
--

INSERT INTO `icons` (`iconID`, `iconName`, `description`) VALUES
                          (1, 'air-freshener', 'Christmas tree'),
                          (2, 'warehouse', 'warehouse'),
                          (3, 'archway', 'archway'),
                          (4, 'baby', 'baby'),
                          (5, 'bath', 'bath and shower'),
                          (6, 'bed', 'bed'),
                          (7, 'cannabis', 'cannabis leaf'),
                          (8, 'camera', 'camera'),
                          (9, 'carrot', 'carrot'),
                          (10, 'campground', 'tent'),
                          (11, 'chart-pie', 'graph'),
                          (12, 'crosshairs', 'crosshair'),
                          (13, 'database', 'symbol'),
                          (14, 'dog', 'doggie'),
                          (15, 'dove', 'bird'),
                          (16, 'download', 'download logo'),
                          (17, 'fish', 'fishys'),
                          (18, 'flask', 'science beaker'),
                          (19, 'fort-awesome', 'castle'),
                          (20, 'mobile-alt', 'mobile phone'),
                          (21, 'php', 'php logo'),
                          (22, 'Playstation', 'ps1 logo'),
                          (23, 'power-off', 'shutdown logo'),
                          (24, 'raspberry-pi', 'pi logo'),
                          (25, 'xbox', 'xbox logo'),
                          (26, 'skull-crossbones', 'skull and bones'),
                          (27, 'smoking', 'smoking');

-- --------------------------------------------------------

--
-- Table structure for table `latitude`
--

CREATE TABLE `latitude` (
`latitudeID` int(11) NOT NULL,
`sensorNameID` int(11) NOT NULL,
`deviceNameID` int(11) DEFAULT NULL,
`latitude` int(11) NOT NULL,
`lowLatitude` int(11) NOT NULL,
`highLatitude` int(11) NOT NULL,
`constRecord` tinyint(1) NOT NULL DEFAULT 0,
`timez` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


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
`outofrangeID` int(11) NOT NULL,
`analogID` int(11) NOT NULL,
`sensorReading` float DEFAULT NULL,
`timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `outofrangehumid`
--

CREATE TABLE `outofrangehumid` (
`outofrangeID` int(11) NOT NULL,
`humidID` int(11) NOT NULL,
`sensorReading` float NOT NULL,
`timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `outofrangetemp`
--

CREATE TABLE `outofrangetemp` (
`outofrangeID` int(11) NOT NULL,
`tempID` int(11) NOT NULL,
`sensorReading` float NOT NULL,
`timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
`roomID` int(11) NOT NULL,
`room` varchar(20) NOT NULL,
`groupNameID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `sensornames`
--

CREATE TABLE `sensornames` (
`sensorNameID` int(11) NOT NULL,
`createdBy` int(11) DEFAULT NULL,
`sensorName` varchar(20) NOT NULL,
`deviceNameID` int(11) NOT NULL,
`sensorTypeID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `sensortype`
--

CREATE TABLE `sensortype` (
`sensorTypeID` int(11) NOT NULL,
`sensorType` varchar(20) NOT NULL,
`description` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `soil`
--

CREATE TABLE `soil` (
`soilID` int(11) NOT NULL,
`analogID` int(11) NOT NULL,
`sensorNameID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `temp`
--

CREATE TABLE `temp` (
`tempID` int(11) NOT NULL,
`sensorNameID` int(11) DEFAULT NULL,
`tempReading` float NOT NULL,
`highTemp` float NOT NULL DEFAULT 26,
`lowTemp` float NOT NULL DEFAULT 12,
`constRecord` tinyint(1) NOT NULL DEFAULT 0,
`timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
-- Indexes for dumped tables
--

--
-- Indexes for table `analog`
--
ALTER TABLE `analog`
    ADD PRIMARY KEY (`analogID`),
    ADD UNIQUE KEY `sensorNameID` (`sensorNameID`),
    ADD KEY `analog_ibfk_3` (`sensorNameID`);

--
-- Indexes for table `bmp`
--
ALTER TABLE `bmp`
    ADD PRIMARY KEY (`bmpID`),
    ADD UNIQUE KEY `tempID*` (`tempID`),
    ADD UNIQUE KEY `humidID` (`humidID`),
    ADD UNIQUE KEY `latitudeID` (`latitudeID`),
    ADD UNIQUE KEY `cardViewID` (`sensorNameID`);

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
    ADD KEY `UserID` (`userID`),
    ADD KEY `cardColour` (`cardColourID`),
    ADD KEY `cardIcon` (`cardIconID`),
    ADD KEY `cardview_show` (`cardViewID`),
    ADD KEY `cardState` (`cardStateID`),
    ADD KEY `FK_E36636B53BE475E6` (`sensorNameID`);

--
-- Indexes for table `constanalog`
--
ALTER TABLE `constanalog`
    ADD PRIMARY KEY (constRecordID),
    ADD KEY `sensorID` (analogID);

--
-- Indexes for table `consthumid`
--
ALTER TABLE `consthumid`
    ADD PRIMARY KEY (constRecordID),
    ADD KEY `sensorID` (`humidID`);

--
-- Indexes for table `consttemp`
--
ALTER TABLE `consttemp`
    ADD PRIMARY KEY (constRecordID),
    ADD KEY `consttemp_ibfk_1` (tempID);

--
-- Indexes for table `dallas`
--
ALTER TABLE `dallas`
    ADD PRIMARY KEY (`dallasID`),
    ADD UNIQUE KEY `tempID` (`tempID`),
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
    ADD UNIQUE KEY `cardviewID` (`sensorNameID`);

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
    ADD KEY `humid_ibfk_3` (`sensorNameID`);

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
    ADD PRIMARY KEY (outofrangeID),
    ADD KEY `sensorID` (analogID);

--
-- Indexes for table `outofrangehumid`
--
ALTER TABLE `outofrangehumid`
    ADD PRIMARY KEY (outofrangeID),
    ADD KEY `sensorID` (humidID);

--
-- Indexes for table `outofrangetemp`
--
ALTER TABLE `outofrangetemp`
    ADD PRIMARY KEY (`outofrangeID`),
    ADD KEY `outofrangetemp_ibfk_1` (`tempID`);

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
    ADD KEY `sensornames_ibfk_1` (`deviceNameID`),
    ADD KEY `sensornames_ibfk_2` (`createdBy`);

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
    ADD UNIQUE KEY `cardViewID` (`sensorNameID`);

--
-- Indexes for table `temp`
--
ALTER TABLE `temp`
    ADD PRIMARY KEY (`tempID`),
    ADD UNIQUE KEY `sensorNameID` (`sensorNameID`);

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
    MODIFY `analogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=337;

--
-- AUTO_INCREMENT for table `bmp`
--
ALTER TABLE `bmp`
    MODIFY `bmpID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT for table `cardcolour`
--
ALTER TABLE `cardcolour`
    MODIFY `colourID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `cardstate`
--
ALTER TABLE `cardstate`
    MODIFY `cardStateID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `cardview`
--
ALTER TABLE `cardview`
    MODIFY `cardViewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2598;

--
-- AUTO_INCREMENT for table `constanalog`
--
ALTER TABLE `constanalog`
    MODIFY constRecordID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consthumid`
--
ALTER TABLE `consthumid`
    MODIFY constRecordID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consttemp`
--
ALTER TABLE `consttemp`
    MODIFY constRecordID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dallas`
--
ALTER TABLE `dallas`
    MODIFY `dallasID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=267;

--
-- AUTO_INCREMENT for table `devicenames`
--
ALTER TABLE `devicenames`
    MODIFY `deviceNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1111;

--
-- AUTO_INCREMENT for table `dhtsensor`
--
ALTER TABLE `dhtsensor`
    MODIFY `dhtID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=368;

--
-- AUTO_INCREMENT for table `groupname`
--
ALTER TABLE `groupname`
    MODIFY `groupNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT for table `groupnnamemapping`
--
ALTER TABLE `groupnnamemapping`
    MODIFY `groupNameMappingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=320;

--
-- AUTO_INCREMENT for table `humid`
--
ALTER TABLE `humid`
    MODIFY `humidID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=828;

--
-- AUTO_INCREMENT for table `icons`
--
ALTER TABLE `icons`
    MODIFY `iconID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1244;

--
-- AUTO_INCREMENT for table `latitude`
--
ALTER TABLE `latitude`
    MODIFY `latitudeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=298;

--
-- AUTO_INCREMENT for table `outofrangeanalog`
--
ALTER TABLE `outofrangeanalog`
    MODIFY outofrangeID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `outofrangehumid`
--
ALTER TABLE `outofrangehumid`
    MODIFY outofrangeID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `outofrangetemp`
--
ALTER TABLE `outofrangetemp`
    MODIFY `outofrangeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6868;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
    MODIFY `roomID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `sensornames`
--
ALTER TABLE `sensornames`
    MODIFY `sensorNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2339;

--
-- AUTO_INCREMENT for table `sensortype`
--
ALTER TABLE `sensortype`
    MODIFY `sensorTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=317;

--
-- AUTO_INCREMENT for table `soil`
--
ALTER TABLE `soil`
    MODIFY `soilID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- AUTO_INCREMENT for table `temp`
--
ALTER TABLE `temp`
    MODIFY `tempID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1285;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
    MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=276;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `analog`
--
ALTER TABLE `analog`
    ADD CONSTRAINT `FK_A78C95C13BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bmp`
--
ALTER TABLE `bmp`
    ADD CONSTRAINT `bmp_ibfk_1` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `bmp_ibfk_2` FOREIGN KEY (`tempID`) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `bmp_ibfk_3` FOREIGN KEY (`humidID`) REFERENCES `humid` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `bmp_ibfk_4` FOREIGN KEY (`latitudeID`) REFERENCES `latitude` (`latitudeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cardview`
--
ALTER TABLE `cardview`
    ADD CONSTRAINT `FK_E36636B53BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_E36636B53casrdState` FOREIGN KEY (`cardStateID`) REFERENCES `cardstate` (`cardStateID`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_E36636B55FD86D04` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_E36636B5840D9A7A` FOREIGN KEY (`cardIconID`) REFERENCES `icons` (`iconID`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_E36636B5A356FF88` FOREIGN KEY (`cardColourID`) REFERENCES `cardcolour` (`colourID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `constanalog`
--
ALTER TABLE `constanalog`
    ADD CONSTRAINT `constanalog_ibfk_1` FOREIGN KEY (analogID) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `consthumid`
--
ALTER TABLE `consthumid`
    ADD CONSTRAINT `consthumid_ibfk_1` FOREIGN KEY (`humidID`) REFERENCES `humid` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `consttemp`
--
ALTER TABLE `consttemp`
    ADD CONSTRAINT `consttemp_ibfk_1` FOREIGN KEY (tempID) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dallas`
--
ALTER TABLE `dallas`
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
    ADD CONSTRAINT `dhtsensor_ibfk_1` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
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
    ADD CONSTRAINT `FK_8D6EB6E33BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
    ADD CONSTRAINT `outofrangeanalog_ibfk_1` FOREIGN KEY (analogID) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `outofrangehumid`
--
ALTER TABLE `outofrangehumid`
    ADD CONSTRAINT `outofrangehumid_ibfk_1` FOREIGN KEY (humidID) REFERENCES `humid` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `outofrangetemp`
--
ALTER TABLE `outofrangetemp`
    ADD CONSTRAINT `outofrangetemp_ibfk_1` FOREIGN KEY (`tempID`) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
    ADD CONSTRAINT `sensornames_ibfk_1` FOREIGN KEY (`deviceNameID`) REFERENCES `devicenames` (`deviceNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `sensornames_ibfk_2` FOREIGN KEY (`createdBy`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `soil`
--
ALTER TABLE `soil`
    ADD CONSTRAINT `soil_ibfk_1` FOREIGN KEY (`analogID`) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `soil_ibfk_2` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `temp`
--
ALTER TABLE `temp`
    ADD CONSTRAINT `FK_B5385CA3BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
    ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`);


COMMIT;

CREATE USER IF NOT EXISTS HomeAppTest IDENTIFIED BY 'TestPassword123';

GRANT ALL PRIVILEGES ON HomeAppTest.* TO 'HomeAppTest'@'%';

FLUSH PRIVILEGES;
