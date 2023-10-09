<?php

declare(strict_types=1);

namespace App\ORM\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230928192822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'intergration of SHT sensor type';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE sht (
                shtID INT AUTO_INCREMENT NOT NULL, 
                tempID INT NOT NULL, 
                humidID INT NOT NULL, 
                sensorID INT NOT NULL,
                UNIQUE INDEX sensorID (sensorID), 
                UNIQUE INDEX tempID (tempID), 
                UNIQUE INDEX humidID (humidID), 
                PRIMARY KEY(shtID)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        ');

        $this->addSql("
            ALTER TABLE `sht`
              ADD CONSTRAINT `shtsensor_ibfk_1` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `shtsensor_ibfk_2` FOREIGN KEY (`humidID`) REFERENCES `humidity` (`humidID`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `shtsensor_ibfk_3` FOREIGN KEY (`tempID`) REFERENCES `temperature` (`tempID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $this->addSql("
            INSERT INTO `sensortype` 
                (`sensorTypeID`, `sensorType`, `description`) 
            VALUES
                (8, 'Sht', 'High Accuracy Temperature and Humidity Sensor');
            
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("SET FOREIGN_KEY_CHECKS = 0;");
        $this->addSql('DROP TABLE sht');
        $this->addSql('DELETE FROM sensortype WHERE sensorTypeID = 8');
        $this->addSql("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
