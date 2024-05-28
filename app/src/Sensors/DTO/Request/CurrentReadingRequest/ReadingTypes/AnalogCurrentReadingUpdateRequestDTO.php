<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SoilConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LDRConstraint;
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
