<?php

namespace App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Bool;

use App\DTOs\UserInterface\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;

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
