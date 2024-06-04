<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\AbstractSensorType;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Repository\SensorType\ORM\SoilRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: SoilRepository::class),
]
class Soil extends AbstractSensorType implements StandardSensorTypeInterface, AnalogReadingTypeInterface
{
    public const NAME = 'Soil';

    public const ALIAS = 'soil';

    public const HIGH_SOIL_READING_BOUNDARY = 9999;

    public const LOW_SOIL_READING_BOUNDARY = 1000;

    private const ALLOWED_READING_TYPES = [
        Analog::READING_TYPE
    ];

    public function getMaxAnalog(): float|int
    {
        return self::HIGH_SOIL_READING_BOUNDARY;
    }

    public function getMinAnalog(): float|int
    {
        return self::LOW_SOIL_READING_BOUNDARY;
    }

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
