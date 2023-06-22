<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;

class AnalogSensorCardViewDTOBuilder extends AbstractStandardReadingTypeDTOBuilder
{
    public function buildAnalogSensorDataFromScalarArray(array $cardData): ?StandardCardViewReadingResponseDTO
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
