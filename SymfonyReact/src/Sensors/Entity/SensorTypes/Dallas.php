<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\AbstractSensorType;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Sensors\Repository\SensorType\ORM\DallasRepository;
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

    public static function getReadingTypeName(): string
    {
        return self::NAME;
    }
}
