<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260528120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add outOfBoundsAlertTimer column to standardreadingtype table with default of 3600 seconds (1 hour)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE standardreadingtype ADD outOfBoundsAlertTimer INT NOT NULL DEFAULT 3600'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'ALTER TABLE standardreadingtype DROP COLUMN outOfBoundsAlertTimer'
        );
    }
}
