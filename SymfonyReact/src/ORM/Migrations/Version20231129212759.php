<?php

declare(strict_types=1);

namespace App\ORM\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231129212759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'this migration is to integrate the standardReadingType table into the temperature, humidity, analog and latitude tables and map the ids from standardReadingType to the corresponding tables';
    }

    public function up(Schema $schema): void
    {
        $allTemperatureEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM standardReadingType WHERE readingType = 'temperature'
        ");
        foreach ($allTemperatureEntities as $temperatureEntity) {
            $this->addSql("
                UPDATE temperature
                SET readingTypeID = {$temperatureEntity['readingTypeID']}
                WHERE sensorID = {$temperatureEntity['sensorID']};
            ");
        }

        $this->addSql("
            ALTER TABLE `temperature`
                DROP FOREIGN KEY IF EXISTS `FK_B5385CA3BE475E6A`,
                DROP COLUMN IF EXISTS `sensorID`;                
        ");

        //select all humidity from standReadingType table and map standardReadingType IDs's as readingTypeID on humidity table
        $allHumidityEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM standardReadingType WHERE readingType = 'humidity'
        ");
        foreach ($allHumidityEntities as $humidityEntity) {
            $this->addSql("
                UPDATE humidity
                SET readingTypeID = {$humidityEntity['readingTypeID']}
                WHERE sensorID = {$humidityEntity['sensorID']};
            ");
        }

        $this->addSql("
            ALTER TABLE `humidity`
                DROP FOREIGN KEY IF EXISTS `humid_ibfk_1`,
                DROP COLUMN IF EXISTS  `sensorID`;
        ");

        //select all analog from standReadingType table and map standardReadingType IDs's as readingTypeID on analog table
        $allAnalogEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM standardReadingType WHERE readingType = 'analog'
        ");
        foreach ($allAnalogEntities as $analogEntity) {
            $this->addSql("
                UPDATE analog
                SET readingTypeID = {$analogEntity['readingTypeID']}
                WHERE sensorID = {$analogEntity['sensorID']};
            ");
        }

        $this->addSql("
            ALTER TABLE `analog`
                DROP FOREIGN KEY IF EXISTS  `analog_ibfk_3`,
                DROP COLUMN IF EXISTS `sensorID`;
        ");

        //select all latitude from standReadingType table and map standardReadingType IDs's as readingTypeID on latitude table
        $allLatitudeEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM standardReadingType WHERE readingType = 'latitude'
        ");

        foreach ($allLatitudeEntities as $latitudeEntity) {
            $this->addSql("
                UPDATE latitude
                SET readingTypeID = {$latitudeEntity['readingTypeID']}
                WHERE sensorID = {$latitudeEntity['sensorID']};
            ");
        }

        $this->addSql("
            ALTER TABLE `latitude`
                DROP FOREIGN KEY IF EXISTS `latitude_ibfk_4`,
                DROP COLUMN IF EXISTS `sensorID`;
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE `temperature`
                ADD COLUMN IF NOT EXISTS `sensorID` INT(11) DEFAULT NULL AFTER `readingTypeID`,
                ADD CONSTRAINT `FK_B5385CA3BE475E6A` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ');

        $allTemperatureEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM standardReadingType WHERE readingType = 'temperature'
        ");
        foreach ($allTemperatureEntities as $temperatureEntity) {
            $this->addSql("
                UPDATE temperature
                SET sensorID = {$temperatureEntity['sensorID']}
                WHERE readingTypeID = {$temperatureEntity['readingTypeID']};
            ");
        }

        $this->addSql('
            ALTER TABLE `humidity`
                ADD COLUMN IF NOT EXISTS `sensorID` INT(11) DEFAULT NULL AFTER `readingTypeID`,
                ADD CONSTRAINT `humid_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ');

        $allHumidityEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM standardReadingType WHERE readingType = 'humidity'
        ");
        foreach ($allHumidityEntities as $humidityEntity) {
            $this->addSql("
                UPDATE humidity
                SET sensorID = {$humidityEntity['sensorID']}
                WHERE readingTypeID = {$humidityEntity['readingTypeID']};
            ");
        }

        $this->addSql('
            ALTER TABLE `analog`
                ADD COLUMN IF NOT EXISTS `sensorID` INT(11) DEFAULT NULL AFTER `readingTypeID`,
                ADD CONSTRAINT `analog_ibfk_3` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ');

        $allAnalogEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM standardReadingType WHERE readingType = 'analog'
        ");
        foreach ($allAnalogEntities as $analogEntity) {
            $this->addSql("
                UPDATE analog
                SET sensorID = {$analogEntity['sensorID']}
                WHERE readingTypeID = {$analogEntity['readingTypeID']};
            ");
        }

        $this->addSql('
            ALTER TABLE `latitude`
                ADD COLUMN IF NOT EXISTS `sensorID` INT(11) DEFAULT NULL AFTER `readingTypeID`,
                ADD CONSTRAINT `latitude_ibfk_4` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ');

        $allLatitudeEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM standardReadingType WHERE readingType = 'latitude'
        ");
        foreach ($allLatitudeEntities as $latitudeEntity) {
            $this->addSql("
                UPDATE latitude
                SET sensorID = {$latitudeEntity['sensorID']}
                WHERE readingTypeID = {$latitudeEntity['readingTypeID']};
            ");
        }
    }
}
