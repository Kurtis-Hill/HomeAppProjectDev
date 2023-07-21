<?php

namespace App\Sensors\Builders\CurrentReadingDTOBuilders;

use App\Sensors\DTO\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;

class BoolCurrentReadingUpdateDTOBuilder
{
    public static function buildCurrentReadingUpdateDTO(
        string $readingType,
        bool $readingTypeCurrentReading,
    ): BoolCurrentReadingUpdateDTO {
        return new BoolCurrentReadingUpdateDTO(
            $readingType,
            $readingTypeCurrentReading,
        );
    }
}
