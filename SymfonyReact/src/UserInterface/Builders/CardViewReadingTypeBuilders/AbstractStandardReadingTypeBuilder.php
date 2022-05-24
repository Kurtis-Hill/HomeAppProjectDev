<?php

namespace App\UserInterface\Builders\CardViewReadingTypeBuilders;

use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;
use DateTime;

abstract class AbstractStandardReadingTypeBuilder
{
    protected function getStandardCardViewDTO(
        string $readingType,
        int|float $currentReading,
        int|float $highReading,
        int|float $lowReading,
        string $formattedDateTime,
        ?string $readingSymbol = null,
    ): StandardCardViewDTO {
        return new StandardCardViewDTO(
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
