<?php

namespace App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Standard;

use App\DTOs\UserInterface\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;

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
