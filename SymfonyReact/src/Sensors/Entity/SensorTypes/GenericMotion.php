<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\AbstractSensorType;
use App\Sensors\Entity\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: GenericMotion::class),
]

class GenericMotion extends AbstractSensorType implements MotionSensorReadingTypeInterface, BoolSensorTypeInterface
{
    public const NAME = 'GenericMotion';

    public const ALIAS = 'genericMotion';

    private const ALLOWED_READING_TYPES = [
        Motion::READING_TYPE,
    ];

    public static function getReadingTypeName(): string
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
