<?php

namespace App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Services\CustomValidators\Sensor\SensorDataValidators\BMP280TemperatureConstraint;
use App\Services\CustomValidators\Sensor\SensorDataValidators\DallasTemperatureConstraint;
use App\Services\CustomValidators\Sensor\SensorDataValidators\DHTTemperatureConstraint;
use App\Services\CustomValidators\Sensor\SensorDataValidators\SHTTemperatureConstraint;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class TemperatureCurrentReadingUpdateRequestDTO extends AbstractCurrentReadingUpdateRequestDTO
{
    #[
        DallasTemperatureConstraint(
            groups: [Dallas::NAME]
        ),
        DHTTemperatureConstraint(
            groups: [Dht::NAME]
        ),
        BMP280TemperatureConstraint(
            groups:[Bmp::NAME]
        ),
        SHTTemperatureConstraint(
            groups: [Sht::NAME]
        ),
    ]
    protected mixed $readingTypeCurrentReading;

    public function getReadingType(): string
    {
        return Temperature::getReadingTypeName();
    }
}
