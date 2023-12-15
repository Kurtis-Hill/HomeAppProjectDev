<?php

namespace App\Sensors\Entity\ReadingTypes\BoolReadingTypes;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Entity;

#[Entity(
//    repositoryClass: RelayRepository::class
)]
class Relay extends AbstractBoolReadingBaseSensor implements AllSensorReadingTypeInterface //implements RelayReadingTypeInterface//, AllSensorReadingTypeInterface
{
    public const READING_TYPE = 'relay';

    public static function getReadingTypeName(): string
    {
        return self::READING_TYPE;
    }

    public function getReadingType(): string
    {
        return self::READING_TYPE;
    }

    public function setUpdatedAt(): void
    {
        // TODO: Implement setUpdatedAt() method.
    }
}
