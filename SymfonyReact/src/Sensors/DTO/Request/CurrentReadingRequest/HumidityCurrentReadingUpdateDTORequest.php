<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest;

use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\HumidityConstraint;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class HumidityCurrentReadingUpdateDTORequest extends AbstractCurrentReadingUpdateRequestDTO
{
    #[HumidityConstraint(groups: [Bmp::NAME, Dht::NAME])]
    protected float|int|string $readingTypeCurrentReading;

    public function getReadingType(): string
    {
        return Humidity::READING_TYPE;
    }
}
