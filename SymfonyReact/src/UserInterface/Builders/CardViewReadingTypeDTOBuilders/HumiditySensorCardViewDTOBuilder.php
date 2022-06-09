<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders;

use App\Sensors\Entity\ReadingTypes\Humidity;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;

class HumiditySensorCardViewDTOBuilder extends AbstractStandardReadingTypeDTOBuilder
{
    public function buildHumiditySensorDataFromScalarArray(array $cardData): ?StandardCardViewReadingResponseDTO
    {
        if (empty($cardData['humid_humidID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['humid_updatedAt']);

        return $this->getStandardCardViewDTO(
            Humidity::READING_TYPE,
            $cardData['humid_currentReading'],
            $cardData['humid_highHumid'],
            $cardData['humid_lowHumid'],
            $dateTime,
            Humidity::READING_SYMBOL,
        );
    }
}
