<?php

namespace App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Bool;

use App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\AbstractReadingTypeDTOBuilder;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;

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
