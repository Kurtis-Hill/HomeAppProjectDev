<?php

namespace App\Entity\Sensor\SensorTypes;

use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Repository\Sensor\SensorType\ORM\BmpRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: BmpRepository::class),
]
class Bmp extends AbstractSensorType implements StandardSensorTypeInterface, TemperatureReadingTypeInterface, HumidityReadingTypeInterface, LatitudeReadingTypeInterface
{
    public const NAME = 'Bmp';

    public const ALIAS = 'bmp';

    public const HIGH_TEMPERATURE_READING_BOUNDARY = 85;

    public const LOW_TEMPERATURE_READING_BOUNDARY = -45;

    private const ALLOWED_READING_TYPES = [
        Temperature::READING_TYPE,
        Humidity::READING_TYPE,
        Latitude::READING_TYPE
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

    public function getMaxLatitude(): float|int
    {
        return Latitude::HIGH_READING;
    }

    public function getMinLatitude(): float|int
    {
        return Latitude::LOW_READING;
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
