<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest;

use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LatitudeConstraint;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class LatitudeCurrentReadingUpdateDTORequest extends AbstractCurrentReadingUpdateRequestDTO
{
    #[LatitudeConstraint(groups: [Bmp::NAME])]
    protected float|int|string $readingTypeCurrentReading;

    public function getReadingType(): string
    {
        return Latitude::READING_TYPE;
    }
}
