<?php

namespace App\UserInterface\Builders\CardViewReadingTypeBuilders;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;

class AnalogSensorCardViewBuilder extends AbstractStandardReadingTypeBuilder
{
    public function buildAnalogSensorDataFromScalarArray(array $cardData): ?StandardCardViewDTO
    {
        if (empty($cardData['analog_analogID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['analog_updatedAt']);

        return $this->getStandardCardViewDTO(
            Analog::READING_TYPE,
            $cardData['analog_analogReading'],
            $cardData['analog_highAnalog'],
            $cardData['analog_lowAnalog'],
            $dateTime,
        );
    }
}
