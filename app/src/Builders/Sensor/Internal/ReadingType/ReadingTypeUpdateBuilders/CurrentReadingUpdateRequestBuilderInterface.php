<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders;

use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;

interface CurrentReadingUpdateRequestBuilderInterface
{
    public function buildRequestCurrentReadingUpdateDTO(mixed $currentReading): AbstractCurrentReadingUpdateRequestDTO;
}
