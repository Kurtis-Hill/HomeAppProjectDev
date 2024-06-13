<?php

namespace App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes;

use App\CustomValidators\Sensor\SensorDataValidators\HumidityConstraint;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\Sht;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class HumidityCurrentReadingUpdateRequestDTO extends AbstractCurrentReadingUpdateRequestDTO
{
    #[HumidityConstraint(groups: [Bmp::NAME, Dht::NAME, Sht::NAME])]
    protected mixed $readingTypeCurrentReading;

    public function getReadingType(): string
    {
        return Humidity::READING_TYPE;
    }
}
