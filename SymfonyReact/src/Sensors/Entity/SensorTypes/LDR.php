<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\AbstractSensorType;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Repository\SensorType\ORM\LDRRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: LDRRepository::class),
]
class LDR extends AbstractSensorType implements StandardSensorTypeInterface, AnalogReadingTypeInterface
{
    public const NAME = 'Ldr';

    public const ALIAS = 'ldr';

    public const HIGH_READING = 1023;

    public const LOW_READING = 0;

    private const ALLOWED_READING_TYPES = [
        Analog::READING_TYPE,
    ];

    public static function getAllowedReadingTypes(): array
    {
        return self::ALLOWED_READING_TYPES;
    }

    public function getMaxAnalog(): float|int
    {
        return self::HIGH_READING;
    }

    public function getMinAnalog(): float|int
    {
        return self::LOW_READING;
    }

    public static function getReadingTypeName(): string
    {
        return self::NAME;
    }
}
