<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;

class TemperatureSensorCardViewDTOBuilder extends AbstractStandardReadingTypeDTOBuilder
{
    public function buildTemperatureSensorDataFromScalarArray(array $cardData): ?StandardCardViewReadingResponseDTO
    {
        if (empty($cardData['temp_tempID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['temp_updatedAt']);

        return $this->getStandardCardViewDTO(
            Temperature::READING_TYPE,
            $cardData['temp_currentReading'],
            $cardData['temp_highTemp'],
            $cardData['temp_lowTemp'],
            $dateTime,
            Temperature::READING_SYMBOL,
        );
    }
}
