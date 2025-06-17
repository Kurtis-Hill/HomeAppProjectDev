<?php

namespace App\Entity\Sensor\SensorTypes;

use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Repository\Sensor\SensorType\ORM\ShtRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: ShtRepository::class),
]
class Sht extends AbstractSensorType implements StandardSensorTypeInterface, TemperatureReadingTypeInterface, HumidityReadingTypeInterface
{
    public const NAME = 'Sht';

    public const ALIAS = 'sht';

    public const HIGH_TEMPERATURE_READING_BOUNDARY = 125;

    public const LOW_TEMPERATURE_READING_BOUNDARY = -40;

    private const ALLOWED_READING_TYPES = [
        Temperature::READING_TYPE,
        Humidity::READING_TYPE,
    ];


    public function getMaxTemperature(): float|int
    {
        return self::HIGH_TEMPERATURE_READING_BOUNDARY;
    }

    public function getMinTemperature(): float|int
    {
        return self::LOW_TEMPERATURE_READING_BOUNDARY;
    }

    public function getMaxHumidity(): float|int
    {
        return Humidity::HIGH_READING;
    }

    public function getMinHumidity(): float|int
    {
        return Humidity::LOW_READING;
    }

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
