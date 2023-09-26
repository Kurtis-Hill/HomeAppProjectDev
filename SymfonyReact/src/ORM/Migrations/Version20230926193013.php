<?php

declare(strict_types=1);

namespace App\ORM\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230926193013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Triggering system update';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE `operators`
                (
                `operatorID` INT NOT NULL AUTO_INCREMENT,
                `operatorName` VARCHAR(255) NOT NULL,
                `operatorSymbol` VARCHAR(255) NOT NULL,
                `operatorDescription` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`operatorID`),
                UNIQUE INDEX `operatorName` (`operatorName`),
                UNIQUE INDEX `operatorSymbol` (`operatorSymbol`)
            )'
        );

        $this->addSql(
            'INSERT INTO `operators` (`operatorID`, `operatorName`, `operatorSymbol`, `operatorDescription`) VALUES
                (1, \'Equals\', \'==\', \'Equal to value\'),
                (2, \'Not Equals\', \'!=\', \'Not Equal to value\'),
                (3, \'Greater Than\', \'>\', \'Greater Than the value\'),
                (4, \'Less Than\', \'<\', \'Less Than the value\'),
                (5, \'Greater Than Or Equal To\', \'>=\', \'Greater Than Or Equal To the value\'),
                (6, \'Less Than Or Equal To\', \'<=\', \'Less Than Or Equal To the value\')'
        );

        $this->addSql(
            'CREATE TABLE `sensortrigger` 
                (
                    `sensorTriggerID` INT NOT NULL AUTO_INCREMENT,
                    `sensorID` INT NOT NULL,
                    `sensorToTriggerID` INT NOT NULL,
                    `valueThatTriggers` VARCHAR(255) NOT NULL,
                    `operatorID` INT NOT NULL,                    
                    `createdBy` INT NOT NULL,                    
                    `createdAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`sensorTriggerID`),
                    INDEX `IDX_1F9B6F4F8D93D649` (`sensorID`),
                    INDEX `IDX_1F9B6F4F8D93D6492` (`sensorToTriggerID`),
                    INDEX `IDX_1F9B6F4F8D93D6493` (`operatorID`),
                    CONSTRAINT `FK_1F9B6F4F8D93D649` FOREIGN KEY (`sensorID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `FK_1F9B6F4F8D93D6492` FOREIGN KEY (`sensorToTriggerID`) REFERENCES `sensors` (`sensorID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `FK_1F9B6F4F8D93D6493` FOREIGN KEY (`operatorID`) REFERENCES `operators` (`operatorID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `FK_1F9B6F4F8D93D6494` FOREIGN KEY (`createdBy`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
                )'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `sensortrigger`');
        $this->addSql('DROP TABLE `operators`');
    }
}
