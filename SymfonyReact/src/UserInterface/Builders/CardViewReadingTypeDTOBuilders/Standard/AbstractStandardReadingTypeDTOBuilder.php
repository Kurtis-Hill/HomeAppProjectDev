<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard;

use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\AbstractReadingTypeDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;

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
