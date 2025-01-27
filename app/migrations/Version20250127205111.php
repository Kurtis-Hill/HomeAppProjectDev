<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127205111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create led table and WS218B led sensor type';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE led (
                ledID INT AUTO_INCREMENT NOT NULL, 
                baseReadingTypeID INT NOT NULL, 
                currentReading JSON NOT NULL,
                presets JSON NOT NULL,
                selectedPreset INT NOT NULL, 
                ledReadingType VARCHAR(255) NOT NULL, 
                PRIMARY KEY(ledID),
                CONSTRAINT FK_2D3A3D3D4A3A3D3 FOREIGN KEY (baseReadingTypeID) REFERENCES basereadingtype (baseReadingTypeID)
             ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('INSERT INTO sensortype (sensorType, description) VALUES (\'WS2812B\', \'WS2812B-2020 is an intelligent control LED light source, its exterior adopts the latest MOLDING packaging technology, the control circuit and RGB chips are integrated in a package of 2020 component.\')');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE led');
        $this->addSql('DELETE FROM sensortype WHERE sensorType = \'WS2812B\'');
    }
}
