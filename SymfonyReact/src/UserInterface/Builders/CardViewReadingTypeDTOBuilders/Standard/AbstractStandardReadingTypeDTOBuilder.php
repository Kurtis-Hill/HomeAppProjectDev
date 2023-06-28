<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard;

use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use DateTime;

abstract class AbstractStandardReadingTypeDTOBuilder
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

    protected function formatDateTime(DateTime $dateTime): string
    {
        return $dateTime->format('d-m-Y H:i:s');
    }
}
