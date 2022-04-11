<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest;

use App\Sensors\Entity\ReadingTypes\Temperature;

class TemperatureCurrentReadingUpdateDTORequest extends AbstractCurrentReadingUpdateRequest
{
    public function getReadingType(): string
    {
        return Temperature::READING_TYPE;
    }
}
