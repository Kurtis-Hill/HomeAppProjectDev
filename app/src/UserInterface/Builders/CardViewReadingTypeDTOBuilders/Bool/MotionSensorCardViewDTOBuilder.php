<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Bool;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\UserInterface\DTO\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;

class MotionSensorCardViewDTOBuilder extends AbstractBoolReadingTypeDTOBuilder
{
    public function buildGenericMotionSensorDataFromScalarArray(array $cardData): ?BoolCardViewReadingResponseDTO
    {
        if (empty($cardData['motion_boolID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['baseReadingType_updatedAt']);

        return $this->getBoolCardViewDTO(
            Motion::READING_TYPE,
            $dateTime,
            $cardData['motion_currentReading'],
            $cardData['motion_expectedReading'],
            $cardData['motion_requestedReading'],
        );
    }
}
