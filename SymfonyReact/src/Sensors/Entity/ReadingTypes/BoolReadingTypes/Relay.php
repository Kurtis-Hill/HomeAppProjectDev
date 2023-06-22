<?php

namespace App\Sensors\Entity\ReadingTypes\BoolReadingTypes;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use Doctrine\ORM\Mapping\Entity;

#[Entity(
//    repositoryClass: RelayRepository::class
)]
class Relay extends AbstractBoolReadingType implements AllSensorReadingTypeInterface //implements RelayReadingTypeInterface//, AllSensorReadingTypeInterface
{
    public const READING_TYPE = 'relay';

    public function getSensorID(): int
    {
        return $this->getSensor()->getSensorID();
    }

    public function setSensorID(int $id)
    {
        return $this->boolID = $id;
    }
}
