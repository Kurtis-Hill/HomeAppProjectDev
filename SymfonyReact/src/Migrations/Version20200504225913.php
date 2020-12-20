<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200504225913 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cardview CHANGE sensorNameID sensorNameID INT DEFAULT NULL, CHANGE roomID roomID INT DEFAULT NULL, CHANGE userID userID INT DEFAULT NULL, CHANGE cardIconID cardIconID INT DEFAULT NULL, CHANGE cardColourID cardColourID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE groupname CHANGE timez timez DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE room CHANGE groupNameID groupNameID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sensornames CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE roomID roomID INT DEFAULT NULL, CHANGE sensorTypeID sensorTypeID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE profilePic profilePic VARCHAR(100) DEFAULT \'/assets/pictures/guest.jpg\', CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE timez timez DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE analog CHANGE roomID roomID INT DEFAULT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL, CHANGE analogReading analogReading DOUBLE PRECISION DEFAULT NULL, CHANGE highAnalog highAnalog DOUBLE PRECISION DEFAULT NULL, CHANGE lowAnalog lowAnalog DOUBLE PRECISION DEFAULT NULL, CHANGE cardViewID cardViewID INT DEFAULT NULL, CHANGE timez timez DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE consthumid CHANGE roomID roomID INT DEFAULT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL, CHANGE humidReading humidReading DOUBLE PRECISION DEFAULT NULL, CHANGE timez timez DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE consttemp CHANGE roomID roomID INT DEFAULT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL, CHANGE tempReading tempReading DOUBLE PRECISION DEFAULT NULL, CHANGE timez timez DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE humid CHANGE roomID roomID INT DEFAULT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL, CHANGE cardViewID cardViewID INT DEFAULT NULL, CHANGE timez timez DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE outofrangeanalog CHANGE roomID roomID INT DEFAULT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL, CHANGE analogReading analogReading DOUBLE PRECISION DEFAULT NULL, CHANGE timez timez DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE outofrangehumid CHANGE roomID roomID INT DEFAULT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL, CHANGE humidReading humidReading DOUBLE PRECISION DEFAULT NULL, CHANGE timez timez DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE outofrangetemp CHANGE roomID roomID INT DEFAULT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL, CHANGE tempReadingID tempReadingID DOUBLE PRECISION DEFAULT NULL, CHANGE timez timez DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE temp CHANGE roomID roomID INT DEFAULT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL, CHANGE cardViewID cardViewID INT DEFAULT NULL, CHANGE timez timez DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE analog CHANGE analogReading analogReading DOUBLE PRECISION DEFAULT \'NULL\', CHANGE highAnalog highAnalog DOUBLE PRECISION DEFAULT \'NULL\', CHANGE lowAnalog lowAnalog DOUBLE PRECISION DEFAULT \'NULL\', CHANGE timez timez DATETIME DEFAULT \'current_timestamp()\' NOT NULL, CHANGE roomID roomID INT DEFAULT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL, CHANGE cardViewID cardViewID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cardview CHANGE cardColourID cardColourID INT DEFAULT NULL, CHANGE cardIconID cardIconID INT DEFAULT NULL, CHANGE roomID roomID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL, CHANGE userID userID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE consthumid CHANGE humidReading humidReading DOUBLE PRECISION DEFAULT \'NULL\', CHANGE timez timez DATETIME DEFAULT \'current_timestamp()\' NOT NULL, CHANGE roomID roomID INT DEFAULT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE consttemp CHANGE tempReading tempReading DOUBLE PRECISION DEFAULT \'NULL\', CHANGE timez timez DATETIME DEFAULT \'current_timestamp()\' NOT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE roomID roomID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE groupname CHANGE timez timez DATETIME DEFAULT \'current_timestamp()\' NOT NULL');
        $this->addSql('ALTER TABLE humid CHANGE timez timez DATETIME DEFAULT \'current_timestamp()\' NOT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE roomID roomID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL, CHANGE cardViewID cardViewID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE outofrangeanalog CHANGE analogReading analogReading DOUBLE PRECISION DEFAULT \'NULL\', CHANGE timez timez DATETIME DEFAULT \'current_timestamp()\' NOT NULL, CHANGE roomID roomID INT DEFAULT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE outofrangehumid CHANGE humidReading humidReading DOUBLE PRECISION DEFAULT \'NULL\', CHANGE timez timez DATETIME DEFAULT \'current_timestamp()\' NOT NULL, CHANGE roomID roomID INT DEFAULT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE outofrangetemp CHANGE tempReadingID tempReadingID DOUBLE PRECISION DEFAULT \'NULL\', CHANGE timez timez DATETIME DEFAULT \'current_timestamp()\' NOT NULL, CHANGE roomID roomID INT DEFAULT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room CHANGE groupNameID groupNameID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sensornames CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE roomID roomID INT DEFAULT NULL, CHANGE sensorTypeID sensorTypeID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE temp CHANGE timez timez DATETIME DEFAULT \'current_timestamp()\' NOT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL, CHANGE roomID roomID INT DEFAULT NULL, CHANGE sensorNameID sensorNameID INT DEFAULT NULL, CHANGE cardViewID cardViewID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE profilePic profilePic VARCHAR(100) CHARACTER SET utf8 DEFAULT \'\'\'/assets/pictures/guest.jpg\'\'\' COLLATE `utf8_general_ci`, CHANGE timez timez DATETIME DEFAULT \'current_timestamp()\' NOT NULL, CHANGE groupNameID groupNameID INT DEFAULT NULL');
    }
}
