<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SoilConstraint;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class AnalogCurrentReadingUpdateDTORequest extends AbstractCurrentReadingUpdateRequestDTO
{
    #[SoilConstraint(groups: [Soil::NAME])]
    protected float|int|string $readingTypeCurrentReading;

    public function getReadingType(): string
    {
        return Analog::READING_TYPE;
    }
}
