<?php

declare(strict_types=1);

namespace App\ORM\Migrations;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231128233517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `standardReadingType`
                (
                    `readingTypeID` INT AUTO_INCREMENT NOT NULL,
                    `sensorID` INT NOT NULL,
                    `currentReading` DOUBLE PRECISION NOT NULL,
                    `highReading` DOUBLE PRECISION NOT NULL,
                    `lowReading` DOUBLE PRECISION NOT NULL,
                    `constRecord` TINYINT(1) NOT NULL,
                    `createdAt` DATETIME NOT NULL,
                    `updatedAt` DATETIME NOT NULL,
                    `readingType` VARCHAR(255) NOT NULL,
                    PRIMARY KEY (`readingTypeID`)
                )             
        ");

        $this->addSql("
            ALTER TABLE `standardReadingType`
                ADD CONSTRAINT `standardReadingType_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $allTemperatureEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM temperature
        ");

        $temperature = Temperature::READING_TYPE;
        foreach ($allTemperatureEntities as $temperatureEntity) {
            $this->addSql("
                INSERT INTO `standardReadingType`
                    (`sensorID`, `readingType`, `currentReading`, `highReading`, `lowReading`, `constRecord`, `createdAt`, `updatedAt`)
                VALUES
                    ({$temperatureEntity['sensorID']}, '$temperature', {$temperatureEntity['tempReading']}, {$temperatureEntity['highTemp']}, {$temperatureEntity['highTemp']}, {$temperatureEntity['constRecord']}, NOW(), '{$temperatureEntity['updatedAt']}');
            ");
        }

        $this->addSql("
            ALTER TABLE `temperature` 
                DROP FOREIGN KEY IF EXISTS `FK_B5385CA3BE475E6`,  
                DROP INDEX IF EXISTS `FK_B5385CA3BE475E6`,
                DROP COLUMN `sensorID`,
                DROP COLUMN `tempReading`,
                DROP COLUMN `highTemp`,
                DROP COLUMN `lowTemp`,
                DROP COLUMN `constRecord`,
                DROP COLUMN `updatedAt`,
                RENAME COLUMN `tempID` TO `readingTypeID`;
        ");

        $allHumidityEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM humidity
        ");

        $humidity = Humidity::READING_TYPE;
        foreach ($allHumidityEntities as $humidityEntity) {
            $this->addSql("
                INSERT INTO `standardReadingType`
                    (`sensorID`, `readingType`, `currentReading`, `highReading`, `lowReading`, `constRecord`, `createdAt`, `updatedAt`)
                VALUES
                    ({$humidityEntity['sensorID']}, '$humidity', {$humidityEntity['humidReading']}, {$humidityEntity['highHumid']}, {$humidityEntity['lowHumid']}, {$humidityEntity['constRecord']}, NOW(), '{$humidityEntity['updatedAt']}');
            ");
        }

        $this->addSql("
            ALTER TABLE `humidity` 
                DROP FOREIGN KEY IF EXISTS `humid_ibfk_1`,
                DROP INDEX IF EXISTS `humid_ibfk_1`,
                DROP COLUMN `sensorID`,
                DROP COLUMN `humidReading`,
                DROP COLUMN `highHumid`,
                DROP COLUMN `lowHumid`,
                DROP COLUMN `constRecord`,
                DROP COLUMN `updatedAt`,
                RENAME COLUMN `humidID` TO `readingTypeID`;
        ");

        $allAnalogEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM analog
        ");

        $analog = Analog::READING_TYPE;
        foreach ($allAnalogEntities as $analogEntity) {
            $this->addSql("
                INSERT INTO `standardReadingType`
                    (`sensorID`, `readingType`, `currentReading`, `highReading`, `lowReading`, `constRecord`, `createdAt`, `updatedAt`)
                VALUES
                    ({$analogEntity['sensorID']}, '$analog', {$analogEntity['analogReading']}, {$analogEntity['highAnalog']}, {$analogEntity['lowAnalog']}, {$analogEntity['constRecord']}, NOW(), '{$analogEntity['updatedAt']}');
            ");
        }

        $this->addSql("
            ALTER TABLE `analog` 
                DROP FOREIGN KEY IF EXISTS `analog_ibfk_3`,
                DROP INDEX IF EXISTS `analog_ibfk_3`,
                DROP COLUMN `sensorID`,
                DROP COLUMN `analogReading`,
                DROP COLUMN `highAnalog`,
                DROP COLUMN `lowAnalog`,
                DROP COLUMN `constRecord`,
                DROP COLUMN `updatedAt`,
                RENAME COLUMN `analogID` TO `readingTypeID`;
        ");

        $allLatitudeEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM latitude
        ");

        $latitude = Latitude::READING_TYPE;
        foreach ($allLatitudeEntities as $latitudeEntity) {
            $this->addSql("
                INSERT INTO `standardReadingType`
                    (`sensorID`, `readingType`, `currentReading`, `highReading`, `lowReading`, `constRecord`, `createdAt`, `updatedAt`)
                VALUES
                    ({$latitudeEntity['sensorID']}, '$latitude', {$latitudeEntity['latitude']}, {$latitudeEntity['highLatitude']}, {$latitudeEntity['lowLatitude']}, {$latitudeEntity['constRecord']}, NOW(), '{$latitudeEntity['updatedAt']}');
            ");
        }

        $this->addSql("
            ALTER TABLE `latitude` 
                DROP FOREIGN KEY IF EXISTS `latitude_ibfk_4`,
                DROP INDEX IF EXISTS `latitude_ibfk_4`,
                DROP COLUMN `sensorID`,
                DROP COLUMN `latitude`,
                DROP COLUMN `highLatitude`,
                DROP COLUMN `lowLatitude`,
                DROP COLUMN `constRecord`,
                DROP COLUMN `updatedAt`,
                RENAME COLUMN `latitudeID` TO `readingTypeID`;
        ");

        $this->addSql("
            ALTER TABLE `sht`
                DROP FOREIGN KEY `shtsensor_ibfk_1`,
                DROP FOREIGN KEY `shtsensor_ibfk_2`,
                DROP FOREIGN KEY `shtsensor_ibfk_3`,
                ADD CONSTRAINT `shtsensor_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT `shtsensor_ibfk_2` FOREIGN KEY (`humidID`) REFERENCES `humidity` (`readingTypeID`) ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT `shtsensor_ibfk_3` FOREIGN KEY (`tempID`) REFERENCES `temperature` (`readingTypeID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    }

    public function down(Schema $schema): void
    {
//        $this->addSql("
//            ALTER TABLE `sht`
//                DROP FOREIGN KEY `shtsensor_ibfk_1`,
//                DROP FOREIGN KEY `shtsensor_ibfk_2`,
//                DROP FOREIGN KEY `shtsensor_ibfk_3`,
//                ADD CONSTRAINT `shtsensor_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE,
//                ADD CONSTRAINT `shtsensor_ibfk_2` FOREIGN KEY (`humidID`) REFERENCES `humidity` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE,
//                ADD CONSTRAINT `shtsensor_ibfk_3` FOREIGN KEY (`tempID`) REFERENCES `temperature` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE;
//        ");
        // this down() migration is auto-generated, please modify it to your needs

        $this->addSql("SET FOREIGN_KEY_CHECKS = 0;");

//        $this->addSql('ALTER TABLE temperature ADD sensorID INT NOT NULL, ADD tempReading DOUBLE PRECISION NOT NULL, ADD highTemp DOUBLE PRECISION NOT NULL, ADD lowTemp DOUBLE PRECISION NOT NULL, ADD constRecord TINYINT(1) NOT NULL, ADD updatedAt DATETIME NOT NULL, RENAME COLUMN readingTypeID TO tempID');
//        $this->addSql('ALTER TABLE temperature ADD CONSTRAINT FK_B5385CA3BE475E6 FOREIGN KEY (sensorID) REFERENCES sensors (sensorID) ON DELETE CASCADE ON UPDATE CASCADE');

        foreach ($this->connection->fetchAllAssociative("SELECT * FROM `standardReadingType` WHERE `readingType` = 'temperature'") as $standardReadingType) {
            $this->addSql("
                INSERT INTO `temperature`
                    (`sensorID`, `tempReading`, `highTemp`, `lowTemp`, `constRecord`, `updatedAt`)
                VALUES
                    ({$standardReadingType['sensorID']}, {$standardReadingType['currentReading']}, {$standardReadingType['highReading']}, {$standardReadingType['lowReading']}, {$standardReadingType['constRecord']}, '{$standardReadingType['updatedAt']}');
            ");
        }

//        $this->addSql('ALTER TABLE humidity ADD sensorID INT NOT NULL, ADD humidReading DOUBLE PRECISION NOT NULL, ADD highHumid DOUBLE PRECISION NOT NULL, ADD lowHumid DOUBLE PRECISION NOT NULL, ADD constRecord TINYINT(1) NOT NULL, ADD updatedAt DATETIME NOT NULL, RENAME COLUMN readingTypeID TO humidID');
//        $this->addSql('ALTER TABLE humidity ADD CONSTRAINT humid_ibfk_1 FOREIGN KEY (sensorID) REFERENCES sensors (sensorID) ON DELETE CASCADE ON UPDATE CASCADE');

        foreach ($this->connection->fetchAllAssociative("SELECT * FROM standardReadingType WHERE readingType = 'humidity'") as $standardReadingType) {
            $this->addSql("
                INSERT INTO `humidity`
                    (`sensorID`, `humidReading`, `highHumid`, `lowHumid`, `constRecord`, `updatedAt`)
                VALUES
                    ({$standardReadingType['sensorID']}, {$standardReadingType['currentReading']}, {$standardReadingType['highReading']}, {$standardReadingType['lowReading']}, {$standardReadingType['constRecord']}, '{$standardReadingType['updatedAt']}');
            ");
        }

//        $this->addSql('ALTER TABLE analog ADD sensorID INT NOT NULL, ADD analogReading DOUBLE PRECISION NOT NULL, ADD highAnalog DOUBLE PRECISION NOT NULL, ADD lowAnalog DOUBLE PRECISION NOT NULL, ADD constRecord TINYINT(1) NOT NULL, ADD updatedAt DATETIME NOT NULL, RENAME COLUMN readingTypeID TO analogID');
//        $this->addSql('ALTER TABLE analog ADD CONSTRAINT analog_ibfk_3 FOREIGN KEY (sensorID) REFERENCES sensors (sensorID) ON DELETE CASCADE ON UPDATE CASCADE');

        foreach ($this->connection->fetchAllAssociative("SELECT * FROM standardReadingType WHERE readingType = 'analog'") as $standardReadingType) {
            $this->addSql("
                INSERT INTO `analog`
                    (`sensorID`, `analogReading`, `highAnalog`, `lowAnalog`, `constRecord`, `updatedAt`)
                VALUES
                    ({$standardReadingType['sensorID']}, {$standardReadingType['currentReading']}, {$standardReadingType['highReading']}, {$standardReadingType['lowReading']}, {$standardReadingType['constRecord']}, '{$standardReadingType['updatedAt']}');
            ");
        }

//        $this->addSql('ALTER TABLE latitude ADD sensorID INT NOT NULL, ADD latitude DOUBLE PRECISION NOT NULL, ADD highLatitude DOUBLE PRECISION NOT NULL, ADD lowLatitude DOUBLE PRECISION NOT NULL, ADD constRecord TINYINT(1) NOT NULL, ADD updatedAt DATETIME NOT NULL, RENAME COLUMN readingTypeID TO latitudeID');
//        $this->addSql('ALTER TABLE latitude ADD CONSTRAINT latitude_ibfk_4 FOREIGN KEY (sensorID) REFERENCES sensors (sensorID) ON DELETE CASCADE ON UPDATE CASCADE');

        foreach ($this->connection->fetchAllAssociative("SELECT * FROM standardReadingType WHERE readingType = 'latitude'") as $standardReadingType) {
            $this->addSql("
                INSERT INTO `latitude`
                    (`sensorID`, `latitude`, `highLatitude`, `lowLatitude`, `constRecord`, `updatedAt`)
                VALUES
                    ({$standardReadingType['sensorID']}, {$standardReadingType['currentReading']}, {$standardReadingType['highReading']}, {$standardReadingType['lowReading']}, {$standardReadingType['constRecord']}, '{$standardReadingType['updatedAt']}');
            ");
        }

        $this->addSql('DROP TABLE IF EXISTS standardReadingType');
        $this->addSql("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
