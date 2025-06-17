<?php

namespace App\Entity\Sensor\SensorTypes;

use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Repository\Sensor\SensorType\ORM\DallasRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: DallasRepository::class),
]
class Dallas extends AbstractSensorType implements StandardSensorTypeInterface, TemperatureReadingTypeInterface
{
    public const NAME = 'Dallas';

    public const ALIAS = 'dallas';

    public const HIGH_TEMPERATURE_READING_BOUNDARY = 125;

    public const LOW_TEMPERATURE_READING_BOUNDARY = -55;

    private const ALLOWED_READING_TYPES = [
        Temperature::READING_TYPE
    ];

    public function getMaxTemperature(): float|int
    {
        return self::HIGH_TEMPERATURE_READING_BOUNDARY;
    }

    public function getMinTemperature(): float|int
    {
        return self::LOW_TEMPERATURE_READING_BOUNDARY;
    }

    public static function getSensorTypeName(): string
    {
        return self::NAME;
    }

    public static function getAllowedReadingTypes(): array
    {
        return self::ALLOWED_READING_TYPES;
    }
}
