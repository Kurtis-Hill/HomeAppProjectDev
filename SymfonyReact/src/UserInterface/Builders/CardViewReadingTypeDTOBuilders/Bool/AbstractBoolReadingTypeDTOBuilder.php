<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Bool;

use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\AbstractReadingTypeDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;

abstract class AbstractBoolReadingTypeDTOBuilder extends AbstractReadingTypeDTOBuilder
{
    protected function getBoolCardViewDTO(
        string $readingType,
        bool $currentReading,
        bool $expectedReading,
        bool $requestedReading,
        string $formattedDateTime,
    ): BoolCardViewReadingResponseDTO {
        return new BoolCardViewReadingResponseDTO(
            $readingType,
            $currentReading,
            $expectedReading,
            $requestedReading,
            $formattedDateTime,
        );
    }
}
