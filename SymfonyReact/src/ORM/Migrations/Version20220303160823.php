<?php

declare(strict_types=1);

namespace App\ORM\Migrations;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\User\Entity\Group;
use App\UserInterface\Entity\Card\CardState;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220303160823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial database file for the base HomeApp';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('
            CREATE TABLE users (
                userID INT AUTO_INCREMENT NOT NULL, 
                firstName VARCHAR(20) NOT NULL, 
                lastName VARCHAR(20) NOT NULL, 
                email VARCHAR(180) NOT NULL,
                groupID INT NOT NULL,
                roles JSON NOT NULL, 
                profilePic VARCHAR(100) CHARACTER SET utf8mb3 DEFAULT \'\'\'/assets/pictures/guest.jpg\'\'\', 
                password LONGTEXT CHARACTER SET utf8mb3 NOT NULL, 
                salt LONGTEXT CHARACTER SET utf8mb3 DEFAULT NULL, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                UNIQUE INDEX email (email),
                INDEX groupID (groupID),
                INDEX profilePic (profilePic), 
                INDEX `password` (password),
                INDEX roles (roles),
                INDEX createdAt (createdAt),
                PRIMARY KEY(userID)
            ) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE `groups` (
                groupID INT AUTO_INCREMENT NOT NULL,
                groupName VARCHAR(50) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                UNIQUE INDEX groupName (groupName),
                INDEX createdAt (createdAt),
                PRIMARY KEY(groupID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE groupmappings (
                groupMappingID INT AUTO_INCREMENT NOT NULL, 
                userID INT NOT NULL, 
                groupID INT NOT NULL, 
                UNIQUE INDEX IDX_1C993DEE5FD86D04 (userID, groupID), 
                PRIMARY KEY(groupMappingID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql("
            CREATE TABLE `basereadingtype` (
                `baseReadingTypeID` INT AUTO_INCREMENT NOT NULL,
                `sensorID` INT NOT NULL,         
                `constRecord` TINYINT(1) NOT NULL,
                `updatedAt` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                `createdAt` DATETIME NOT NULL DEFAULT current_timestamp(),      
                INDEX sensorID (sensorID),
                INDEX constRecord (constRecord),
                INDEX updatedAt (updatedAt),
                INDEX createdAt (createdAt),
                PRIMARY KEY (`baseReadingTypeID`)
            )
        ");

        $this->addSql("
            CREATE TABLE `standardreadingtype`
            (
                `readingTypeID` INT AUTO_INCREMENT NOT NULL, 
                `baseReadingTypeID` INT NOT NULL,
                `currentReading` DOUBLE PRECISION NOT NULL,
                `highReading` DOUBLE PRECISION NOT NULL,
                `lowReading` DOUBLE PRECISION NOT NULL,
                `standardReadingType` VARCHAR(50) NOT NULL,        
                INDEX currentReading (currentReading),
                INDEX highReading (highReading),
                INDEX lowReading (lowReading),
                INDEX standardreadingtypeIndex (standardReadingType),
                PRIMARY KEY (`readingTypeID`)
            )             
        ");

        $this->addSql(
            'CREATE TABLE boolreadingtype (
                `readingTypeID` INT AUTO_INCREMENT NOT NULL,
                `baseReadingTypeID` INT NOT NULL,
                `currentReading` TINYINT(1) NOT NULL,
                `requestedReading` TINYINT(1) NOT NULL,
                `expectedReading` TINYINT(1) NULL DEFAULT NULL,
                `boolReadingType` VARCHAR(25) NOT NULL,
                INDEX currentReading (currentReading),
                INDEX boolReadingType (boolReadingType),
                PRIMARY KEY(readingTypeID)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            '
            CREATE TABLE readingtypeconst 
            (
                constRecordID INT AUTO_INCREMENT NOT NULL, 
                baseReadingTypeID INT NOT NULL, 
                sensorReading DOUBLE PRECISION NOT NULL, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL,
                sensorReadingType VARCHAR(50) NOT NULL, 
                INDEX sensorReading (sensorReading),
                INDEX createdAt (createdAt),
                PRIMARY KEY(constRecordID)
             ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
        );


        $this->addSql('
            CREATE TABLE readingtypeoutofrange (
                outofrangeID INT AUTO_INCREMENT NOT NULL, 
                baseReadingTypeID INT NOT NULL, 
                sensorReading DOUBLE PRECISION NOT NULL, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL,
                sensorReadingType VARCHAR(50) NOT NULL, 
                INDEX sensorReading (sensorReading),
                INDEX createdAt (createdAt),
                PRIMARY KEY(outofrangeID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE cardview (
                cardViewID INT AUTO_INCREMENT NOT NULL, 
                sensorID INT DEFAULT NULL, 
                userID INT DEFAULT NULL, 
                iconID INT DEFAULT NULL, 
                colourID INT DEFAULT NULL, 
                stateID INT DEFAULT 1 NOT NULL, 
                UNIQUE INDEX user_cardview (userID, sensorID),
                INDEX UserID (userID), 
                PRIMARY KEY(cardViewID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE devices (
                deviceID INT AUTO_INCREMENT NOT NULL, 
                deviceName VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, 
                password LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, 
                groupID INT NOT NULL, 
                roomID INT NOT NULL, 
                createdBy INT NOT NULL, 
                ipAddress VARCHAR(13) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, 
                externalIpAddress VARCHAR(13) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, 
                roles JSON NOT NULL COLLATE `utf8mb4_general_ci`, 
                INDEX createdBy (createdBy),
                INDEX groupID (groupID),
                INDEX roomID (roomID),
                INDEX deviceName (deviceName),
                UNIQUE INDEX device_room_un (deviceName, roomID),
                UNIQUE INDEX deviceIP (ipAddress, externalIpAddress),  
                PRIMARY KEY(deviceID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE colours (
                colourID INT AUTO_INCREMENT NOT NULL, 
                colour VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, 
                shade VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, 
                UNIQUE INDEX colour (colour), 
                UNIQUE INDEX shade (shade), 
                PRIMARY KEY(colourID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('
            CREATE TABLE state (
                stateID INT AUTO_INCREMENT NOT NULL, 
                state VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, 
                UNIQUE INDEX state (state), 
                PRIMARY KEY(stateID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE icons (
                iconID INT AUTO_INCREMENT NOT NULL, 
                iconName VARCHAR(20) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, 
                description VARCHAR(20) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, 
                UNIQUE INDEX iconName_2 (iconName), 
                PRIMARY KEY(iconID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\'
        ');

        $this->addSql('
            CREATE TABLE refresh_tokens (
                id INT AUTO_INCREMENT NOT NULL, 
                refresh_token VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, 
                username VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, 
                valid DATETIME NOT NULL, 
                UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), 
                PRIMARY KEY(id)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('
            CREATE TABLE room (
                roomID INT AUTO_INCREMENT NOT NULL, 
                room VARCHAR(20) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`,
                UNIQUE INDEX room (room),
                PRIMARY KEY(roomID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE sensors (
                sensorID INT AUTO_INCREMENT NOT NULL, 
                createdBy INT NOT NULL, 
                sensorName VARCHAR(50) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, 
                deviceID INT NOT NULL, 
                sensorTypeID INT NOT NULL,
                pinNumber TINYINT NOT NULL,
                takeReadingIntervalMilli MEDIUMINT DEFAULT '. Sensor::DEFAULT_READING_INTERVAL . ' NOT NULL,
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL,
                UNIQUE INDEX sensor_device (sensorName, deviceID),
                INDEX sensorName (sensorName),
                INDEX createdBy (createdBy),
                INDEX deviceID (deviceID),
                INDEX sensorTypeID (sensorTypeID),
                PRIMARY KEY(sensorID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\'
        ');

        $this->addSql('
            CREATE TABLE sensortype (
                sensorTypeID INT AUTO_INCREMENT NOT NULL, 
                sensorType VARCHAR(20) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, 
                description VARCHAR(50) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, 
                UNIQUE INDEX sensorType (sensorType), 
                PRIMARY KEY(sensorTypeID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\'
        ');

        $this->addSql('
            CREATE TABLE readingtypes (
                    readingTypeID INT AUTO_INCREMENT NOT NULL, 
                    readingType VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`,
                    UNIQUE INDEX readingType (readingType), 
                    PRIMARY KEY(readingTypeID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        // create table to log ip addresses
        $this->addSql('
            CREATE TABLE iplog (
                iplogID INT AUTO_INCREMENT NOT NULL, 
                ipAddress VARCHAR(13) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                UNIQUE INDEX ipAddress (ipAddress), 
                PRIMARY KEY(iplogID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\'
        ');

        $this->addSql("
            INSERT INTO `readingtypes` 
                (`readingTypeID`, `readingType`) 
            VALUES
                (1, '". Temperature::READING_TYPE ."'),
                (2, '" . Humidity::READING_TYPE . "'),
                (3, '". Analog::READING_TYPE . "'),
                (4, '". Latitude::READING_TYPE . "'),
                (5, '". Relay::READING_TYPE ."'),
                (6, '" . Motion::READING_TYPE . "');
        ");

        $this->addSql("
            INSERT INTO `sensortype` 
                (`sensorTypeID`, `sensorType`, `description`)   
            VALUES
                (1, '". Dht::NAME ."', 'Temperature and Humidity Sensor'),
                (2, '" . Dallas::NAME . "', 'Water Proof Temperature Sensor'),
                (3, '". Soil::NAME . "', 'Soil Moisture Sensor'),
                (4, '" . Bmp::NAME . "', 'Weather Station Sensor'),
                (5, '" . GenericRelay::NAME . "', 'Generic relay'),
                (6, '" . GenericMotion::NAME . "', 'Generic motion sensor'),
                (7, '" . LDR::NAME . "', 'Light resistor sensor'),
                (8, '" . Sht::NAME . "', 'High Accuracy Temperature and Humidity Sensor');
        ");

        $this->addSql("
            INSERT INTO `colours` 
                (`colourID`, `colour`, `shade`) 
            VALUES
                (1, 'danger', 'red'),
                (2, 'success', 'green'),
                (3, 'warning', 'Yellow'),
                (4, 'primary', 'blue'),
                (5, 'info', 'light blue'),
                (6, 'secondary', 'light grey'),
                (7, 'light', 'white'),
                (8, 'dark', 'dark grey');
        ");

        $this->addSql("
            INSERT INTO `state` 
                (`stateID`, `state`) 
            VALUES
                (3, '" . CardState::DEVICE_ONLY . "'),
                (2, '" . CardState::OFF . "'),
                (1, '" . CardState::ON . "'),
                (4, '" . CardState::ROOM_ONLY . "');
        ");

        $this->addSql("
            INSERT INTO `users` 
                (`userID`, `firstName`, `lastName`, `email`, `roles`, `groupID`, `profilePic`, `password`, `salt`, `createdAt`) 
            VALUES
                (1, 'admin', 'admin', 'admin', '[\"ROLE_ADMIN\"]', 1, 'guest.jpg', '\$argon2id\$v=19\$m=65536,t=4,p=1\$7zx+pasSn547DYfLgO9MuQ\$ACTjDqrmJDgB9KfoZUOpESDZn/071R/Bmfju9o+R1Zw', NULL, '2021-07-15 17:19:32');
        ");

        $this->addSql("
            INSERT INTO groups
                (`groupID`, `groupName`) 
            VALUES
                (1, '" . Group::HOME_APP_GROUP_NAME . "'),
                (2, '" . Group::ADMIN_GROUP_NAME ."');
        ");

        $this->addSql("
            INSERT INTO `icons` 
                (`iconID`, `iconName`, `description`) 
            VALUES
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
                (27, 'smoking', 'smoking'),
                (28, 'couch', 'couch/sofa'),
                (29, 'sun', 'the sun'),
                (30, 'frog', 'frog from the side'),
                (31, 'water', 'water 3 lines'),
                (32, 'temperature-low', 'thermometer on low'),
                (33, 'temperature-high', 'thermometer on high'),
                (34, 'house-user', 'house with user icon'),
                (35, 'shower', 'shower head'),
                (36, 'fan', 'fan blades'),
                (37, 'lightbulb', 'light bulb');
        ");

        // Alter tables
        $this->addSql("
            ALTER TABLE `users`
              ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`groupID`) REFERENCES `groups` (`groupID`);
        ");

        $this->addSql("
            ALTER TABLE `groupmappings`
              ADD CONSTRAINT `groupmapping_ibfk_1` FOREIGN KEY (`groupID`) REFERENCES `groups` (`groupID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `groupmapping_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `sensors`
              ADD CONSTRAINT `FK_82F2A8F46B4A071A` FOREIGN KEY (`sensorTypeID`) REFERENCES `sensortype` (`sensorTypeID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `sensornames_ibfk_1` FOREIGN KEY (`deviceID`) REFERENCES `devices` (`deviceID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `sensornames_ibfk_2` FOREIGN KEY (`createdBy`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `devices`
              ADD CONSTRAINT `devicenames_ibfk_1` FOREIGN KEY (`groupID`) REFERENCES `groups` (`groupID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `devicenames_ibfk_2` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `devicenames_ibfk_3` FOREIGN KEY (`createdBy`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `cardview`
              ADD CONSTRAINT `FK_E36636B53BE475E6` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `FK_E36636B53casrdState` FOREIGN KEY (`stateID`) REFERENCES `state` (`stateID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `FK_E36636B55FD86D04` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `FK_E36636B5840D9A7A` FOREIGN KEY (`iconID`) REFERENCES `icons` (`iconID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `FK_E36636B5A356FF88` FOREIGN KEY (`colourID`) REFERENCES `colours` (`colourID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `readingtypeoutofrange`
                ADD CONSTRAINT `outofrangeanalog_ibfk_1` FOREIGN KEY (baseReadingTypeID) REFERENCES `basereadingtype` (`baseReadingTypeID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `readingtypeconst`
                ADD CONSTRAINT `constanalog_ibfk_1` FOREIGN KEY (baseReadingTypeID) REFERENCES `basereadingtype` (`baseReadingTypeID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `basereadingtype`
                ADD CONSTRAINT FK_STANDARD_READING_TYPE_SENSOR FOREIGN KEY (sensorID) REFERENCES sensors (sensorID) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql('
            ALTER TABLE `standardreadingtype`
            ADD CONSTRAINT FK_STANDARD_READING_TYPE FOREIGN KEY (baseReadingTypeID) REFERENCES basereadingtype (baseReadingTypeID) ON DELETE CASCADE ON UPDATE CASCADE        
        ');

        $this->addSql('
            ALTER TABLE `boolreadingtype`
            ADD CONSTRAINT FK_BOOL_READING_TYPE FOREIGN KEY (baseReadingTypeID) REFERENCES basereadingtype (baseReadingTypeID) ON DELETE CASCADE ON UPDATE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql("SET FOREIGN_KEY_CHECKS = 0;");

        $this->addSql('DROP TABLE IF EXISTS colours');

        $this->addSql('DROP TABLE IF EXISTS state');

        $this->addSql('DROP TABLE IF EXISTS icons');

        $this->addSql('DROP TABLE IF EXISTS room');

        $this->addSql('DROP TABLE IF EXISTS bmp');

        $this->addSql('DROP TABLE IF EXISTS  dallas');

        $this->addSql('DROP TABLE IF EXISTS dht');

        $this->addSql('DROP TABLE IF EXISTS soil');

        $this->addSql('DROP TABLE IF EXISTS constanalog');

        $this->addSql('DROP TABLE IF EXISTS consthumid');

        $this->addSql('DROP TABLE IF EXISTS constlatitude');

        $this->addSql('DROP TABLE IF EXISTS consttemp');

        $this->addSql('DROP TABLE IF EXISTS outofrangeanalog');

        $this->addSql('DROP TABLE IF EXISTS outofrangehumid');

        $this->addSql('DROP TABLE IF EXISTS outofrangelatitude');

        $this->addSql('DROP TABLE IF EXISTS outofrangetemp');

        $this->addSql('DROP TABLE IF EXISTS sensors');

        $this->addSql('DROP TABLE IF EXISTS analog');

        $this->addSql('DROP TABLE IF EXISTS humidity');

        $this->addSql('DROP TABLE IF EXISTS temperature');

        $this->addSql('DROP TABLE IF EXISTS latitude');

        $this->addSql('DROP TABLE IF EXISTS migration_versions');

        $this->addSql('DROP TABLE IF EXISTS refresh_tokens');

        $this->addSql('DROP TABLE IF EXISTS devices');

        $this->addSql('DROP TABLE IF EXISTS sensortype');

        $this->addSql('DROP TABLE IF EXISTS cardview');

        $this->addSql('DROP TABLE IF EXISTS readingtypes');

        $this->addSql('DROP TABLE IF EXISTS `groups`');

        $this->addSql('DROP TABLE IF EXISTS groupmappings');

        $this->addSql('DROP TABLE IF EXISTS users');

        $this->addSql('DROP TABLE IF EXISTS iplog');

        $this->addSql('DROP TABLE IF EXISTS basereadingtype');

        $this->addSql('DROP TABLE IF EXISTS boolreadingtype');

        $this->addSql('DROP TABLE IF EXISTS standardreadingtype');

        $this->addSql('DROP TABLE IF EXISTS readingtypeconst');

        $this->addSql('DROP TABLE IF EXISTS readingtypeoutofrange');

        $this->addSql("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
