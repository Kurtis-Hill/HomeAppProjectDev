<?php

namespace App\Sensors\Entity\ReadingTypes\BoolReadingTypes;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Repository\ReadingType\ORM\MotionRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Entity;

#[
    Entity(repositoryClass: MotionRepository::class),
]
class Motion extends AbstractBoolReadingBaseSensor
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
