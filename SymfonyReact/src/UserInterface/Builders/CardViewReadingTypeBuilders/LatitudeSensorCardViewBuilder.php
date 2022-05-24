<?php

namespace App\UserInterface\Builders\CardViewReadingTypeBuilders;

use App\Sensors\Entity\ReadingTypes\Latitude;
use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;

class LatitudeSensorCardViewBuilder extends AbstractStandardReadingTypeBuilder
{
    public function buildLatitudeSensorDataFromScalarArray(array $cardData): ?StandardCardViewDTO
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
