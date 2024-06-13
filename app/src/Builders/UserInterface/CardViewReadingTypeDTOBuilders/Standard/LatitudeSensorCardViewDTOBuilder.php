<?php

namespace App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Standard;

use App\DTOs\UserInterface\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;

class LatitudeSensorCardViewDTOBuilder extends AbstractStandardReadingTypeDTOBuilder
{
    public function buildLatitudeSensorDataFromScalarArray(array $cardData): ?StandardCardViewReadingResponseDTO
    {
        if (empty($cardData['lat_readingTypeID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['baseReadingType_updatedAt']);

        return $this->getStandardCardViewDTO(
            Latitude::READING_TYPE,
            $cardData['lat_currentReading'],
            $cardData['lat_highReading'],
            $cardData['lat_lowReading'],
            $dateTime,
        );
    }
}
