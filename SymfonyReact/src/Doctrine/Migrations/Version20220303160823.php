<?php

declare(strict_types=1);

namespace App\Doctrine\Migrations;

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

        $this->addSql('CREATE TABLE readingtypes (readingTypeID INT AUTO_INCREMENT NOT NULL, readingType VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(readingTypeID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE analog (analogID INT AUTO_INCREMENT NOT NULL, sensorNameID INT NOT NULL, analogReading DOUBLE PRECISION DEFAULT NULL, highAnalog DOUBLE PRECISION DEFAULT 1000, lowAnalog DOUBLE PRECISION DEFAULT 1000, constRecord TINYINT(1) DEFAULT \'0\', updatedAt DATETIME DEFAULT current_timestamp() NOT NULL, UNIQUE INDEX analog_ibfk_3 (sensorNameID), PRIMARY KEY(analogID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE bmp (bmpID INT AUTO_INCREMENT NOT NULL, tempID INT NOT NULL, humidID INT NOT NULL, latitudeID INT NOT NULL, sensorNameID INT DEFAULT NULL, INDEX bmp_ibfk_1 (sensorNameID), UNIQUE INDEX tempID (tempID), UNIQUE INDEX humidID (humidID), UNIQUE INDEX latitudeID (latitudeID), PRIMARY KEY(bmpID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE cardcolour (colourID INT AUTO_INCREMENT NOT NULL, colour VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, shade VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, UNIQUE INDEX colour (colour), UNIQUE INDEX shade (shade), PRIMARY KEY(colourID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE cardstate (cardStateID INT AUTO_INCREMENT NOT NULL, state VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, UNIQUE INDEX state (state), PRIMARY KEY(cardStateID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE cardview (cardViewID INT AUTO_INCREMENT NOT NULL, sensorNameID INT DEFAULT NULL, userID INT DEFAULT NULL, cardIconID INT DEFAULT NULL, cardColourID INT DEFAULT NULL, cardStateID INT DEFAULT 1 NOT NULL, INDEX FK_E36636B53casrdState (cardStateID), INDEX UserID (userID), INDEX FK_E36636B5840D9A7A (cardIconID), INDEX cardview_show (cardViewID), INDEX FK_E36636B5A356FF88 (cardColourID), INDEX FK_E36636B53BE475E6 (sensorNameID), PRIMARY KEY(cardViewID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE constanalog (constRecordID INT AUTO_INCREMENT NOT NULL, analogID INT NOT NULL, sensorReading DOUBLE PRECISION NOT NULL, createdAt DATETIME NOT NULL, INDEX sensorID (analogID), PRIMARY KEY(constRecordID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE consthumid (constRecordID INT AUTO_INCREMENT NOT NULL, humidID INT NOT NULL, sensorReading DOUBLE PRECISION NOT NULL, createdAt DATETIME DEFAULT current_timestamp() NOT NULL, INDEX sensorID (humidID), PRIMARY KEY(constRecordID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE constlatitude (constRecordID INT AUTO_INCREMENT NOT NULL, latitudeID INT NOT NULL, sensorReading DOUBLE PRECISION NOT NULL, createdAt DATETIME DEFAULT current_timestamp() NOT NULL, INDEX latitudeID (latitudeID), PRIMARY KEY(constRecordID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE consttemp (constRecordID INT AUTO_INCREMENT NOT NULL, tempID INT NOT NULL, sensorReading DOUBLE PRECISION NOT NULL, createdAt DATETIME DEFAULT current_timestamp() NOT NULL, INDEX consttemp_ibfk_1 (tempID), PRIMARY KEY(constRecordID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE dallas (dallasID INT AUTO_INCREMENT NOT NULL, tempID INT NOT NULL, sensorNameID INT NOT NULL, UNIQUE INDEX tempID (tempID), INDEX sensorNameID (sensorNameID), PRIMARY KEY(dallasID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE devicenames (deviceNameID INT AUTO_INCREMENT NOT NULL, deviceName VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, password LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, groupNameID INT NOT NULL, roomID INT NOT NULL, createdBy INT NOT NULL, ipAddress VARCHAR(13) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, externalIpAddress VARCHAR(13) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, roles JSON NOT NULL COLLATE `utf8mb4_general_ci`, INDEX createdBy (createdBy), INDEX groupNameID (groupNameID), INDEX roomID (roomID), PRIMARY KEY(deviceNameID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE dhtsensor (dhtID INT AUTO_INCREMENT NOT NULL, tempID INT NOT NULL, humidID INT NOT NULL, sensorNameID INT NOT NULL, UNIQUE INDEX cardviewID (sensorNameID), UNIQUE INDEX tempID (tempID), UNIQUE INDEX humidID (humidID), PRIMARY KEY(dhtID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE groupname (groupNameID INT AUTO_INCREMENT NOT NULL, groupName VARCHAR(50) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, createdAt DATETIME DEFAULT current_timestamp() NOT NULL, UNIQUE INDEX groupName (groupName), PRIMARY KEY(groupNameID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE groupnnamemapping (groupNameMappingID INT AUTO_INCREMENT NOT NULL, userID INT NOT NULL, groupNameID INT NOT NULL, INDEX groupNameID (groupNameID), INDEX userID (userID, groupNameID), INDEX IDX_1C993DEE5FD86D04 (userID), PRIMARY KEY(groupNameMappingID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE humid (humidID INT AUTO_INCREMENT NOT NULL, sensorNameID INT NOT NULL, humidReading DOUBLE PRECISION NOT NULL, highHumid DOUBLE PRECISION DEFAULT \'70\' NOT NULL, lowHumid DOUBLE PRECISION DEFAULT \'15\' NOT NULL, constRecord TINYINT(1) DEFAULT \'0\' NOT NULL, updatedAt DATETIME DEFAULT current_timestamp() NOT NULL, UNIQUE INDEX sensorNameID (sensorNameID), PRIMARY KEY(humidID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE icons (iconID INT AUTO_INCREMENT NOT NULL, iconName VARCHAR(20) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, description VARCHAR(20) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, UNIQUE INDEX iconName_2 (iconName), PRIMARY KEY(iconID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE latitude (latitudeID INT AUTO_INCREMENT NOT NULL, sensorNameID INT NOT NULL, latitude DOUBLE PRECISION NOT NULL, lowLatitude DOUBLE PRECISION NOT NULL, highLatitude DOUBLE PRECISION NOT NULL, constRecord TINYINT(1) DEFAULT \'0\' NOT NULL, updatedAt DATETIME DEFAULT current_timestamp() NOT NULL, UNIQUE INDEX sensorNameID (sensorNameID), PRIMARY KEY(latitudeID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE outofrangeanalog (outofrangeID INT AUTO_INCREMENT NOT NULL, analogID INT NOT NULL, sensorReading DOUBLE PRECISION NOT NULL, createdAt DATETIME DEFAULT current_timestamp() NOT NULL, INDEX sensorID (analogID), PRIMARY KEY(outofrangeID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE outofrangehumid (outofrangeID INT AUTO_INCREMENT NOT NULL, humidID INT NOT NULL, sensorReading DOUBLE PRECISION NOT NULL, createdAt DATETIME DEFAULT current_timestamp() NOT NULL, INDEX sensorID (humidID), PRIMARY KEY(outofrangeID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE outofrangelatitude (outofrangeID INT AUTO_INCREMENT NOT NULL, latitudeID INT NOT NULL, sensorReading DOUBLE PRECISION NOT NULL, createdAt DATETIME DEFAULT current_timestamp() NOT NULL, INDEX outofrangelatitude_ibfk_1 (latitudeID), PRIMARY KEY(outofrangeID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE outofrangetemp (outofrangeID INT AUTO_INCREMENT NOT NULL, tempID INT NOT NULL, sensorReading DOUBLE PRECISION NOT NULL, createdAt DATETIME DEFAULT current_timestamp() NOT NULL, INDEX outofrangetemp_ibfk_1 (tempID), PRIMARY KEY(outofrangeID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, username VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE room (roomID INT AUTO_INCREMENT NOT NULL, room VARCHAR(20) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, groupNameID INT NOT NULL, INDEX GroupName (groupNameID), PRIMARY KEY(roomID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE sensornames (sensorNameID INT AUTO_INCREMENT NOT NULL, createdBy INT NOT NULL, sensorName VARCHAR(20) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, deviceNameID INT NOT NULL, sensorTypeID INT NOT NULL, INDEX SensorType (sensorTypeID), INDEX sensornames_ibfk_1 (deviceNameID), INDEX sensornames_ibfk_2 (createdBy), PRIMARY KEY(sensorNameID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE sensortype (sensorTypeID INT AUTO_INCREMENT NOT NULL, sensorType VARCHAR(20) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, description VARCHAR(50) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, UNIQUE INDEX sensorType (sensorType), PRIMARY KEY(sensorTypeID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE soil (soilID INT AUTO_INCREMENT NOT NULL, analogID INT NOT NULL, sensorNameID INT NOT NULL, UNIQUE INDEX cardViewID (sensorNameID), UNIQUE INDEX analogID (analogID), PRIMARY KEY(soilID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE temp (tempID INT AUTO_INCREMENT NOT NULL, sensorNameID INT DEFAULT NULL, tempReading DOUBLE PRECISION NOT NULL, highTemp DOUBLE PRECISION DEFAULT \'26\' NOT NULL, lowTemp DOUBLE PRECISION DEFAULT \'12\' NOT NULL, constRecord TINYINT(1) DEFAULT \'0\' NOT NULL, updatedAt DATETIME DEFAULT current_timestamp() NOT NULL, UNIQUE INDEX sensorNameID (sensorNameID), PRIMARY KEY(tempID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE user (userID INT AUTO_INCREMENT NOT NULL, firstName VARCHAR(20) NOT NULL, lastName VARCHAR(20) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, profilePic VARCHAR(100) CHARACTER SET utf8mb3 DEFAULT \'\'\'/assets/pictures/guest.jpg\'\'\', password LONGTEXT CHARACTER SET utf8mb3 NOT NULL, salt LONGTEXT CHARACTER SET utf8mb3 DEFAULT NULL, groupNameID INT DEFAULT NULL, createdAt DATETIME DEFAULT current_timestamp() NOT NULL, INDEX GroupName (groupNameID), UNIQUE INDEX email (email), PRIMARY KEY(userID)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');

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
            INSERT INTO `cardcolour` 
                (`colourID`, `colour`, `shade`) 
            VALUES
                (1, 'danger', 'red'),
                (2, 'success', 'green'),
                (3, 'warning', 'Yellow'),
                (4, 'primary', 'blue');
        ");

        $this->addSql("
            INSERT INTO `cardstate` 
                (`cardStateID`, `state`) 
            VALUES
                (3, 'DEVICE_ONLY'),
                (2, 'OFF'),
                (1, 'ON'),
                (4, 'ROOM_ONLY');
        ");

        $this->addSql("
            INSERT INTO `groupname` 
                (`groupNameID`, `groupName`, `createdAt`) 
            VALUES
                (1, 'admin', '2021-06-06 02:54:58');
        ");

        $this->addSql("
            INSERT INTO `groupnnamemapping` 
                (`groupNameMappingID`, `userID`, `groupNameID`) 
            VALUES
                (1, 1, 1);
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

        $this->addSql("
            INSERT INTO `user` 
                (`userID`, `firstName`, `lastName`, `email`, `roles`, `profilePic`, `password`, `salt`, `groupNameID`, `createdAt`) 
            VALUES
                (1, 'admin', 'admin', 'admin', '[\"ROLE_ADMIN\"]', '/assets/pictures/users/guest.jpg', '\$argon2id\$v=19\$m=65536,t=4,p=1\$7zx+pasSn547DYfLgO9MuQ\$ACTjDqrmJDgB9KfoZUOpESDZn/071R/Bmfju9o+R1Zw', NULL, 1, '2021-07-15 17:19:32');
        ");
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE readingtypes');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE analog');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE bmp');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE cardcolour');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE cardstate');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE cardview');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE constanalog');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE consthumid');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE constlatitude');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE consttemp');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE dallas');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE devicenames');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE dhtsensor');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE groupname');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE groupnnamemapping');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE humid');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE icons');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE latitude');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE migration_versions');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE outofrangeanalog');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE outofrangehumid');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE outofrangelatitude');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE outofrangetemp');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE refresh_tokens');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE room');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE sensornames');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE sensortype');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE soil');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE temp');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE user');
    }
}
