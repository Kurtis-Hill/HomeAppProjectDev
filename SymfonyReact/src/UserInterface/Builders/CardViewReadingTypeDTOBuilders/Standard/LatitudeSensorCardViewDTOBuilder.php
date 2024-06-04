<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;

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
