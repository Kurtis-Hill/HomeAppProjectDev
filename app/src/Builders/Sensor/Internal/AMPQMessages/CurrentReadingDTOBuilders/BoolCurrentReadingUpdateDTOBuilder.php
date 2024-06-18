<?php
declare(strict_types=1);

namespace App\Builders\Sensor\Internal\AMPQMessages\CurrentReadingDTOBuilders;

use App\DTOs\Sensor\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;

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
