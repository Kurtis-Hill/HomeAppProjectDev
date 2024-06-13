<?php

namespace App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Standard;

use App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\AbstractReadingTypeDTOBuilder;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;

abstract class AbstractStandardReadingTypeDTOBuilder extends AbstractReadingTypeDTOBuilder
{
    protected function getStandardCardViewDTO(
        string $readingType,
        int|float $currentReading,
        int|float $highReading,
        int|float $lowReading,
        string $formattedDateTime,
        ?string $readingSymbol = null,
    ): StandardCardViewReadingResponseDTO {
        return new StandardCardViewReadingResponseDTO(
            $readingType,
            $currentReading,
            $highReading,
            $lowReading,
            $formattedDateTime,
            $readingSymbol,
        );
    }
}
