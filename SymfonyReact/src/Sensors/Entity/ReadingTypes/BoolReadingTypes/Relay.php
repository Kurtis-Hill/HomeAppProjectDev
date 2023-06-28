<?php

namespace App\Sensors\Entity\ReadingTypes\BoolReadingTypes;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use Doctrine\ORM\Mapping\Entity;

#[Entity(
//    repositoryClass: RelayRepository::class
)]
class Relay extends AbstractBoolReadingSensor implements AllSensorReadingTypeInterface, BoolReadingSensorInterface //implements RelayReadingTypeInterface//, AllSensorReadingTypeInterface
{
    public const READING_TYPE = 'relay';

    public function getSensorID(): int
    {
        return $this->getSensor()->getSensorID();
    }

    public function setSensorID(int $id): void
    {
        $this->boolID = $id;
    }

    public static function getReadingTypeName(): string
    {
        return self::READING_TYPE;
    }

    public function getReadingType(): string
    {
        return self::READING_TYPE;
    }
}
