<?php

namespace App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Standard;

use App\DTOs\UserInterface\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;

class TemperatureSensorCardViewDTOBuilder extends AbstractStandardReadingTypeDTOBuilder
{
    public function buildTemperatureSensorDataFromScalarArray(array $cardData): ?StandardCardViewReadingResponseDTO
    {
        if (empty($cardData['temp_readingTypeID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['baseReadingType_updatedAt']);

        return $this->getStandardCardViewDTO(
            Temperature::READING_TYPE,
            $cardData['temp_currentReading'],
            $cardData['temp_highReading'],
            $cardData['temp_lowReading'],
            $dateTime,
            Temperature::READING_SYMBOL,
        );
    }
}
