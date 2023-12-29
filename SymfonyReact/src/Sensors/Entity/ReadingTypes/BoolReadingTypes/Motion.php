<?php

namespace App\Sensors\Entity\ReadingTypes\BoolReadingTypes;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Entity;

#[
//    Entity(repositoryClass: MotionRepository::class),
    Entity(),
//    ORM\Table(name: 'motion'),
]
class Motion extends AbstractBoolReadingBaseSensor //implements AllSensorReadingTypeInterface //, AllSensorReadingTypeInterface
{
    public const READING_TYPE = 'motion';

    public static function getReadingTypeName(): string
    {
        return self::READING_TYPE;
    }

    public function getReadingType(): string
    {
        return self::READING_TYPE;
    }
}
