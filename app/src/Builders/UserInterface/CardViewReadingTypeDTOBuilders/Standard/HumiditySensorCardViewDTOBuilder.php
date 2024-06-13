<?php

namespace App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Standard;

use App\DTOs\UserInterface\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;

class HumiditySensorCardViewDTOBuilder extends AbstractStandardReadingTypeDTOBuilder
{
    public function buildHumiditySensorDataFromScalarArray(array $cardData): ?StandardCardViewReadingResponseDTO
    {
        if (empty($cardData['humid_readingTypeID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['baseReadingType_updatedAt']);

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
