<?php

namespace App\Sensors\Entity\ReadingTypes\BoolReadingTypes;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use Doctrine\ORM\Mapping\Entity;

#[
//    Entity(repositoryClass: MotionRepository::class),
    Entity(),
//    ORM\Table(name: 'motion'),
]
class Motion extends AbstractBoolReadingType implements AllSensorReadingTypeInterface //, AllSensorReadingTypeInterface
{
    public const READING_TYPE = 'motion';

    public function getSensorID(): int
    {
        return $this->getSensor()->getSensorID();
    }

    public function setSensorID(int $id)
    {
        return $this->boolID = $id;
    }

}
