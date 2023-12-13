<?php

namespace App\Sensors\Entity\ReadingTypes\BoolReadingTypes;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Entity;

#[Entity(
//    repositoryClass: RelayRepository::class
)]
class Relay extends AbstractBoolReadingBaseSensor implements AllSensorReadingTypeInterface, BoolReadingSensorInterface //implements RelayReadingTypeInterface//, AllSensorReadingTypeInterface
{
    public const READING_TYPE = 'relay';

    public function getSensorID(): int
    {
        return $this->getSensor()->getSensorID();
    }

//    public function setSensorID(int $id): void
//    {
//        $this->boolID = $id;
//    }

    public static function getReadingTypeName(): string
    {
        return self::READING_TYPE;
    }

    public function getReadingType(): string
    {
        return self::READING_TYPE;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->getSensor()->getCreatedAt();
    }

    public function setCreatedAt(DateTimeInterface $createdAt)
    {
        $this->getSensor()->setCreatedAt($createdAt);
    }

    public function setUpdatedAt(): void
    {
        // TODO: Implement setUpdatedAt() method.
    }
}
