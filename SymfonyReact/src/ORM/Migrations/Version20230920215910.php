<?php

declare(strict_types=1);

namespace App\ORM\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230920215910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'adding new icons to the database';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
        INSERT INTO `icons` 
            (`iconID`, `iconName`, `description`) 
        VALUES
            (28, 'couch', 'couch/sofa'),
            (29, 'sun', 'the sun'),
            (30, 'frog', 'frog from the side'),
            (31, 'water', 'water 3 lines'),
            (32, 'temperature-low', 'thermometer on low'),
            (33, 'temperature-high', 'thermometer on high'),
            (34, 'house-user', 'house with user icon'),
            (35, 'shower', 'shower head'),
            (36, 'fan', 'fan blades'),
            (37, 'lightbulb', 'light bulb');
        ");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("
            DELETE FROM `icons` 
                WHERE `iconID` IN (28, 29, 30, 31, 32, 33, 34, 35, 36, 37);
        ");

    }
}
