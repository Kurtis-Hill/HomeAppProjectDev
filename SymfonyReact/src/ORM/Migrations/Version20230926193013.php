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
            'CREATE TABLE `operators` (
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
            'CREATE TABLE `triggertype` (
                `triggerTypeID` INT NOT NULL AUTO_INCREMENT,
                `triggerTypeName` VARCHAR(255) NOT NULL,
                `triggerTypeDescription` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`triggerTypeID`),
                UNIQUE INDEX `triggerTypeName` (`triggerTypeName`)
            )'
        );

        $this->addSql(
            'INSERT INTO `triggertype` (`triggerTypeID`, `triggerTypeName`, `triggerTypeDescription`) VALUES
                (1, \'Email\', \'Send an email\'),
                (2, \'Relay Up\', \'Turn relay on\'),
                (3, \'Relay Down\', \'Turn relay off\')'
        );

        $this->addSql(
            'CREATE TABLE `sensortrigger` 
                (
                    `sensorTriggerID` INT NOT NULL AUTO_INCREMENT,
                    `baseReadingTypeThatTriggers` INT NOT NULL,
                    `baseReadingTypeToTriggerID` INT NOT NULL,
                    `triggerTypeID` INT NOT NULL,
                    `valueThatTriggers` VARCHAR(255) NOT NULL,                
                    `operatorID` INT NOT NULL,                    
                    `createdBy` INT NOT NULL,                 
                    `startTime` INT NULL,
                    `endTime` INT NULL,
                    `monday` BOOLEAN DEFAULT TRUE,
                    `tuesday` BOOLEAN DEFAULT TRUE,
                    `wednesday` BOOLEAN DEFAULT TRUE,
                    `thursday` BOOLEAN DEFAULT TRUE,
                    `friday` BOOLEAN DEFAULT TRUE,
                    `saturday` BOOLEAN DEFAULT TRUE,
                    `sunday` BOOLEAN DEFAULT TRUE, 
                    `override` BOOLEAN DEFAULT FALSE, 
                    `createdAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX `baseReadingTypeThatTriggers` (`baseReadingTypeThatTriggers`),
                    INDEX `baseReadingTypeToTriggerID` (`baseReadingTypeToTriggerID`),
                    INDEX `triggerTypeID` (`triggerTypeID`),
                    INDEX `operatorID` (`operatorID`),
                    INDEX `createdBy` (`createdBy`),
                    INDEX `startTime` (`startTime`),
                    INDEX `endTime` (`endTime`),
                    INDEX `monday` (`monday`),
                    INDEX `tuesday` (`tuesday`),
                    INDEX `wednesday` (`wednesday`),
                    INDEX `thursday` (`thursday`),
                    INDEX `friday` (`friday`),
                    INDEX `saturday` (`saturday`),
                    INDEX `sunday` (`sunday`),
                    INDEX `override` (`override`),
                    PRIMARY KEY (`sensorTriggerID`)
                )
                DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' 
        '
        );

        $this->addSql("
            ALTER TABLE `sensortrigger`
                ADD CONSTRAINT `FK_1F9B6F4F8D93D6494` FOREIGN KEY (`operatorID`) REFERENCES `operators` (`operatorID`) ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT `FK_1F9B6F4F8D93D6495` FOREIGN KEY (`triggerTypeID`) REFERENCES `triggertype` (`triggerTypeID`) ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT `FK_1F9B6F4F8D93D6498` FOREIGN KEY (`createdBy`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT `FK_1F9B6F4F8D93D6497` FOREIGN KEY (baseReadingTypeToTriggerID) REFERENCES `basereadingtype` (`baseReadingTypeID`) ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT `FK_1F9B6F4F8D93D6496` FOREIGN KEY (`baseReadingTypeThatTriggers`) REFERENCES `basereadingtype` (`baseReadingTypeID`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `sensortrigger`');
        $this->addSql('DROP TABLE `triggertype`');
        $this->addSql('DROP TABLE `operators`');
    }
}
