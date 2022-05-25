<?php

namespace App\Sensors\Builders\ReadingTypeResponseBuilders;

use App\Sensors\DTO\Response\ReadingTypes\ReadingTypeResponseDTO;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;

class ReadingTypeResponseBuilder
{
    public static function buildReadingTypeResponseDTO(ReadingTypes $readingTypes)
    {
        return new ReadingTypeResponseDTO(
            $readingTypes->getReadingTypeID(),
            $readingTypes->getReadingType(),
        );
    }
}
