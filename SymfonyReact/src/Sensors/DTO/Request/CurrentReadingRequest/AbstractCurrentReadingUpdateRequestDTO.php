<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest;

use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\BMP280TemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\DallasTemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\DHTTemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\HumidityConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LatitudeConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SoilConstraint;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
abstract class AbstractCurrentReadingUpdateRequestDTO
{
    protected float|int|string $readingTypeCurrentReading;

    public function __construct(float|int|string $readingTypeCurrentReading)
    {
        $this->readingTypeCurrentReading = $readingTypeCurrentReading;
    }

    public function getCurrentReading(): float|int|string
    {
        return $this->readingTypeCurrentReading;
    }

    abstract public function getReadingType(): string;
}
