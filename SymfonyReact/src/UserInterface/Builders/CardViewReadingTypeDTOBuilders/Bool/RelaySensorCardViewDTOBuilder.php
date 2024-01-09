<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Bool;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\UserInterface\DTO\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;

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
            $cardData['relay_expectedReading'],
            $cardData['relay_requestedReading'],
        );
    }
}
