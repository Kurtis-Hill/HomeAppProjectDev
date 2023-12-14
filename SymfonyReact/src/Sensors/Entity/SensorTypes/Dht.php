<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\AbstractSensorType;
use App\Sensors\Entity\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Sensors\Repository\SensorType\ORM\DhtRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: DhtRepository::class),
]
class Dht extends AbstractSensorType implements StandardSensorTypeInterface, TemperatureReadingTypeInterface, HumidityReadingTypeInterface
{
    public const NAME = 'Dht';

    public const ALIAS = 'dht';

    public const HIGH_TEMPERATURE_READING_BOUNDARY = 80;

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
