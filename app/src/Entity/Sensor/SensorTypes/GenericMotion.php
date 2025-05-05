<?php

namespace App\Entity\Sensor\SensorTypes;

use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Repository\Sensor\SensorType\ORM\GenericMotionRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: GenericMotionRepository::class),
]

class GenericMotion extends AbstractSensorType implements MotionSensorReadingTypeInterface, BoolSensorTypeInterface
{
    public const NAME = 'GenericMotion';

    public const ALIAS = 'genericMotion';

    private const ALLOWED_READING_TYPES = [
        Motion::READING_TYPE,
    ];

    public static function getSensorTypeName(): string
    {
        return self::NAME;
    }

    public static function getReadingTypeAlias(): string
    {
        return self::ALIAS;
    }

    public static function getAllowedReadingTypes(): array
    {
        return self::ALLOWED_READING_TYPES;
    }
}
