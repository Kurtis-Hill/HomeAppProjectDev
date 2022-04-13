<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes;

use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LatitudeConstraint;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class LatitudeCurrentReadingUpdateRequestDTO extends AbstractCurrentReadingUpdateRequestDTO
{
    #[LatitudeConstraint(groups: [Bmp::NAME])]
    protected mixed $readingTypeCurrentReading;

    public function getReadingType(): string
    {
        return Latitude::READING_TYPE;
    }
}
