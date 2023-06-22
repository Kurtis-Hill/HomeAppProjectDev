<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes;

use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SoilConstraint;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class AnalogCurrentReadingUpdateRequestDTO extends AbstractCurrentReadingUpdateRequestDTO
{
    #[SoilConstraint(groups: [Soil::NAME])]
    protected mixed $readingTypeCurrentReading;

    public function getReadingType(): string
    {
        return \App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog::READING_TYPE;
    }
}
