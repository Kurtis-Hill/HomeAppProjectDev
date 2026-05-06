<?php

namespace App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Bool;

use App\DTOs\UserInterface\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;

class RelaySensorCardViewDTOBuilder extends AbstractBoolReadingTypeDTOBuilder
{
    public function buildGenericRelaySensorDataFromScalarArray(array $cardData): ?BoolCardViewReadingResponseDTO
    {
        if (empty($cardData['relay_boolID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['baseReadingType_updatedAt']);

        return $this->getBoolCardViewDTO(
            Relay::READING_TYPE,
            $dateTime,
            $cardData['relay_currentReading'],
            $cardData['relay_expectedReading'] ?? null,
            $cardData['relay_requestedReading'],
        );
    }
}
