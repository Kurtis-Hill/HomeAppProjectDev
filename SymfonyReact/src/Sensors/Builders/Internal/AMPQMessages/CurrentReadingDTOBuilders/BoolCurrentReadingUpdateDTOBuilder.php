<?php
declare(strict_types=1);

namespace App\Sensors\Builders\Internal\AMPQMessages\CurrentReadingDTOBuilders;

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
