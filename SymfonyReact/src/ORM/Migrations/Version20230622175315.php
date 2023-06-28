<?php

declare(strict_types=1);

namespace App\ORM\Migrations;

use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230622175315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding bool type sensors';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql(
            'CREATE TABLE boolsensor (
                boolID INT AUTO_INCREMENT NOT NULL,
                sensorID INT NOT NULL,
                currentReading TINYINT(1) NOT NULL,
                requestedReading TINYINT(1) NOT NULL,
                expectedReading TINYINT(1) NULL DEFAULT NULL,
                boolReadingType VARCHAR(25) NOT NULL,
                constRecord TINYINT(1) DEFAULT \'0\', 
                createdAt DATETIME NOT NULL,
                updatedAt DATETIME DEFAULT current_timestamp() NOT NULL,
                UNIQUE INDEX UNQ_SENSOR_BOOL (sensorID),
                INDEX IDX_BOOL_SENSOR (sensorID),
                PRIMARY KEY(boolID)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

        $this->addSql(
            'ALTER TABLE boolsensor
                ADD CONSTRAINT FK_BOOL_SENSOR FOREIGN KEY (sensorID) REFERENCES sensors (sensorID) ON DELETE CASCADE'
        );

        $this->addSql(
            'CREATE TABLE genericmotion (
                genericmotionID INT AUTO_INCREMENT NOT NULL,
                motionID INT NOT NULL,
                sensorID INT NOT NULL,
                UNIQUE INDEX UNIQ_GENERIC_MOTION_SENSOR (sensorID),
                UNIQUE INDEX UNIQ_GENERIC_MOTION_2 (motionID),
                PRIMARY KEY (genericmotionID)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

        $this->addSql(
            'ALTER TABLE genericmotion
                ADD CONSTRAINT FK_GENERIC_MOTION_SENSOR FOREIGN KEY (sensorID) REFERENCES sensors (sensorID) ON DELETE CASCADE,
                ADD CONSTRAINT FK_GENERIC_MOTION_MOTION FOREIGN KEY (motionID) REFERENCES boolsensor (boolID) ON DELETE CASCADE'
        );

        $this->addSql(
            'CREATE TABLE genericrelay(
                genericrelayID INT AUTO_INCREMENT NOT NULL,
                relayID INT NOT NULL,
                sensorID INT NOT NULL,
                UNIQUE INDEX UNIQ_GENERIC_RELAY_SENSOR (sensorID),
                UNIQUE INDEX UNIQ_GENERIC_RELAY (relayID),
                PRIMARY KEY (genericrelayID)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

        $this->addSql(
            'ALTER TABLE genericrelay
                ADD CONSTRAINT FK_GENERIC_RELAY_SENSOR FOREIGN KEY (sensorID) REFERENCES sensors (sensorID) ON DELETE CASCADE,
                ADD CONSTRAINT FK_GENERIC_RELAY_RELAY FOREIGN KEY (relayID) REFERENCES boolsensor (boolID) ON DELETE CASCADE'
        );

        $this->addSql("
            INSERT INTO `sensortype` 
                (`sensorType`, `description`)   
            VALUES
                ('" . GenericMotion::NAME . "', 'Generic Motion Sensor'),
                ('" . GenericRelay::NAME . "', 'Generic Relay Sensor')
        ");
//        $this->addSql(
//            'CREATE TABLE relay (
//                relayID INT AUTO_INCREMENT NOT NULL,
//                boolID INT(1) NOT NULL,
//                UNIQUE INDEX UNIQ_RELAY_BOOL (boolID),
//                PRIMARY KEY (relayID)
//            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
//        );
//
//        $this->addSql(
//            'ALTER TABLE relay
//                ADD CONSTRAINT KF_RELAY_BOOL FOREIGN KEY (boolID) REFERENCES boolsensor (boolID) ON DELETE CASCADE'
//        );
//
//        $this->addSql(
//            'CREATE TABLE motion (
//                motionID INT AUTO_INCREMENT NOT NULL,
//                boolID INT(1) NOT NULL,
//                PRIMARY KEY (motionID),
//                UNIQUE INDEX UNIQ_BOOL_MOTION (boolID)
//            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
//        );
//
//        $this->addSql(
//            'ALTER TABLE motion
//                ADD CONSTRAINT FK_MOTION_BOOL FOREIGN KEY (boolID) REFERENCES boolsensor (boolID) ON DELETE CASCADE'
//        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql("SET FOREIGN_KEY_CHECKS=0");
        $this->addSql(
            "DELETE FROM `sensortype` WHERE sensorType = '" . GenericMotion::NAME . "' OR sensorType = '" . GenericRelay::NAME . "'"
        );
        $this->addSql(
            "DROP TABLE boolsensor"
        );

        $this->addSql(
            "DROP TABLE genericmotion"
        );

        $this->addSql(
            "DROP TABLE genericrelay"
        );
        $this->addSql("SET FOREIGN_KEY_CHECKS=1");
    }
}
