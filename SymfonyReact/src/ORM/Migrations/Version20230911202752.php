<?php

declare(strict_types=1);

namespace App\ORM\Migrations;

use App\Sensors\Entity\SensorTypes\LDR;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230911202752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add LDR support';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            INSERT INTO `sensortype`
                (`sensorTypeID`, `sensorType`, `description`)
            VALUES
                (7, '" . LDR::NAME . "', 'Light resistor sensor');
        ");

        $this->addSql("
            CREATE TABLE ldr (
                ldrID INT AUTO_INCREMENT NOT NULL,
                analogID INT NOT NULL,
                sensorID INT NOT NULL,
                UNIQUE KEY analogID (analogID),
                UNIQUE KEY sensorID (sensorID),
                PRIMARY KEY (ldrID)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->addSql("
            ALTER TABLE `ldr`
                ADD CONSTRAINT `ldr_ibfk_1` FOREIGN KEY (`analogID`) REFERENCES `analog` (`analogID`) ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT `ldr_ibfk_2` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("
            DELETE FROM `sensortype` WHERE `sensorType` = '". LDR::NAME . "';
        ");

        $this->addSql("
            DROP TABLE ldr;
        ");
    }
}
