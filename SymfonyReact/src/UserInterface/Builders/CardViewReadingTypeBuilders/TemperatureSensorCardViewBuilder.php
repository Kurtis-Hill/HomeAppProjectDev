<?php

namespace App\UserInterface\Builders\CardViewReadingTypeBuilders;

use App\Sensors\Entity\ReadingTypes\Temperature;
use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;

class TemperatureSensorCardViewBuilder extends AbstractStandardReadingTypeBuilder
{
    public function buildTemperatureSensorDataFromScalarArray(array $cardData): ?StandardCardViewDTO
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
