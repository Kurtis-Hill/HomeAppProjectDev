-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 06, 2020 at 04:29 PM
-- Server version: 10.3.25-MariaDB-0+deb10u1
-- PHP Version: 7.3.19-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `HomeApp`
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
  `analogReading` double DEFAULT NULL,
  `highAnalog` double DEFAULT NULL,
  `lowAnalog` double DEFAULT NULL,
  `constRecord` tinyint(4) DEFAULT 1,
  `cardViewID` int(11) DEFAULT NULL,
  `deviceNameID` int(11) NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `analog`
--

INSERT INTO `analog` (`analogID`, `roomID`, `groupNameID`, `sensorNameID`, `analogReading`, `highAnalog`, `lowAnalog`, `constRecord`, `cardViewID`, `deviceNameID`, `timez`) VALUES
(2, 3, 1, 3, 2444, 1234, 4567, 1, 6, 3, '2020-08-16 14:52:18');

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
  `roomID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `cardIconID` int(11) DEFAULT NULL,
  `cardColourID` int(11) DEFAULT NULL,
  `cardStateID` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cardview`
--

INSERT INTO `cardview` (`cardViewID`, `sensorNameID`, `roomID`, `userID`, `cardIconID`, `cardColourID`, `cardStateID`) VALUES
(4, 1, 2, 1, 9, 3, 3),
(5, 2, 1, 1, 15, 2, 1),
(6, 3, 3, 1, 11, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `consthumid`
--

CREATE TABLE `consthumid` (
  `humidID` int(11) NOT NULL,
  `roomID` int(11) DEFAULT NULL,
  `groupNameID` int(11) DEFAULT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  `humidReading` double DEFAULT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `consttemp`
--

CREATE TABLE `consttemp` (
  `tempID` int(11) NOT NULL,
  `roomID` int(11) DEFAULT NULL,
  `groupNameID` int(11) DEFAULT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  `tempReading` double DEFAULT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `devicenames`
--

CREATE TABLE `devicenames` (
  `deviceNameID` int(11) NOT NULL,
  `deviceName` varchar(20) NOT NULL,
  `deviceSecret` varchar(32) NOT NULL,
  `groupNameID` int(11) NOT NULL,
  `roomID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `devicenames`
--

INSERT INTO `devicenames` (`deviceNameID`, `deviceName`, `deviceSecret`, `groupNameID`, `roomID`) VALUES
(1, 'LivingRooom', '', 1, 1),
(2, 'Bedroom', '', 1, 2),
(3, 'plant analog sensor', '', 1, 3),
(4, 'zfdg', '', 1, 1),
(5, 'asef', 'e3d65a300305f3183a222a19a44a5b7e', 1, 1),
(6, 'awefr/', '4eb5c2db652a5176ae8cee9997890ae8', 1, 1),
(7, 'asdfsadf', 'd0c0ad8fccefb3aa752c210bcab7d102', 1, 1),
(8, 'afasdf222', '872b0ddac91da6684bba61bf8e6a0d93', 1, 1),
(9, 'afasdf222', '6e039873e9fa4afa89efe3e74f456c09', 1, 1),
(10, 'afasdf222', '8bb86ac2c98fe5f13af20d278a53eac4', 1, 1),
(11, 'adsca', 'e3d7a44cd55ded3c532f3565ec57884c', 1, 1),
(12, 'LivingRoom', '4588aba19d8208e17767cd166527331c', 1, 1),
(13, 'LivingRoom', '2bfeb46344540fb935c598e130134407', 1, 1),
(14, 'sdfsd', '1aed08fbc406381caa8327cd695233c6', 1, 1),
(15, 'sdfsd', '7760110a357a84bab548c74823507b65', 1, 1),
(16, 'sdfsd', 'a5d7dcced3dceef0ff6c4480d15796e1', 1, 1),
(17, 'sdfsd', 'dc711b43aecbc5cfcc66f25f41034b99', 1, 1),
(18, 'sdfsd', '5437765a20eb89ecffeafee13ba360fd', 1, 1),
(19, 'sdfsd', 'ab3137e33cfe2b3325809f27bb011daf', 1, 1),
(20, 'lmwendkwkjdwlke', 'd74503e0433986a8f5ade5fcd9002d28', 1, 1),
(21, 'sdfsd', 'bd30f2a365673af184a32831849a4bc8', 1, 1),
(22, 'LivingRoom2', 'cf18dd902c0d153b3e40dd2dcff2c363', 1, 1),
(23, 'adrfsadfnmasfd', '5238d8f69be38da9fa6fe564df9ebf3c', 1, 1),
(24, 'KJBHSADFsadfdsaf', '970ce1ae71f034ea060344b8a5f58f32', 1, 1),
(25, 'sadfdd', '14c10dc2f870f330170b51384dfcd749', 1, 1),
(26, 'dfsgddghhh', 'da2c257ef6ef54f4f84e48040b200b00', 1, 1),
(27, 'sdfkdfggg', 'e45c065de762b8a8a10437843c8721fb', 1, 1),
(28, 'afsgdfg', '729b04d312bbe49db65943bcab714dea', 1, 1),
(29, 'asdfsdf', '0eb3b3fe1094e8dfe3c4bca4bb8e8f5d', 1, 1),
(30, 'sadfdsfvv', '3da3914133eb4a591ac8488f4f0043fd', 1, 1),
(31, 'zxcxxx', '66ab90058cce42063817e004361d77df', 1, 1),
(32, 'bbbccxcdd', '58755c4a4ca832848f39224754dadd18', 1, 1),
(33, 'mmxncx', '2bdbfc84e0feb69bc3975c0dcdc8eae4', 1, 1),
(34, 'lsdghdsf', 'f8c0e83b3ef18735e906229e8dd57b2e', 1, 1),
(35, 'dsfgdfz', 'aabab36228945b2ce0efb0b9b454d8f8', 1, 1),
(36, 'gujereeee', '12c27ae7cb335514d064b5a9a8dd9861', 1, 1),
(37, 'deviceNameee', 'e825f8e018e6dd281ffc0a2c9bc38841', 1, 1),
(38, 'ghfffuee', '1b611c77be558ac5b855043fa5c12dcb', 1, 1),
(39, 'ghfffueex', 'eaed84ed6eec8abb2bf6a2e7de8952ce', 1, 2),
(40, 'plooffff', 'd83da60065a6d4b3854e446d6815bfe6', 1, 1),
(41, 'heydooo', 'd56314bbd93e4d1bd3ad74b5625c12cf', 1, 1),
(42, 'jkhasjkdfjsdds', 'fbf87c68eeb2c42ecd07946f5e566e2f', 1, 1),
(43, 'jkkdmccccc', 'f1a8d7edbe24a3a5ee221d4d559c5cb2', 1, 1),
(44, 'cmccc', '8c6b986d35317eb6e4cdad1bf0424baf', 1, 1),
(45, 'fghfggggg', '81160002147f4de939fc8d2a3dbfec1d', 1, 1),
(46, '2', '70c1bc3358ad1f9c6e795cfb4d39ab23', 2, 2),
(47, 'Dreadddna', '559d051cbb6c98fbeb43e49cfd541b90', 2, 1),
(48, 'yereeeeee', '6d8455829717062b2c9b0c6d383b43b3', 1, 1),
(49, 'dfick', '7bf7ab89d7771a74912b3f149f9b29aa', 1, 1),
(50, 'jersssusss', 'b12f78b2b64f0984384ccffe9b804041', 1, 1),
(51, 'dfgdffffff', 'df72112679a8509a126e673900278c40', 1, 1),
(52, 'kkkjhjkhjkh', '3303aae01796511ddc30fe2fbd237208', 1, 1),
(53, 'gggfdfgff', 'e54b2ff4b6a55bf951aa8ef1c383983e', 1, 1),
(54, 'hhgfhgfggfggg', '57bce8280fc146122dc9557423f21185', 1, 1),
(55, 'jjhg', 'cf36b3c1f2aa5ed79fa69e13a7148839', 1, 1),
(56, 'ghjk56545674567', '1d167327a60b14ad7c29c17f1c9eef8d', 1, 1),
(57, 'cvbcv', '43543db3f42538883dd4f06867fe58db', 1, 1),
(58, 'dfsg', 'ef3302fc751ca136f3093409fa79485d', 1, 1),
(59, 'dfsg', 'e130caee0eb1dbfc09d7e1f159f4cad9', 1, 1),
(60, 'dfsg', '3ea409a16d4d981358f5fb095feef72d', 1, 1),
(61, 'dfsg', 'a218bf635ed2436a2127db6eb00ee93c', 1, 1),
(62, 'dfsg', 'bf73270488004df48ee91f8c15555825', 1, 1),
(63, 'sdfc', '6ca57a7461778a6601d8232d3dd0853b', 1, 1),
(64, 'sdfc', '8ceec15a5f7c2faa28eeb9a814b78b61', 1, 1),
(65, 'sdfcdsfadfasdf', '0abdd1ea61d3be6e6067cd2c2208ac69', 1, 1),
(66, 'cxzvcvbcvbvcvv', '9c7e00af0932b54ea03ad5d7a7fc25a8', 1, 1),
(67, 'zxcxx', '0ddf75e9792f5a4d53872b6d030590d8', 1, 1),
(68, 'asdfdsff', '8f6eeda814dba238ee79fe27a2636ac1', 1, 1),
(69, 'hereisone', 'c2bd9d866fb152e5c6fc006c8616dd12', 1, 1),
(70, 'jelly', '4267b4fe4e34fbce8b94e7ff63ef59ab', 1, 1),
(71, 'weqdas', '9112e31cbb55a2680c579bffee45e1e6', 1, 1),
(72, 'jammy', '1691aa5130e759a8380dc410d997960f', 1, 1),
(73, 'kjbkmmmmmm', 'cb783fc08c0570a5e9bf9c4a900982b9', 2, 2);

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
(2, 'test', '2020-08-30 23:28:28');

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
(2, 1, 2);

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
  `constRecord` tinyint(1) NOT NULL,
  `cardViewID` int(11) DEFAULT NULL,
  `deviceNameID` int(11) NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `humid`
--

INSERT INTO `humid` (`humidID`, `roomID`, `groupNameID`, `sensorNameID`, `humidReading`, `highHumid`, `lowHumid`, `constRecord`, `cardViewID`, `deviceNameID`, `timez`) VALUES
(2, 2, 1, 1, 10, 3, 4, 0, NULL, 2, '2020-08-16 14:43:18');

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
  `roomID` int(11) DEFAULT NULL,
  `groupNameID` int(11) DEFAULT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  `analogReading` double DEFAULT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `outofrangehumid`
--

CREATE TABLE `outofrangehumid` (
  `humidID` int(11) NOT NULL,
  `roomID` int(11) DEFAULT NULL,
  `groupNameID` int(11) DEFAULT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  `humidReading` double DEFAULT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `outofrangetemp`
--

CREATE TABLE `outofrangetemp` (
  `tempID` int(11) NOT NULL,
  `roomID` int(11) DEFAULT NULL,
  `groupNameID` int(11) DEFAULT NULL,
  `sensorNameID` int(11) DEFAULT NULL,
  `tempReadingID` double DEFAULT NULL,
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
(508, '67496cc414a829e30b20c60a8d182cf7a0088b5510071da06a5bac4b25381a9368338cbb7030d4a4b6b482902c111ee955a4ab40c0d90b37273a31e3e86e0030', 'admin', '2020-12-05 00:53:47');

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
  `groupNameID` int(11) DEFAULT NULL,
  `roomID` int(11) DEFAULT NULL,
  `sensorTypeID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sensornames`
--

INSERT INTO `sensornames` (`sensorNameID`, `sensorName`, `deviceNameID`, `groupNameID`, `roomID`, `sensorTypeID`) VALUES
(1, 'BabySensor', 2, 1, 2, 1),
(2, 'FishTank', 1, 1, 1, 2),
(3, 'Plants', 3, 1, 1, 3);

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
(4, 'BM11', 'Weather Station Sensor');

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
  `constRecord` tinyint(1) NOT NULL,
  `cardViewID` int(11) DEFAULT NULL,
  `deviceNameID` int(11) NOT NULL,
  `timez` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `temp`
--

INSERT INTO `temp` (`tempID`, `roomID`, `groupNameID`, `sensorNameID`, `tempReading`, `highTemp`, `lowTemp`, `constRecord`, `cardViewID`, `deviceNameID`, `timez`) VALUES
(7, 1, 1, 2, 10, 90, 100, 1, 5, 1, '2020-08-16 14:45:07'),
(8, 2, 1, 1, 10, 1, 2, 1, 4, 2, '2020-08-16 14:45:07');

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
(1, 'admin', 'admin', 'admin', '[\"ROLE_USER\",\"ROLE_ADMIN\"]', '/assets/pictures/guest.jpg', '$argon2id$v=19$m=65536,t=4,p=1$YZtgDPbw/cXStIttSFXAgQ$+S2z//XpDmqAvprgfCC1MonuFoMUBMzD4iZ7E0xdE64', NULL, 1, '2020-07-07 23:00:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analog`
--
ALTER TABLE `analog`
  ADD PRIMARY KEY (`analogID`),
  ADD KEY `roomID` (`roomID`),
  ADD KEY `groupNameID` (`groupNameID`),
  ADD KEY `analog_ibfk_3` (`sensorNameID`),
  ADD KEY `analog_ibfk_5` (`cardViewID`),
  ADD KEY `analog_ibfk_6` (`deviceNameID`);

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
  ADD KEY `SensorName` (`sensorNameID`),
  ADD KEY `Room` (`roomID`),
  ADD KEY `UserID` (`userID`),
  ADD KEY `cardColour` (`cardColourID`),
  ADD KEY `cardIcon` (`cardIconID`),
  ADD KEY `cardview_show` (`cardViewID`),
  ADD KEY `cardState` (`cardStateID`);

--
-- Indexes for table `consthumid`
--
ALTER TABLE `consthumid`
  ADD PRIMARY KEY (`humidID`),
  ADD KEY `Room` (`roomID`),
  ADD KEY `GroupName` (`groupNameID`),
  ADD KEY `SensorName` (`sensorNameID`);

--
-- Indexes for table `consttemp`
--
ALTER TABLE `consttemp`
  ADD PRIMARY KEY (`tempID`),
  ADD KEY `Room` (`roomID`),
  ADD KEY `GroupName` (`groupNameID`),
  ADD KEY `SensorName` (`sensorNameID`);

--
-- Indexes for table `devicenames`
--
ALTER TABLE `devicenames`
  ADD PRIMARY KEY (`deviceNameID`),
  ADD KEY `groupNameID` (`groupNameID`),
  ADD KEY `roomID` (`roomID`);

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
  ADD KEY `Room` (`roomID`),
  ADD KEY `GroupName` (`groupNameID`),
  ADD KEY `humid_ibfk_3` (`sensorNameID`),
  ADD KEY `humid_ibfk_5` (`cardViewID`),
  ADD KEY `humid_ibfk_6` (`deviceNameID`);

--
-- Indexes for table `icons`
--
ALTER TABLE `icons`
  ADD PRIMARY KEY (`iconID`),
  ADD UNIQUE KEY `iconName_2` (`iconName`);

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
  ADD KEY `roomOne` (`roomID`),
  ADD KEY `groupOne` (`groupNameID`),
  ADD KEY `sensorOne` (`sensorNameID`);

--
-- Indexes for table `outofrangehumid`
--
ALTER TABLE `outofrangehumid`
  ADD PRIMARY KEY (`humidID`),
  ADD KEY `Room` (`roomID`),
  ADD KEY `GroupName` (`groupNameID`),
  ADD KEY `SensorName` (`sensorNameID`);

--
-- Indexes for table `outofrangetemp`
--
ALTER TABLE `outofrangetemp`
  ADD PRIMARY KEY (`tempID`),
  ADD KEY `Room` (`roomID`),
  ADD KEY `GroupName` (`groupNameID`),
  ADD KEY `SensorName` (`sensorNameID`);

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
  ADD KEY `GroupName` (`groupNameID`),
  ADD KEY `Room` (`roomID`),
  ADD KEY `SensorType` (`sensorTypeID`),
  ADD KEY `sensornames_ibfk_1` (`deviceNameID`);

--
-- Indexes for table `sensortype`
--
ALTER TABLE `sensortype`
  ADD PRIMARY KEY (`sensorTypeID`);

--
-- Indexes for table `temp`
--
ALTER TABLE `temp`
  ADD PRIMARY KEY (`tempID`),
  ADD UNIQUE KEY `sensorNameID` (`sensorNameID`),
  ADD KEY `Room` (`roomID`),
  ADD KEY `GroupName` (`groupNameID`),
  ADD KEY `temp_ibfk_5` (`cardViewID`),
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
  MODIFY `analogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `cardViewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT for table `devicenames`
--
ALTER TABLE `devicenames`
  MODIFY `deviceNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `groupname`
--
ALTER TABLE `groupname`
  MODIFY `groupNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `groupnnamemapping`
--
ALTER TABLE `groupnnamemapping`
  MODIFY `groupNameMappingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `humid`
--
ALTER TABLE `humid`
  MODIFY `humidID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `icons`
--
ALTER TABLE `icons`
  MODIFY `iconID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
  MODIFY `tempID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=509;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `roomID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sensornames`
--
ALTER TABLE `sensornames`
  MODIFY `sensorNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sensortype`
--
ALTER TABLE `sensortype`
  MODIFY `sensorTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `temp`
--
ALTER TABLE `temp`
  MODIFY `tempID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `analog`
--
ALTER TABLE `analog`
  ADD CONSTRAINT `FK_A78C95C12D8C0469` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_A78C95C13BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_A78C95C140774A0E` FOREIGN KEY (`cardViewID`) REFERENCES `cardview` (`cardViewID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_A78C95C19F0A2316` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_A78C95C19F0A2419` FOREIGN KEY (`deviceNameID`) REFERENCES `devicenames` (`deviceNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cardview`
--
ALTER TABLE `cardview`
  ADD CONSTRAINT `FK_E36636B53BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_E36636B53casrdState` FOREIGN KEY (`cardStateID`) REFERENCES `cardstate` (`cardStateID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_E36636B55FD86D04` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_E36636B5840D9A7A` FOREIGN KEY (`cardIconID`) REFERENCES `icons` (`iconID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_E36636B59F0A2316` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_E36636B5A356FF88` FOREIGN KEY (`cardColourID`) REFERENCES `cardcolour` (`colourID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `consthumid`
--
ALTER TABLE `consthumid`
  ADD CONSTRAINT `FK_999EEF662D8C0469` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`),
  ADD CONSTRAINT `FK_999EEF663BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`),
  ADD CONSTRAINT `FK_999EEF669F0A2316` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`);

--
-- Constraints for table `consttemp`
--
ALTER TABLE `consttemp`
  ADD CONSTRAINT `consttemp_ibfk_1` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`),
  ADD CONSTRAINT `consttemp_ibfk_2` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`),
  ADD CONSTRAINT `consttemp_ibfk_3` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`);

--
-- Constraints for table `devicenames`
--
ALTER TABLE `devicenames`
  ADD CONSTRAINT `devicenames_ibfk_1` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devicenames_ibfk_2` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `FK_8D6EB6E340774A0E` FOREIGN KEY (`cardViewID`) REFERENCES `cardview` (`cardViewID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_8D6EB6E39F0A2316` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_A78C95C19F0A2317` FOREIGN KEY (`deviceNameID`) REFERENCES `devicenames` (`deviceNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `outofrangeanalog`
--
ALTER TABLE `outofrangeanalog`
  ADD CONSTRAINT `outofrangeanalog_ibfk_1` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`),
  ADD CONSTRAINT `outofrangeanalog_ibfk_2` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`),
  ADD CONSTRAINT `outofrangeanalog_ibfk_3` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`);

--
-- Constraints for table `outofrangehumid`
--
ALTER TABLE `outofrangehumid`
  ADD CONSTRAINT `FK_16E23E162D8C0469` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`),
  ADD CONSTRAINT `FK_16E23E163BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`),
  ADD CONSTRAINT `FK_16E23E169F0A2316` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`);

--
-- Constraints for table `outofrangetemp`
--
ALTER TABLE `outofrangetemp`
  ADD CONSTRAINT `FK_E319C00A2D8C0469` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`),
  ADD CONSTRAINT `FK_E319C00A3BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`),
  ADD CONSTRAINT `FK_E319C00A9F0A2316` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`);

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `FK_729F519B2D8C0469` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`);

--
-- Constraints for table `sensornames`
--
ALTER TABLE `sensornames`
  ADD CONSTRAINT `FK_82F2A8F42D8C0469` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_82F2A8F46B4A071A` FOREIGN KEY (`sensorTypeID`) REFERENCES `sensortype` (`sensorTypeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_82F2A8F49F0A2316` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sensornames_ibfk_1` FOREIGN KEY (`deviceNameID`) REFERENCES `devicenames` (`deviceNameID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `temp`
--
ALTER TABLE `temp`
  ADD CONSTRAINT `FK_A78C95C19F0A231` FOREIGN KEY (`deviceNameID`) REFERENCES `devicenames` (`deviceNameID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_B5385CA2D8C0469` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`),
  ADD CONSTRAINT `FK_B5385CA3BE475E6` FOREIGN KEY (`sensorNameID`) REFERENCES `sensornames` (`sensorNameID`),
  ADD CONSTRAINT `FK_B5385CA40774A0E` FOREIGN KEY (`cardViewID`) REFERENCES `cardview` (`cardViewID`),
  ADD CONSTRAINT `FK_B5385CA9F0A2316` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`groupNameID`) REFERENCES `groupname` (`groupNameID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
