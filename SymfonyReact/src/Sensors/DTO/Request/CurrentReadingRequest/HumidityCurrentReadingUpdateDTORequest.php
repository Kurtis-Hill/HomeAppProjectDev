<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest;

use App\Sensors\Entity\ReadingTypes\Humidity;

class HumidityCurrentReadingUpdateDTORequest extends AbstractCurrentReadingUpdateRequest
{
    public function getReadingType(): string
    {
        return Humidity::READING_TYPE;
    }
}
