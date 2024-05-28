<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Bool;

use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\AbstractReadingTypeDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;

abstract class AbstractBoolReadingTypeDTOBuilder extends AbstractReadingTypeDTOBuilder
{
    protected function getBoolCardViewDTO(
        string $readingType,
        string $formattedDateTime,
        ?bool $currentReading = null,
        ?bool $expectedReading = null,
        ?bool $requestedReading = null,
    ): BoolCardViewReadingResponseDTO {
        return new BoolCardViewReadingResponseDTO(
            $readingType,
            $formattedDateTime,
            $currentReading,
            $expectedReading,
            $requestedReading,
        );
    }
}
