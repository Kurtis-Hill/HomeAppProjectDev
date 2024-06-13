<?php

namespace App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes;

use App\CustomValidators\Sensor\SensorDataValidators\LDRConstraint;
use App\CustomValidators\Sensor\SensorDataValidators\SoilConstraint;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Soil;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class AnalogCurrentReadingUpdateRequestDTO extends AbstractCurrentReadingUpdateRequestDTO
{
    #[
        SoilConstraint(groups: [Soil::NAME]),
        LDRConstraint(groups: [LDR::NAME]),
    ]
    protected mixed $readingTypeCurrentReading;

    public function getReadingType(): string
    {
        return Analog::READING_TYPE;
    }
}
