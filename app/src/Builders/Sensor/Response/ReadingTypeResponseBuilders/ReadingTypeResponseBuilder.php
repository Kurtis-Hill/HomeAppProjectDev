<?php

namespace App\Builders\Sensor\Response\ReadingTypeResponseBuilders;

use App\DTOs\Sensor\Response\ReadingTypes\ReadingTypeResponseDTO;
use App\Entity\Sensor\ReadingTypes\ReadingTypes;

class ReadingTypeResponseBuilder
{
    public static function buildReadingTypeResponseDTO(ReadingTypes $readingTypes): ReadingTypeResponseDTO
    {
        return new ReadingTypeResponseDTO(
            $readingTypes->getReadingTypeID(),
            $readingTypes->getReadingType(),
        );
    }
}
