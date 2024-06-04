<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;

class AnalogSensorCardViewDTOBuilder extends AbstractStandardReadingTypeDTOBuilder
{
    public function buildAnalogSensorDataFromScalarArray(array $cardData): ?StandardCardViewReadingResponseDTO
    {
        if (empty($cardData['analog_readingTypeID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['baseReadingType_updatedAt']);

        return $this->getStandardCardViewDTO(
            Analog::READING_TYPE,
            $cardData['analog_currentReading'],
            $cardData['analog_highReading'],
            $cardData['analog_lowReading'],
            $dateTime,
        );
    }
}
