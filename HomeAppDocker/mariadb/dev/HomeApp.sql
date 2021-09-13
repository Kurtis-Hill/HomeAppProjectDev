-- MariaDB dump 10.19  Distrib 10.6.2-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: HomeApp
-- ------------------------------------------------------
-- Server version	10.6.2-MariaDB-1:10.6.2+maria~focal

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS HomeAppTwo;
CREATE DATABASE IF NOT EXISTS HomeAppTwoTest;

use HomeAppTwo;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Table structure for table `analog`
--

DROP TABLE IF EXISTS `analog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `analog` (
  `analogID` int(11) NOT NULL AUTO_INCREMENT,
  `sensorNameID` int(11) DEFAULT NULL,
  `analogReading` smallint(6) DEFAULT NULL,
  `highAnalog` smallint(6) DEFAULT 1000,
  `lowAnalog` smallint(6) DEFAULT 1000,
  `constRecord` tinyint(4) DEFAULT 0,
  `timez` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`analogID`),
  UNIQUE KEY `sensorNameID` (`sensorNameID`),
  KEY `analog_ibfk_3` (`sensorNameID`),
  CONSTRAINT `FK_A78C95C13BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=345 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bmp`
--

DROP TABLE IF EXISTS `bmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bmp` (
  `bmpID` int(11) NOT NULL AUTO_INCREMENT,
  `tempID` int(11) NOT NULL,
  `humidID` int(11) NOT NULL,
  `latitudeID` int(11) NOT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  PRIMARY KEY (`bmpID`),
  UNIQUE KEY `tempID*` (`tempID`),
  UNIQUE KEY `humidID` (`humidID`),
  UNIQUE KEY `latitudeID` (`latitudeID`),
  UNIQUE KEY `cardViewID` (`sensorNameID`),
  CONSTRAINT `bmp_ibfk_1` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bmp_ibfk_2` FOREIGN KEY (`tempID`) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bmp_ibfk_3` FOREIGN KEY (`humidID`) REFERENCES `humid` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bmp_ibfk_4` FOREIGN KEY (`latitudeID`) REFERENCES `latitude` (`latitudeID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `cardcolour`
--

DROP TABLE IF EXISTS `cardcolour`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cardcolour` (
  `colourID` int(11) NOT NULL AUTO_INCREMENT,
  `colour` varchar(20) NOT NULL,
  `shade` varchar(20) NOT NULL,
  PRIMARY KEY (`colourID`),
  UNIQUE KEY `colour` (`colour`)
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cardcolour`
--

LOCK TABLES `cardcolour` WRITE;
/*!40000 ALTER TABLE `cardcolour` DISABLE KEYS */;
INSERT INTO `cardcolour` VALUES (185,'danger','red'),(186,'success','green'),(187,'warning','Yellow'),(188,'primary','blue');
/*!40000 ALTER TABLE `cardcolour` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cardstate`
--

DROP TABLE IF EXISTS `cardstate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cardstate` (
  `cardStateID` int(11) NOT NULL AUTO_INCREMENT,
  `state` varchar(50) NOT NULL,
  PRIMARY KEY (`cardStateID`)
) ENGINE=InnoDB AUTO_INCREMENT=192 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cardstate`
--

LOCK TABLES `cardstate` WRITE;
/*!40000 ALTER TABLE `cardstate` DISABLE KEYS */;
INSERT INTO `cardstate` VALUES (188,'ON'),(189,'OFF'),(190,'DEVICE_ONLY'),(191,'ROOM_ONLY');
/*!40000 ALTER TABLE `cardstate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cardview`
--

DROP TABLE IF EXISTS `cardview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cardview` (
  `cardViewID` int(11) NOT NULL AUTO_INCREMENT,
  `sensorNameID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `cardIconID` int(11) DEFAULT NULL,
  `cardColourID` int(11) DEFAULT NULL,
  `cardStateID` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`cardViewID`),
  KEY `UserID` (`userID`),
  KEY `cardColour` (`cardColourID`),
  KEY `cardIcon` (`cardIconID`),
  KEY `cardview_show` (`cardViewID`),
  KEY `cardState` (`cardStateID`),
  KEY `FK_E36636B53BE475E6` (`sensorNameID`),
  CONSTRAINT `FK_E36636B53BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON UPDATE CASCADE,
  CONSTRAINT `FK_E36636B53casrdState` FOREIGN KEY (`cardStateID`) REFERENCES `cardstate` (`cardStateID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_E36636B55FD86D04` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_E36636B5840D9A7A` FOREIGN KEY (`cardIconID`) REFERENCES `icons` (`iconID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_E36636B5A356FF88` FOREIGN KEY (`cardColourID`) REFERENCES `cardcolour` (`colourID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2694 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `constanalog`
--

DROP TABLE IF EXISTS `constanalog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `constanalog` (
  `constRecordID` int(11) NOT NULL AUTO_INCREMENT,
  `sensorReadingTypeID` int(11) NOT NULL,
  `sensorReading` float NOT NULL,
  `timez` date NOT NULL,
  PRIMARY KEY (`constRecordID`),
  KEY `sensorID` (`sensorReadingTypeID`),
  CONSTRAINT `constanalog_ibfk_1` FOREIGN KEY (`sensorReadingTypeID`) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `consthumid`
--

DROP TABLE IF EXISTS `consthumid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consthumid` (
  `constRecordID` int(11) NOT NULL AUTO_INCREMENT,
  `sensorID` int(11) NOT NULL,
  `sensorReadingTypeID` float NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`constRecordID`),
  KEY `sensorID` (`sensorID`),
  CONSTRAINT `consthumid_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `humid` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `consttemp`
--

DROP TABLE IF EXISTS `consttemp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consttemp` (
  `constRecordID` int(11) NOT NULL AUTO_INCREMENT,
  `sensorReadingTypeID` int(11) NOT NULL,
  `sensorReading` float NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`constRecordID`),
  KEY `consttemp_ibfk_1` (`sensorReadingTypeID`),
  CONSTRAINT `consttemp_ibfk_1` FOREIGN KEY (`sensorReadingTypeID`) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dallas`
--

DROP TABLE IF EXISTS `dallas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dallas` (
  `dallasID` int(11) NOT NULL AUTO_INCREMENT,
  `tempID` int(11) NOT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  PRIMARY KEY (`dallasID`),
  UNIQUE KEY `tempID` (`tempID`),
  KEY `sensorNameID` (`sensorNameID`),
  CONSTRAINT `dallas_ibfk_2` FOREIGN KEY (`tempID`) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `dallas_ibfk_3` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=275 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devicenames`
--

DROP TABLE IF EXISTS `devicenames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devicenames` (
  `deviceNameID` int(11) NOT NULL AUTO_INCREMENT,
  `deviceName` varchar(20) NOT NULL,
  `password` longtext NOT NULL,
  `groupNameID` int(11) NOT NULL,
  `roomID` int(11) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:json)',
  PRIMARY KEY (`deviceNameID`),
  KEY `groupNameID` (`groupNameID`),
  KEY `roomID` (`roomID`),
  KEY `createdBy` (`createdBy`),
  CONSTRAINT `devicenames_ibfk_1` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `devicenames_ibfk_2` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `devicenames_ibfk_3` FOREIGN KEY (`createdBy`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1120 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `dhtsensor`
--

DROP TABLE IF EXISTS `dhtsensor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dhtsensor` (
  `dhtID` int(11) NOT NULL AUTO_INCREMENT,
  `tempID` int(11) NOT NULL,
  `humidID` int(11) NOT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  PRIMARY KEY (`dhtID`),
  UNIQUE KEY `tempID` (`tempID`),
  UNIQUE KEY `humidID` (`humidID`),
  UNIQUE KEY `cardviewID` (`sensorNameID`),
  CONSTRAINT `dhtsensor_ibfk_1` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `dhtsensor_ibfk_2` FOREIGN KEY (`humidID`) REFERENCES `humid` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `dhtsensor_ibfk_3` FOREIGN KEY (`tempID`) REFERENCES `temp` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=376 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groupname`
--

DROP TABLE IF EXISTS `groupname`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupname` (
  `groupNameID` int(11) NOT NULL AUTO_INCREMENT,
  `groupName` varchar(50) NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`groupNameID`),
  UNIQUE KEY `groupName` (`groupName`)
) ENGINE=InnoDB AUTO_INCREMENT=286 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupname`
--

LOCK TABLES `groupname` WRITE;
/*!40000 ALTER TABLE `groupname` DISABLE KEYS */;
INSERT INTO `groupname` VALUES (1,'admin-group','2021-07-15 17:19:32');
/*!40000 ALTER TABLE `groupname` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groupnnamemapping`
--

DROP TABLE IF EXISTS `groupnnamemapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupnnamemapping` (
  `groupNameMappingID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `groupNameID` int(11) NOT NULL,
  PRIMARY KEY (`groupNameMappingID`),
  KEY `groupNameID` (`groupNameID`),
  KEY `userID` (`userID`,`groupNameID`),
  CONSTRAINT `groupnnamemapping_ibfk_1` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `groupnnamemapping_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=327 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupnnamemapping`
--

LOCK TABLES `groupnnamemapping` WRITE;
/*!40000 ALTER TABLE `groupnnamemapping` DISABLE KEYS */;
INSERT INTO `groupnnamemapping` VALUES (1,1,1);
/*!40000 ALTER TABLE `groupnnamemapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `humid`
--

DROP TABLE IF EXISTS `humid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `humid` (
  `humidID` int(11) NOT NULL AUTO_INCREMENT,
  `sensorNameID` int(11) DEFAULT NULL,
  `humidReading` float NOT NULL,
  `highHumid` float NOT NULL DEFAULT 70,
  `lowHumid` float NOT NULL DEFAULT 15,
  `constRecord` tinyint(1) NOT NULL DEFAULT 0,
  `timez` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`humidID`),
  UNIQUE KEY `sensorNameID` (`sensorNameID`),
  KEY `humid_ibfk_3` (`sensorNameID`),
  CONSTRAINT `FK_8D6EB6E33BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=844 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `icons`
--

DROP TABLE IF EXISTS `icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `icons` (
  `iconID` int(11) NOT NULL AUTO_INCREMENT,
  `iconName` varchar(20) DEFAULT NULL,
  `description` varchar(20) NOT NULL,
  PRIMARY KEY (`iconID`),
  UNIQUE KEY `iconName_2` (`iconName`)
) ENGINE=InnoDB AUTO_INCREMENT=1271 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `icons`
--

LOCK TABLES `icons` WRITE;
/*!40000 ALTER TABLE `icons` DISABLE KEYS */;
INSERT INTO `icons` VALUES (1244,'air-freshener','Christmas tree'),(1245,'warehouse','warehouse'),(1246,'archway','archway'),(1247,'baby','baby'),(1248,'bath','bath and shower'),(1249,'bed','bed'),(1250,'cannabis','cannabis leaf'),(1251,'camera','camera'),(1252,'carrot','carrot'),(1253,'campground','tent'),(1254,'chart-pie','graph'),(1255,'crosshairs','crosshair'),(1256,'database','symbol'),(1257,'dog','doggie'),(1258,'dove','bird'),(1259,'download','download logo'),(1260,'fish','fishys'),(1261,'flask','science beaker'),(1262,'fort-awesome','castle'),(1263,'mobile-alt','mobile phone'),(1264,'php','php logo'),(1265,'Playstation','ps1 logo'),(1266,'power-off','shutdown logo'),(1267,'raspberry-pi','pi logo'),(1268,'xbox','xbox logo'),(1269,'skull-crossbones','skull and bones'),(1270,'smoking','smoking');
/*!40000 ALTER TABLE `icons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `latitude`
--

DROP TABLE IF EXISTS `latitude`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `latitude` (
  `latitudeID` int(11) NOT NULL AUTO_INCREMENT,
  `sensorNameID` int(11) NOT NULL,
  `deviceNameID` int(11) DEFAULT NULL,
  `latitude` int(11) NOT NULL,
  `lowLatitude` int(11) NOT NULL,
  `highLatitude` int(11) NOT NULL,
  `constRecord` tinyint(1) NOT NULL DEFAULT 0,
  `timez` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`latitudeID`),
  UNIQUE KEY `sensorNameID` (`sensorNameID`),
  UNIQUE KEY `deviceNameID` (`deviceNameID`),
  CONSTRAINT `latitude_ibfk_1` FOREIGN KEY (`deviceNameID`) REFERENCES `devicenames` (`deviceNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `latitude_ibfk_4` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=306 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_versions`
--

LOCK TABLES `migration_versions` WRITE;
/*!40000 ALTER TABLE `migration_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outofrangeanalog`
--

DROP TABLE IF EXISTS `outofrangeanalog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outofrangeanalog` (
  `outofrangeID` int(11) NOT NULL AUTO_INCREMENT,
  `analogID` int(11) NOT NULL,
  `sensorReading` float DEFAULT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`outofrangeID`),
  KEY `sensorID` (`analogID`),
  CONSTRAINT `outofrangeanalog_ibfk_1` FOREIGN KEY (`analogID`) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `outofrangehumid`
--

DROP TABLE IF EXISTS `outofrangehumid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outofrangehumid` (
  `outofrangeID` int(11) NOT NULL AUTO_INCREMENT,
  `humidID` int(11) NOT NULL,
  `sensorReading` float NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`outofrangeID`),
  KEY `sensorID` (`humidID`),
  CONSTRAINT `outofrangehumid_ibfk_1` FOREIGN KEY (`humidID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `outofrangetemp`
--

DROP TABLE IF EXISTS `outofrangetemp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outofrangetemp` (
  `outofrangeID` int(11) NOT NULL AUTO_INCREMENT,
  `tempID` int(11) DEFAULT NULL,
  `sensorReading` int(11) DEFAULT NULL,
  `timez` datetime DEFAULT NULL,
  PRIMARY KEY (`outofrangeID`),
  KEY `outofrangetemp_temp_tempID_fk` (`tempID`),
  CONSTRAINT `outofrangetemp_temp_tempID_fk` FOREIGN KEY (`tempID`) REFERENCES `temp` (`tempID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `refresh_tokens`
--

DROP TABLE IF EXISTS `refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refresh_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refresh_token` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valid` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9BACE7E1C74F2195` (`refresh_token`)
) ENGINE=InnoDB AUTO_INCREMENT=6881 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `room`
--

DROP TABLE IF EXISTS `room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room` (
  `roomID` int(11) NOT NULL AUTO_INCREMENT,
  `room` varchar(20) NOT NULL,
  `groupNameID` int(11) DEFAULT NULL,
  PRIMARY KEY (`roomID`),
  KEY `GroupName` (`groupNameID`),
  CONSTRAINT `FK_729F519B2D8C0469` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`)
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sensornames`
--

DROP TABLE IF EXISTS `sensornames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensornames` (
  `sensorNameID` int(11) NOT NULL AUTO_INCREMENT,
  `createdBy` int(11) DEFAULT NULL,
  `sensorName` varchar(20) NOT NULL,
  `deviceNameID` int(11) NOT NULL,
  `sensorTypeID` int(11) DEFAULT NULL,
  PRIMARY KEY (`sensorNameID`),
  KEY `SensorType` (`sensorTypeID`),
  KEY `sensornames_ibfk_1` (`deviceNameID`),
  KEY `sensornames_ibfk_2` (`createdBy`),
  CONSTRAINT `FK_82F2A8F46B4A071A` FOREIGN KEY (`sensorTypeID`) REFERENCES `sensortype` (`sensorTypeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sensornames_ibfk_1` FOREIGN KEY (`deviceNameID`) REFERENCES `devicenames` (`deviceNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sensornames_ibfk_2` FOREIGN KEY (`createdBy`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2387 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sensortype`
--

DROP TABLE IF EXISTS `sensortype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensortype` (
  `sensorTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `sensorType` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`sensorTypeID`),
  UNIQUE KEY `sensorType` (`sensorType`)
) ENGINE=InnoDB AUTO_INCREMENT=321 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensortype`
--

LOCK TABLES `sensortype` WRITE;
/*!40000 ALTER TABLE `sensortype` DISABLE KEYS */;
INSERT INTO `sensortype` VALUES (317,'Dht','Temperature and Humidity Sensor'),(318,'Dallas','Water Proof Temperature Sensor'),(319,'Soil','Soil Moisture Sensor'),(320,'Bmp','Weather Station Sensor');
/*!40000 ALTER TABLE `sensortype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `soil`
--

DROP TABLE IF EXISTS `soil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soil` (
  `soilID` int(11) NOT NULL AUTO_INCREMENT,
  `analogID` int(11) NOT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  PRIMARY KEY (`soilID`),
  UNIQUE KEY `analogID` (`analogID`),
  UNIQUE KEY `cardViewID` (`sensorNameID`),
  CONSTRAINT `soil_ibfk_1` FOREIGN KEY (`analogID`) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `soil_ibfk_2` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=263 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temp`
--

DROP TABLE IF EXISTS `temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp` (
  `tempID` int(11) NOT NULL AUTO_INCREMENT,
  `sensorNameID` int(11) DEFAULT NULL,
  `tempReading` float NOT NULL,
  `highTemp` float NOT NULL DEFAULT 26,
  `lowTemp` float NOT NULL DEFAULT 12,
  `constRecord` tinyint(1) NOT NULL DEFAULT 0,
  `timez` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tempID`),
  UNIQUE KEY `sensorNameID` (`sensorNameID`),
  CONSTRAINT `FK_B5385CA3BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1309 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(20) NOT NULL,
  `lastName` varchar(20) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:json)',
  `profilePic` varchar(100) DEFAULT '/assets/pictures/guest.jpg',
  `password` longtext NOT NULL,
  `salt` longtext DEFAULT NULL,
  `groupNameID` int(11) DEFAULT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`userID`),
  KEY `GroupName` (`groupNameID`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`)
) ENGINE=InnoDB AUTO_INCREMENT=280 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','admin','admin','[\"ROLE_ADMIN\"]','/assets/pictures/guest.jpg','$argon2id$v=19$m=65536,t=4,p=1$7zx+pasSn547DYfLgO9MuQ$ACTjDqrmJDgB9KfoZUOpESDZn/071R/Bmfju9o+R1Zw',NULL,1,'2021-07-15 17:19:32');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-09-12 17:52:23
