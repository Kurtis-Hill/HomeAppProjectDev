<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;

class LatitudeSensorCardViewDTOBuilder extends AbstractStandardReadingTypeDTOBuilder
{
    public function buildLatitudeSensorDataFromScalarArray(array $cardData): ?StandardCardViewReadingResponseDTO
    {
        if (empty($cardData['lat_latitudeID'])) {
            return null;
        }
        $dateTime = $this->formatDateTime($cardData['lat_updatedAt']);

        return $this->getStandardCardViewDTO(
            Latitude::READING_TYPE,
            $cardData['lat_latitude'],
            $cardData['lat_highLatitude'],
            $cardData['lat_lowLatitude'],
            $dateTime,
        );
    }
}
