<?php

declare(strict_types=1);

namespace App\ORM\Migrations;

use App\User\Entity\GroupNames;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
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
            CREATE TABLE user (
                userID INT AUTO_INCREMENT NOT NULL, 
                firstName VARCHAR(20) NOT NULL, 
                lastName VARCHAR(20) NOT NULL, 
                email VARCHAR(180) NOT NULL, 
                roles JSON NOT NULL, 
                profilePic VARCHAR(100) CHARACTER SET utf8mb3 DEFAULT \'\'\'/assets/pictures/guest.jpg\'\'\', 
                password LONGTEXT CHARACTER SET utf8mb3 NOT NULL, 
                salt LONGTEXT CHARACTER SET utf8mb3 DEFAULT NULL, 
                groupID INT DEFAULT NULL, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                INDEX GroupName (groupID), 
                INDEX profilePic (profilePic), 
                UNIQUE INDEX email (email), 
                PRIMARY KEY(userID)
            ) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE groupname (
                groupID INT AUTO_INCREMENT NOT NULL,
                groupName VARCHAR(50) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                UNIQUE INDEX groupName (groupName), 
                PRIMARY KEY(groupID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE groupnnamemapping (
                groupNameMappingID INT AUTO_INCREMENT NOT NULL, 
                userID INT NOT NULL, 
                groupID INT NOT NULL, 
                INDEX groupID (groupID), 
                INDEX userID (userID), 
                UNIQUE INDEX IDX_1C993DEE5FD86D04 (userID, groupID), 
                PRIMARY KEY(groupNameMappingID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE analog (
                analogID INT AUTO_INCREMENT NOT NULL, 
                sensorID INT NOT NULL, 
                analogReading DOUBLE PRECISION DEFAULT NULL, 
                highAnalog DOUBLE PRECISION DEFAULT 1000, 
                lowAnalog DOUBLE PRECISION DEFAULT 1000, 
                constRecord TINYINT(1) DEFAULT \'0\', 
                updatedAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                UNIQUE INDEX analog_ibfk_3 (sensorID), 
                PRIMARY KEY(analogID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE humidity (
                humidID INT AUTO_INCREMENT NOT NULL, 
                sensorID INT NOT NULL, 
                humidReading DOUBLE PRECISION NOT NULL, 
                highHumid DOUBLE PRECISION DEFAULT \'70\' NOT NULL, 
                lowHumid DOUBLE PRECISION DEFAULT \'15\' NOT NULL, 
                constRecord TINYINT(1) DEFAULT \'0\' NOT NULL, 
                updatedAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                UNIQUE INDEX humid_ibfk_1 (sensorID), 
                PRIMARY KEY(humidID)
            )
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE latitude (
                latitudeID INT AUTO_INCREMENT NOT NULL, 
                sensorID INT NOT NULL, 
                latitude DOUBLE PRECISION NOT NULL, 
                highLatitude DOUBLE PRECISION DEFAULT \'90\' NOT NULL, 
                lowLatitude DOUBLE PRECISION DEFAULT \'-90\' NOT NULL, 
                constRecord TINYINT(1) DEFAULT \'0\' NOT NULL, 
                updatedAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                UNIQUE INDEX lat_ibfk_1 (sensorID), 
                PRIMARY KEY(latitudeID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
        );

        $this->addSql('
            CREATE TABLE temperature (
                tempID INT AUTO_INCREMENT NOT NULL, 
                sensorID INT DEFAULT NULL, 
                tempReading DOUBLE PRECISION NOT NULL, 
                highTemp DOUBLE PRECISION DEFAULT \'26\' NOT NULL, 
                lowTemp DOUBLE PRECISION DEFAULT \'12\' NOT NULL, 
                constRecord TINYINT(1) DEFAULT \'0\' NOT NULL, 
                updatedAt DATETIME DEFAULT current_timestamp() NOT NULL,
                UNIQUE INDEX temp_ibfk_1 (sensorID), 
                PRIMARY KEY(tempID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
        );

        $this->addSql('
            CREATE TABLE constanalog (
                constRecordID INT AUTO_INCREMENT NOT NULL, 
                analogID INT NOT NULL, 
                sensorReading DOUBLE PRECISION NOT NULL, 
                createdAt DATETIME NOT NULL, 
                INDEX sensorID (analogID), 
                PRIMARY KEY(constRecordID)
             ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('
            CREATE TABLE consthumid (
                constRecordID INT AUTO_INCREMENT NOT NULL, 
                humidID INT NOT NULL, 
                sensorReading DOUBLE PRECISION NOT NULL, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                INDEX sensorID (humidID), 
                PRIMARY KEY(constRecordID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE constlatitude (
                constRecordID INT AUTO_INCREMENT NOT NULL, 
                latitudeID INT NOT NULL, 
                sensorReading DOUBLE PRECISION NOT NULL, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                INDEX latitudeID (latitudeID), PRIMARY KEY(constRecordID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE consttemp (
                constRecordID INT AUTO_INCREMENT NOT NULL, 
                tempID INT NOT NULL, 
                sensorReading DOUBLE PRECISION NOT NULL, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                INDEX consttemp_ibfk_1 (tempID), 
                PRIMARY KEY(constRecordID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE outofrangeanalog (
                outofrangeID INT AUTO_INCREMENT NOT NULL, 
                analogID INT NOT NULL, 
                sensorReading DOUBLE PRECISION NOT NULL, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                INDEX sensorID (analogID), 
                PRIMARY KEY(outofrangeID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE outofrangehumid (
                outofrangeID INT AUTO_INCREMENT NOT NULL, 
                humidID INT NOT NULL, 
                sensorReading DOUBLE PRECISION NOT NULL, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                INDEX sensorID (humidID), 
                PRIMARY KEY(outofrangeID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE outofrangelatitude (
                outofrangeID INT AUTO_INCREMENT NOT NULL, 
                latitudeID INT NOT NULL, 
                sensorReading DOUBLE PRECISION NOT NULL, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                INDEX outofrangelatitude_ibfk_1 (latitudeID), 
                PRIMARY KEY(outofrangeID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE outofrangetemp (
                outofrangeID INT AUTO_INCREMENT NOT NULL, 
                tempID INT NOT NULL, 
                sensorReading DOUBLE PRECISION NOT NULL, 
                createdAt DATETIME DEFAULT current_timestamp() NOT NULL, 
                INDEX outofrangetemp_ibfk_1 (tempID), 
                PRIMARY KEY(outofrangeID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE bmp (
                bmpID INT AUTO_INCREMENT NOT NULL, 
                tempID INT NOT NULL, 
                humidID INT NOT NULL, 
                latitudeID INT NOT NULL, 
                sensorID INT DEFAULT NULL, 
                UNIQUE INDEX sensorID (sensorID), 
                UNIQUE INDEX tempID (tempID), 
                UNIQUE INDEX humidID (humidID), 
                UNIQUE INDEX latitudeID (latitudeID), 
                PRIMARY KEY(bmpID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE dallas (
                dallasID INT AUTO_INCREMENT NOT NULL, 
                tempID INT NOT NULL, 
                sensorID INT NOT NULL, 
                UNIQUE INDEX tempID (tempID), 
                UNIQUE INDEX sensorID (sensorID), 
                PRIMARY KEY(dallasID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE dht (
                dhtID INT AUTO_INCREMENT NOT NULL, 
                tempID INT NOT NULL, 
                humidID INT NOT NULL, 
                sensorID INT NOT NULL, 
                UNIQUE INDEX sensorID (sensorID), 
                UNIQUE INDEX tempID (tempID), 
                UNIQUE INDEX humidID (humidID), 
                PRIMARY KEY(dhtID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql('
            CREATE TABLE soil (
                soilID INT AUTO_INCREMENT NOT NULL, 
                analogID INT NOT NULL, 
                sensorID INT NOT NULL, 
                UNIQUE INDEX analogID (analogID), 
                UNIQUE INDEX sensorID (sensorID), 
                PRIMARY KEY(soilID)
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
                INDEX FK_E36636B53casrdState (stateID), 
                INDEX UserID (userID), 
                INDEX FK_E36636B5840D9A7A (iconID), 
                INDEX cardview_show (cardViewID), 
                INDEX FK_E36636B5A356FF88 (colourID), 
                INDEX FK_E36636B53BE475E6 (sensorID), 
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
                UNIQUE INDEX device_room_un (deviceName, roomID), 
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
                INDEX sensornames_ibfk_1 (deviceID), 
                INDEX sensornames_ibfk_2 (createdBy), 
                INDEX sensortype (sensorTypeID), 
                UNIQUE INDEX sensor_device (sensorName, deviceID),
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

        $this->addSql("
            INSERT INTO `readingtypes` 
                (`readingTypeID`, `readingType`) 
            VALUES
                (1, 'temperature'),
                (2, 'humidity'),
                (3, 'analog'),
                (4, 'latitude');
        ");

        $this->addSql("
            INSERT INTO `sensortype` 
                (`sensorTypeID`, `sensorType`, `description`)   
            VALUES
                (1, 'Dht', 'Temperature and Humidity Sensor'),
                (2, 'Dallas', 'Water Proof Temperature Sensor'),
                (3, 'Soil', 'Soil Moisture Sensor'),
                (4, 'Bmp', 'Weather Station Sensor');
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
                (3, 'DEVICE_ONLY'),
                (2, 'OFF'),
                (1, 'ON'),
                (4, 'ROOM_ONLY');
        ");

        $this->addSql("
            INSERT INTO `user` 
                (`userID`, `firstName`, `lastName`, `email`, `roles`, `profilePic`, `password`, `salt`, `groupID`, `createdAt`) 
            VALUES
                (1, 'admin', 'admin', 'admin', '[\"ROLE_ADMIN\"]', 'guest.jpg', '\$argon2id\$v=19\$m=65536,t=4,p=1\$7zx+pasSn547DYfLgO9MuQ\$ACTjDqrmJDgB9KfoZUOpESDZn/071R/Bmfju9o+R1Zw', NULL, 2, '2021-07-15 17:19:32');
        ");

        $this->addSql("
            INSERT INTO `groupname` 
                (`groupID`, `groupName`) 
            VALUES
                (1, 'home-app-group'),
                (2, 'admin-group');
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
                (27, 'smoking', 'smoking');
        ");

        // Alter tables
        $this->addSql("
            ALTER TABLE `user`
              ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`groupID`) REFERENCES `groupname` (`groupID`);
        ");

        $this->addSql("
            ALTER TABLE `groupnnamemapping`
              ADD CONSTRAINT `groupnnamemapping_ibfk_1` FOREIGN KEY (`groupID`) REFERENCES `groupname` (`groupID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `groupnnamemapping_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `sensors`
              ADD CONSTRAINT `FK_82F2A8F46B4A071A` FOREIGN KEY (`sensorTypeID`) REFERENCES `sensortype` (`sensorTypeID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `sensornames_ibfk_1` FOREIGN KEY (`deviceID`) REFERENCES `devices` (`deviceID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `sensornames_ibfk_2` FOREIGN KEY (`createdBy`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `devices`
              ADD CONSTRAINT `devicenames_ibfk_1` FOREIGN KEY (`groupID`) REFERENCES `groupname` (`groupID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `devicenames_ibfk_2` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `devicenames_ibfk_3` FOREIGN KEY (`createdBy`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `cardview`
              ADD CONSTRAINT `FK_E36636B53BE475E6` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `FK_E36636B53casrdState` FOREIGN KEY (`stateID`) REFERENCES `state` (`stateID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `FK_E36636B55FD86D04` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `FK_E36636B5840D9A7A` FOREIGN KEY (`iconID`) REFERENCES `icons` (`iconID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `FK_E36636B5A356FF88` FOREIGN KEY (`colourID`) REFERENCES `colours` (`colourID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `temperature`
                ADD CONSTRAINT `FK_B5385CA3BE475E6` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `analog`
                ADD CONSTRAINT `FK_A78C95C13BE475E6` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE;   
        ");

        $this->addSql("
            ALTER TABLE `humidity`
                ADD CONSTRAINT `FK_8D6EB6E33BE475E6` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `latitude`
              ADD CONSTRAINT `latitude_ibfk_4` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `bmp`
              ADD CONSTRAINT `bmp_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `bmp_ibfk_2` FOREIGN KEY (`tempID`) REFERENCES `temperature` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `bmp_ibfk_3` FOREIGN KEY (`humidID`) REFERENCES `humidity` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `bmp_ibfk_4` FOREIGN KEY (`latitudeID`) REFERENCES `latitude` (`latitudeID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `soil`
              ADD CONSTRAINT `soil_ibfk_1` FOREIGN KEY (`analogID`) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `soil_ibfk_2` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `dallas`
              ADD CONSTRAINT `dallas_ibfk_2` FOREIGN KEY (`tempID`) REFERENCES `temperature` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `dallas_ibfk_3` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `dht`
              ADD CONSTRAINT `dhtsensor_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `dhtsensor_ibfk_2` FOREIGN KEY (`humidID`) REFERENCES `humidity` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `dhtsensor_ibfk_3` FOREIGN KEY (`tempID`) REFERENCES `temperature` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `constanalog`
                ADD CONSTRAINT `constanalog_ibfk_1` FOREIGN KEY (analogID) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `consthumid`
                ADD CONSTRAINT `consthumid_ibfk_1` FOREIGN KEY (`humidID`) REFERENCES `humidity` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `consttemp`
              ADD CONSTRAINT `consttemp_ibfk_1` FOREIGN KEY (tempID) REFERENCES `temperature` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `outofrangeanalog`
                ADD CONSTRAINT `outofrangeanalog_ibfk_1` FOREIGN KEY (analogID) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `outofrangehumid`
                ADD CONSTRAINT `outofrangehumid_ibfk_1` FOREIGN KEY (humidID) REFERENCES `humidity` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `outofrangetemp`
                ADD CONSTRAINT `outofrangetemp_ibfk_1` FOREIGN KEY (`tempID`) REFERENCES `temperature` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            ALTER TABLE `outofrangelatitude`
                ADD CONSTRAINT `outofrangelatitude_ibfk_1` FOREIGN KEY (`latitudeID`) REFERENCES `latitude` (`latitudeID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
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

        $this->addSql('DROP TABLE IF EXISTS groupname');

        $this->addSql('DROP TABLE IF EXISTS groupnnamemapping');

        $this->addSql('DROP TABLE IF EXISTS user');

        $this->addSql("SET FOREIGN_KEY_CHECKS = 1;");
    }

}
