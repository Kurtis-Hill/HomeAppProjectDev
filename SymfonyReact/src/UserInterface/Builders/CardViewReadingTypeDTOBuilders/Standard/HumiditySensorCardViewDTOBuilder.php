<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;

class HumiditySensorCardViewDTOBuilder extends AbstractStandardReadingTypeDTOBuilder
{
    public function buildHumiditySensorDataFromScalarArray(array $cardData): ?StandardCardViewReadingResponseDTO
    {
        if (empty($cardData['humid_readingTypeID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['humid_updatedAt']);

        return $this->getStandardCardViewDTO(
            Humidity::READING_TYPE,
            $cardData['humid_currentReading'],
            $cardData['humid_highReading'],
            $cardData['humid_lowReading'],
            $dateTime,
            Humidity::READING_SYMBOL,
        );
    }
}
