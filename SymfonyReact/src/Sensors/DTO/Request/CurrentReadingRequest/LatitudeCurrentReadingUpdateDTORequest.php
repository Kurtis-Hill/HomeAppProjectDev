<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest;

use App\Sensors\Entity\ReadingTypes\Latitude;

class LatitudeCurrentReadingUpdateDTORequest extends AbstractCurrentReadingUpdateRequest
{
    public function getReadingType(): string
    {
        return Latitude::READING_TYPE;
    }
}
