CREATE DATABASE IF NOT EXISTS HomeApp;
CREATE DATABASE IF NOT EXISTS HomeAppTest;

use HomeApp;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `HomeApp`
--

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
-- Dumping data for table `analog`
--

LOCK TABLES `analog` WRITE;
/*!40000 ALTER TABLE `analog` DISABLE KEYS */;
INSERT INTO `analog` VALUES (337,2341,10,9999,1111,0,'2021-07-15 17:19:33'),(338,2347,10,9999,1111,0,'2021-07-15 17:19:33'),(339,2353,10,9999,1111,0,'2021-07-15 17:19:33'),(340,2359,10,9999,1111,0,'2021-07-15 17:19:33'),(341,2365,10,9999,1111,0,'2021-07-15 17:19:33'),(342,2371,10,9999,1111,0,'2021-07-15 17:19:33'),(343,2377,10,9999,1111,0,'2021-07-15 17:19:33'),(344,2383,10,9999,1111,0,'2021-07-15 17:19:33');
/*!40000 ALTER TABLE `analog` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `bmp`
--

LOCK TABLES `bmp` WRITE;
/*!40000 ALTER TABLE `bmp` DISABLE KEYS */;
INSERT INTO `bmp` VALUES (242,1287,829,298,2342),(243,1290,831,299,2349),(244,1293,833,300,2354),(245,1296,835,301,2361),(246,1299,837,302,2366),(247,1302,839,303,2373),(248,1305,841,304,2378),(249,1308,843,305,2385);
/*!40000 ALTER TABLE `bmp` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `cardview`
--

LOCK TABLES `cardview` WRITE;
/*!40000 ALTER TABLE `cardview` DISABLE KEYS */;
INSERT INTO `cardview` VALUES (2598,2339,276,1251,185,188),(2599,2339,277,1265,187,188),(2600,2340,276,1254,188,188),(2601,2340,277,1258,188,188),(2602,2341,276,1244,188,188),(2603,2341,277,1249,188,188),(2604,2342,276,1267,185,188),(2605,2342,277,1258,187,188),(2606,2343,276,1255,185,188),(2607,2343,277,1246,186,188),(2608,2344,276,1262,187,189),(2609,2344,277,1245,187,189),(2610,2345,276,1260,185,188),(2611,2345,277,1248,185,188),(2612,2346,276,1268,185,189),(2613,2346,277,1263,186,189),(2614,2347,276,1255,186,188),(2615,2347,277,1256,188,188),(2616,2348,276,1252,188,189),(2617,2348,277,1252,186,189),(2618,2349,276,1253,187,188),(2619,2349,277,1267,186,188),(2620,2350,276,1246,187,189),(2621,2350,277,1245,188,189),(2622,2351,276,1266,186,188),(2623,2351,277,1267,186,188),(2624,2352,276,1247,186,188),(2625,2352,277,1265,186,188),(2626,2353,276,1250,185,188),(2627,2353,277,1256,186,188),(2628,2354,276,1244,188,188),(2629,2354,277,1270,185,188),(2630,2355,276,1244,187,188),(2631,2355,277,1260,187,188),(2632,2356,276,1268,185,189),(2633,2356,277,1256,188,189),(2634,2357,276,1245,187,188),(2635,2357,277,1251,185,188),(2636,2358,276,1251,187,189),(2637,2358,277,1269,186,189),(2638,2359,276,1264,185,188),(2639,2359,277,1267,186,188),(2640,2360,276,1269,188,189),(2641,2360,277,1269,187,189),(2642,2361,276,1263,186,188),(2643,2361,277,1250,188,188),(2644,2362,276,1254,185,189),(2645,2362,277,1266,188,189),(2646,2363,276,1266,186,188),(2647,2363,277,1252,188,188),(2648,2364,276,1246,185,188),(2649,2364,277,1269,185,188),(2650,2365,276,1246,186,188),(2651,2365,277,1247,187,188),(2652,2366,276,1251,188,188),(2653,2366,277,1263,185,188),(2654,2367,276,1263,186,188),(2655,2367,277,1251,188,188),(2656,2368,276,1249,185,189),(2657,2368,277,1251,187,189),(2658,2369,276,1254,188,188),(2659,2369,277,1254,188,188),(2660,2370,276,1251,188,189),(2661,2370,277,1253,188,189),(2662,2371,276,1256,188,188),(2663,2371,277,1260,186,188),(2664,2372,276,1253,188,189),(2665,2372,277,1259,188,189),(2666,2373,276,1244,185,188),(2667,2373,277,1270,188,188),(2668,2374,276,1252,187,189),(2669,2374,277,1257,188,189),(2670,2375,276,1244,188,188),(2671,2375,277,1263,188,188),(2672,2376,276,1265,186,188),(2673,2376,277,1246,188,188),(2674,2377,276,1250,188,188),(2675,2377,277,1268,187,188),(2676,2378,276,1248,186,188),(2677,2378,277,1262,188,188),(2678,2379,276,1257,188,188),(2679,2379,277,1250,186,188),(2680,2380,276,1247,187,189),(2681,2380,277,1257,187,189),(2682,2381,276,1261,188,188),(2683,2381,277,1259,188,188),(2684,2382,276,1257,186,189),(2685,2382,277,1251,187,189),(2686,2383,276,1261,185,188),(2687,2383,277,1255,185,188),(2688,2384,276,1245,188,189),(2689,2384,277,1263,186,189),(2690,2385,276,1245,188,188),(2691,2385,277,1253,186,188),(2692,2386,276,1246,187,189),(2693,2386,277,1250,188,189);
/*!40000 ALTER TABLE `cardview` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `constanalog`
--

LOCK TABLES `constanalog` WRITE;
/*!40000 ALTER TABLE `constanalog` DISABLE KEYS */;
/*!40000 ALTER TABLE `constanalog` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `consthumid`
--

LOCK TABLES `consthumid` WRITE;
/*!40000 ALTER TABLE `consthumid` DISABLE KEYS */;
/*!40000 ALTER TABLE `consthumid` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `consttemp`
--

LOCK TABLES `consttemp` WRITE;
/*!40000 ALTER TABLE `consttemp` DISABLE KEYS */;
INSERT INTO `consttemp` VALUES (1,1289,10,'2021-09-12 16:10:39'),(2,1289,10,'2021-09-12 16:10:54'),(3,1289,10,'2021-09-12 16:19:33'),(4,1289,10,'2021-09-12 16:38:07');
/*!40000 ALTER TABLE `consttemp` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `dallas`
--

LOCK TABLES `dallas` WRITE;
/*!40000 ALTER TABLE `dallas` DISABLE KEYS */;
INSERT INTO `dallas` VALUES (267,1286,2340),(268,1289,2345),(269,1292,2352),(270,1295,2357),(271,1298,2364),(272,1301,2369),(273,1304,2376),(274,1307,2381);
/*!40000 ALTER TABLE `dallas` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `devicenames`
--

LOCK TABLES `devicenames` WRITE;
/*!40000 ALTER TABLE `devicenames` DISABLE KEYS */;
INSERT INTO `devicenames` VALUES (1111,'aaa','$argon2id$v=19$m=65536,t=4,p=1$oQAQODnFdJU0csQdqEgA0A$w3X63UKCGgCumLLFVUD6LWXX1yuXJd8kz6itN6/gb2E',282,160,276,'[\"ROLE_DEVICE\"]'),(1112,'aar','$argon2id$v=19$m=65536,t=4,p=1$897z9QAspeze1aU7dnrqEw$oodV4i7pHHB+jQYBEpnHwQhZFtJMu3S28RP6OMJvSaM',282,161,276,'[\"ROLE_DEVICE\"]'),(1113,'arr','$argon2id$v=19$m=65536,t=4,p=1$OJwEooV6Xr9A4W4TU8DstA$weEH3XES/7ceC1/DPLHezp6R/HpUqBbsB0C39IOA4DU',283,161,276,'[\"ROLE_DEVICE\"]'),(1114,'ara','$argon2id$v=19$m=65536,t=4,p=1$a30Ng9bMtZ4WxmZLbwDrKA$AXpQxJqlQmJ43bZILVX8AF5HBdoQMdUkBL/C8/MHAHw',283,160,276,'[\"ROLE_DEVICE\"]'),(1115,'rrr','$argon2id$v=19$m=65536,t=4,p=1$eTeCOs7fl1doiwPTOo6+Qg$8sOQU5beoKVkC+C0Mq2N6Px6EZUE7CxQ5HqeGwRbb7M',283,161,277,'[\"ROLE_DEVICE\"]'),(1116,'rra','$argon2id$v=19$m=65536,t=4,p=1$l9TXLHf9hipgJBVpJ9qYpA$um7I3ooALqaxtr0psdrY72dWDcGk/GybLe+UDi6cj6s',283,160,277,'[\"ROLE_DEVICE\"]'),(1117,'raa','$argon2id$v=19$m=65536,t=4,p=1$rH+W+7kZ+w6iNG1jnyGVYQ$F1fageMGM9BYCDjl5LHePHWzL93zUmQ1ylkr6YOErTg',282,160,277,'[\"ROLE_DEVICE\"]'),(1118,'rar','$argon2id$v=19$m=65536,t=4,p=1$NePiSFQ4GUmEk9UVvgOqKw$zPEIydJd0BoO78bKf0GEhp+TFSMuzh7pgOsPg0EpIPU',282,161,277,'[\"ROLE_DEVICE\"]'),(1119,'apiLoginTest','$argon2id$v=19$m=65536,t=4,p=1$FN/N6QNP0J+iXzqBr9i8fQ$P+Lfu9iMkEbJXnjgix2No7XEZQDLp3qg2SvNdnn4Aao',282,160,276,'[\"ROLE_DEVICE\"]');
/*!40000 ALTER TABLE `devicenames` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `dhtsensor`
--

LOCK TABLES `dhtsensor` WRITE;
/*!40000 ALTER TABLE `dhtsensor` DISABLE KEYS */;
INSERT INTO `dhtsensor` VALUES (368,1285,828,2339),(369,1288,830,2343),(370,1291,832,2351),(371,1294,834,2355),(372,1297,836,2363),(373,1300,838,2367),(374,1303,840,2375),(375,1306,842,2379);
/*!40000 ALTER TABLE `dhtsensor` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `humid`
--

LOCK TABLES `humid` WRITE;
/*!40000 ALTER TABLE `humid` DISABLE KEYS */;
INSERT INTO `humid` VALUES (828,2339,10,80,10,0,'2021-07-15 17:19:33'),(829,2342,10,80,10,0,'2021-07-15 17:19:33'),(830,2343,10,80,10,0,'2021-07-15 17:19:33'),(831,2349,10,80,10,0,'2021-07-15 17:19:33'),(832,2351,10,80,10,0,'2021-07-15 17:19:33'),(833,2354,10,80,10,0,'2021-07-15 17:19:33'),(834,2355,10,80,10,0,'2021-07-15 17:19:33'),(835,2361,10,80,10,0,'2021-07-15 17:19:33'),(836,2363,10,80,10,0,'2021-07-15 17:19:33'),(837,2366,10,80,10,0,'2021-07-15 17:19:33'),(838,2367,10,80,10,0,'2021-07-15 17:19:33'),(839,2373,10,80,10,0,'2021-07-15 17:19:33'),(840,2375,10,80,10,0,'2021-07-15 17:19:33'),(841,2378,10,80,10,0,'2021-07-15 17:19:33'),(842,2379,10,80,10,0,'2021-07-15 17:19:33'),(843,2385,10,80,10,0,'2021-07-15 17:19:33');
/*!40000 ALTER TABLE `humid` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `latitude`
--

LOCK TABLES `latitude` WRITE;
/*!40000 ALTER TABLE `latitude` DISABLE KEYS */;
INSERT INTO `latitude` VALUES (298,2342,NULL,10,58,66,0,'2021-07-15'),(299,2349,NULL,10,58,66,0,'2021-07-15'),(300,2354,NULL,10,58,66,0,'2021-07-15'),(301,2361,NULL,10,58,66,0,'2021-07-15'),(302,2366,NULL,10,58,66,0,'2021-07-15'),(303,2373,NULL,10,58,66,0,'2021-07-15'),(304,2378,NULL,10,58,66,0,'2021-07-15'),(305,2385,NULL,10,58,66,0,'2021-07-15');
/*!40000 ALTER TABLE `latitude` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `outofrangeanalog`
--

LOCK TABLES `outofrangeanalog` WRITE;
/*!40000 ALTER TABLE `outofrangeanalog` DISABLE KEYS */;
/*!40000 ALTER TABLE `outofrangeanalog` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `outofrangehumid`
--

LOCK TABLES `outofrangehumid` WRITE;
/*!40000 ALTER TABLE `outofrangehumid` DISABLE KEYS */;
/*!40000 ALTER TABLE `outofrangehumid` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `outofrangetemp`
--

LOCK TABLES `outofrangetemp` WRITE;
/*!40000 ALTER TABLE `outofrangetemp` DISABLE KEYS */;
INSERT INTO `outofrangetemp` VALUES (4,1289,10,'2021-09-12 16:10:54'),(5,1289,10,'2021-09-12 16:19:33'),(6,1289,10,'2021-09-12 16:38:07'),(7,1289,10,'2021-09-12 16:53:29'),(8,1289,10,'2021-09-12 16:53:41'),(9,1289,10,'2021-09-12 16:53:42'),(10,1289,10,'2021-09-12 16:53:42'),(11,1289,10,'2021-09-12 16:53:43'),(12,1289,10,'2021-09-12 16:54:27'),(13,1289,10,'2021-09-12 16:54:27'),(14,1289,10,'2021-09-12 16:54:27'),(15,1289,10,'2021-09-12 16:54:27'),(16,1289,10,'2021-09-12 16:55:58');
/*!40000 ALTER TABLE `outofrangetemp` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `refresh_tokens`
--

LOCK TABLES `refresh_tokens` WRITE;
/*!40000 ALTER TABLE `refresh_tokens` DISABLE KEYS */;
INSERT INTO `refresh_tokens` VALUES (6868,'3f139d75a8326e1a167fda4d8b94797765c13f6331767dcab2259c76b6a5bfb71db68a28b05f193929e3e644ef98cdb130f1748813b9dfdd7ba664cde6ab8ad8','admin-user@gmail.com','2021-07-15 21:19:41'),(6869,'e52b2e492a7d4c2411ff36b0685bddb695e4375b69e218c99b5b393c5560ef7f34adce0b4ec54c1ba02fcd4c2ce443bb25d66acbb31d0bed18f39d3c4d010f97','admin-user@gmail.com','2021-08-19 21:19:16'),(6870,'bdfe53fa39222040bc28779f225bfb172a262257f14bf7dd5963603461c4eb1e838139973440046484f160142da70974d488997012eb1f9d45e37cc14010f1ba','admin-user@gmail.com','2021-08-19 21:38:57'),(6871,'217740bf472e8479f6abe801ea27a869f8434e5b11ca849fb1f7997729e9ff4aaf06e07787c603357e5faed34e2c0499cd5ea792c820b51cc4a58fb2efecd844','admin-user@gmail.com','2021-08-19 21:47:59'),(6872,'7d8c7e0121edeb1148dee410aee0225b8d0b9695bc908c2e4ecf5b9446226e7533b50356e69918eda53c5e8684499689fe7eead5d38a238b339d11fbe89fd599','admin-user@gmail.com','2021-08-25 20:02:16'),(6873,'61c7e4c211590330698af092763cc211c9e04ac2dd337ac148e0fc175ce64ed28be043a87ff8b8f48768fced13e00e6345f974ca3b63d05610d98ec87d334277','admin-user@gmail.com','2021-09-02 19:34:01'),(6874,'0a10374ee92bd71816ad3139076564f0481498b1dbfaa4dac235fdbf52039473164ecfe2cee0a05a2889055103c57150232976dffc22b5e8972aba5b24f2a0c0','aar','2021-09-02 19:43:11'),(6875,'97e997468937469dff530275789f2f407a3974848722cfd827aa1786fda8f94585b459fde2604ab398a4c7d1491634f88198ea8c43923a7e8b61363fba15998c','aar','2021-09-03 00:38:31'),(6876,'88bd4d4b211b0d966a62982810a30df9f7b34a52b49bc69ce7c633865cbfc6ee104aa26533cb4f311014b3c6e00be66d594b642edee6b06110b94354548a80f1','admin-user@gmail.com','2021-09-03 01:46:31'),(6877,'39567304e8501496399810536b053e7542ef07056d86b44b6c9d042df8ca39731eb81c87bd8f638782f035ca45fc1195f25a7db25658f6b5fa9997e5bf59cb18','admin-user@gmail.com','2021-09-10 16:06:21'),(6878,'0d798c706b04350dc222bc2f335e0a2f481564b884021b6a84aa9087562e1dac98811c460f9497b566c444a2d47a99c68c8dbf84b65d311024c9330c24efdcdf','aar','2021-09-10 21:19:47'),(6879,'3b6fc3b94fa28dcca6e4686419807bef07cc6c790d10f310a10afb7f1816fea45e7657bb819f3eb1a9794956b51e7f61a771328bbdd6920f1ed1cd7df3114b48','aar','2021-09-12 16:36:25'),(6880,'681167b9ff80befcab47ecb0f30493cfe900dcdb02418b373d42a9a3bba3930ef6f474c8ace1d29ab1ab75b2cc8f1b2b4250c39ab8ace72ebd644aeb48e54605','aar','2021-09-12 20:55:55');
/*!40000 ALTER TABLE `refresh_tokens` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `room`
--

LOCK TABLES `room` WRITE;
/*!40000 ALTER TABLE `room` DISABLE KEYS */;
INSERT INTO `room` VALUES (160,'LivingRoom',282),(161,'BedRoom',283);
/*!40000 ALTER TABLE `room` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `sensornames`
--

LOCK TABLES `sensornames` WRITE;
/*!40000 ALTER TABLE `sensornames` DISABLE KEYS */;
INSERT INTO `sensornames` VALUES (2339,276,'Dht0',1111,317),(2340,276,'Dallas0',1111,318),(2341,276,'Soil0',1111,319),(2342,276,'Bmp0',1111,320),(2343,276,'Dht1',1112,317),(2344,277,'Dht11',1112,317),(2345,276,'Dallas1',1112,318),(2346,277,'Dallas11',1112,318),(2347,276,'Soil1',1112,319),(2348,277,'Soil11',1112,319),(2349,276,'Bmp1',1112,320),(2350,277,'Bmp11',1112,320),(2351,276,'Dht2',1113,317),(2352,276,'Dallas2',1113,318),(2353,276,'Soil2',1113,319),(2354,276,'Bmp2',1113,320),(2355,276,'Dht3',1113,317),(2356,277,'Dht13',1113,317),(2357,276,'Dallas3',1113,318),(2358,277,'Dallas13',1113,318),(2359,276,'Soil3',1113,319),(2360,277,'Soil13',1113,319),(2361,276,'Bmp3',1113,320),(2362,277,'Bmp13',1113,320),(2363,276,'Dht4',1115,317),(2364,276,'Dallas4',1115,318),(2365,276,'Soil4',1115,319),(2366,276,'Bmp4',1115,320),(2367,276,'Dht5',1116,317),(2368,277,'Dht15',1116,317),(2369,276,'Dallas5',1116,318),(2370,277,'Dallas15',1116,318),(2371,276,'Soil5',1116,319),(2372,277,'Soil15',1116,319),(2373,276,'Bmp5',1116,320),(2374,277,'Bmp15',1116,320),(2375,276,'Dht6',1117,317),(2376,276,'Dallas6',1117,318),(2377,276,'Soil6',1117,319),(2378,276,'Bmp6',1117,320),(2379,276,'Dht7',1118,317),(2380,277,'Dht17',1118,317),(2381,276,'Dallas7',1118,318),(2382,277,'Dallas17',1118,318),(2383,276,'Soil7',1118,319),(2384,277,'Soil17',1118,319),(2385,276,'Bmp7',1118,320),(2386,277,'Bmp17',1118,320);
/*!40000 ALTER TABLE `sensornames` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `soil`
--

LOCK TABLES `soil` WRITE;
/*!40000 ALTER TABLE `soil` DISABLE KEYS */;
INSERT INTO `soil` VALUES (255,337,2341),(256,338,2347),(257,339,2353),(258,340,2359),(259,341,2365),(260,342,2371),(261,343,2377),(262,344,2383);
/*!40000 ALTER TABLE `soil` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `temp`
--

LOCK TABLES `temp` WRITE;
/*!40000 ALTER TABLE `temp` DISABLE KEYS */;
INSERT INTO `temp` VALUES (1285,2339,10,52,10,0,'2021-07-15 17:19:33'),(1286,2340,10,52,10,0,'2021-07-15 17:19:33'),(1287,2342,10,50,10,0,'2021-07-15 17:19:33'),(1288,2343,10,50,10,0,'2021-07-15 17:19:33'),(1289,2345,10,50,10,0,'2021-07-15 17:19:33'),(1290,2349,10,50,10,0,'2021-07-15 17:19:33'),(1291,2351,10,50,10,0,'2021-07-15 17:19:33'),(1292,2352,10,50,10,0,'2021-07-15 17:19:33'),(1293,2354,10,50,10,0,'2021-07-15 17:19:33'),(1294,2355,10,50,10,0,'2021-07-15 17:19:33'),(1295,2357,10,50,10,0,'2021-07-15 17:19:33'),(1296,2361,10,50,10,0,'2021-07-15 17:19:33'),(1297,2363,10,50,10,0,'2021-07-15 17:19:33'),(1298,2364,10,50,10,0,'2021-07-15 17:19:33'),(1299,2366,10,50,10,0,'2021-07-15 17:19:33'),(1300,2367,10,50,10,0,'2021-07-15 17:19:33'),(1301,2369,10,50,10,0,'2021-07-15 17:19:33'),(1302,2373,10,50,10,0,'2021-07-15 17:19:33'),(1303,2375,10,50,10,0,'2021-07-15 17:19:33'),(1304,2376,10,50,10,0,'2021-07-15 17:19:33'),(1305,2378,10,50,10,0,'2021-07-15 17:19:33'),(1306,2379,10,50,10,0,'2021-07-15 17:19:33'),(1307,2381,10,50,10,0,'2021-07-15 17:19:33'),(1308,2385,10,50,10,0,'2021-07-15 17:19:33');
/*!40000 ALTER TABLE `temp` ENABLE KEYS */;
UNLOCK TABLES;

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
INSERT INTO `user` VALUES (1,'admin','admin','admin','[\"ROLE_ADMIN\"]','/assets/pictures/guest.jpg','$argon2id$v=19$m=65536,t=4,p=1$7zx+pasSn547DYfLgO9MuQ$ACTjDqrmJDgB9KfoZUOpESDZn/071R/Bmfju9o+R1Zw',NULL,282,'2021-07-15 17:19:32');

GRANT ALL PRIVILEGES ON HomeApp.* TO 'HomeAppUser'@'%';
GRANT ALL PRIVILEGES ON HomeAppTest.* TO 'HomeAppUser'@'%';

FLUSH PRIVILEGES;

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

-- Dump completed on 2021-09-12 18:31:08
