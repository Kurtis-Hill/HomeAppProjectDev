<?php

namespace App\Entity\Sensor\ReadingTypes\StandardReadingTypes;

use App\CustomValidators\Sensor\SensorDataValidators\HumidityConstraint;
use App\Entity\Sensor\SensorTypes\Interfaces\ReadingSymbolInterface;
use App\Repository\Sensor\ReadingType\ORM\HumidityRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: HumidityRepository::class),
]
class Humidity extends AbstractStandardReadingType implements ReadingSymbolInterface
{
    public const READING_TYPE = 'humidity';

    public const READING_SYMBOL = '%';

    public const HIGH_READING = 100;

    public const LOW_READING = 0;

    #[HumidityConstraint]
    protected float $currentReading;

    #[HumidityConstraint]
    protected float $highReading = 80;

    #[HumidityConstraint]
    protected float $lowReading = 10;

    public function getSensorID(): int
    {
        return $this->getReadingTypeID();
    }

    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highReading = $reading;
        }
    }

    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowReading = $reading;
        }
    }

    public function getReadingType(): string
    {
        return self::READING_TYPE;
    }

    public static function getReadingTypeName(): string
    {
        return self::READING_TYPE;
    }

    public static function getReadingSymbol(): string
    {
        return self::READING_SYMBOL;
    }
}
