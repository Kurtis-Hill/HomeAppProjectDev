<?php

namespace App\UserInterface\Builders\CardViewReadingTypeBuilders;

use App\Sensors\Entity\ReadingTypes\Humidity;
use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;

class HumiditySensorCardViewBuilder extends AbstractStandardReadingTypeBuilder
{
    public function buildHumiditySensorDataFromScalarArray(array $cardData): ?StandardCardViewDTO
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
