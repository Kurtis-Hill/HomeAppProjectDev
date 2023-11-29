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
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // this up() migration is auto-generated, please modify it to your needs
        $allTemperatureEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM standardReadingType WHERE readingType = 'temperature'
        ");
        foreach ($allTemperatureEntities as $temperatureEntity) {
            $this->addSql("
                UPDATE temperature
                SET readingTypeID = {$temperatureEntity['readingTypeID']}
                WHERE sensorID = {$temperatureEntity['readingTypeID']};
            ");
        }

        $this->addSql("
            ALTER TABLE `temperature`
                DROP COLUMN `sensorID`;                
        ");

        //select all humidity from standReadingType table and map standardReadingType IDs's as readingTypeID on humidity table
        $allHumidityEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM standardReadingType WHERE readingType = 'humidity'
        ");
        foreach ($allHumidityEntities as $humidityEntity) {
            $this->addSql("
                UPDATE humidity
                SET readingTypeID = {$humidityEntity['readingTypeID']}
                WHERE sensorID = {$humidityEntity['readingTypeID']};
            ");
        }

        $this->addSql("
            ALTER TABLE `humidity`
                DROP COLUMN `sensorID`;
        ");

        //select all analog from standReadingType table and map standardReadingType IDs's as readingTypeID on analog table
        $allAnalogEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM standardReadingType WHERE readingType = 'analog'
        ");
        foreach ($allAnalogEntities as $analogEntity) {
            $this->addSql("
                UPDATE analog
                SET readingTypeID = {$analogEntity['readingTypeID']}
                WHERE sensorID = {$analogEntity['readingTypeID']};
            ");
        }

        $this->addSql("
            ALTER TABLE `analog`
                DROP COLUMN `sensorID`;
        ");

        //select all latitude from standReadingType table and map standardReadingType IDs's as readingTypeID on latitude table
        $allLatitudeEntities = $this->connection->fetchAllAssociative("
            SELECT * FROM standardReadingType WHERE readingType = 'latitude'
        ");

        foreach ($allLatitudeEntities as $latitudeEntity) {
            $this->addSql("
                UPDATE latitude
                SET readingTypeID = {$latitudeEntity['readingTypeID']}
                WHERE sensorID = {$latitudeEntity['readingTypeID']};
            ");
        }

        $this->addSql("
            ALTER TABLE `latitude`
                DROP COLUMN `sensorID`;
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
