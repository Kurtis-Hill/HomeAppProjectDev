<?php

namespace App\Sensors\Builders\Internal\ReadingType\ReadingTypeUpdateBuilders;

use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;

interface CurrentReadingUpdateRequestBuilderInterface
{
    public function buildRequestCurrentReadingUpdateDTO(mixed $currentReading): AbstractCurrentReadingUpdateRequestDTO;
}
