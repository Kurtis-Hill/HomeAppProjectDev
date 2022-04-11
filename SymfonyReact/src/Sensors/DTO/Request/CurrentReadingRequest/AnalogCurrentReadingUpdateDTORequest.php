<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest;

use App\Sensors\Entity\ReadingTypes\Analog;

class AnalogCurrentReadingUpdateDTORequest extends AbstractCurrentReadingUpdateRequest
{
    public function getReadingType(): string
    {
        return Analog::READING_TYPE;
    }
}
