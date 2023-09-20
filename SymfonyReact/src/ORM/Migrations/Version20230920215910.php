<?php

declare(strict_types=1);

namespace App\ORM\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
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
            (31, 'water', 'water 3 lines');
            (32, 'temperature-low', 'thermometer on low'),
            (32, 'temperature-high', 'thermometer on high'),
            (32, 'house-user', 'house with user icon'),
            (32, 'shower', 'shower head'),
            (32, 'fan', 'fan blades'),
            (32, 'lightbulb', 'light bulb'),
        ");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
