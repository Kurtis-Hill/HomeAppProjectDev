<?php

namespace App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes;

use App\CustomValidators\Sensor\SensorDataValidators\LatitudeConstraint;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\SensorTypes\Bmp;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class LatitudeCurrentReadingUpdateRequestDTO extends AbstractCurrentReadingUpdateRequestDTO
{
    #[LatitudeConstraint(groups: [Bmp::NAME])]
    protected mixed $readingTypeCurrentReading;

    public function getReadingType(): string
    {
        return Latitude::getReadingTypeName();
    }
}
