<?php

namespace App\UserInterface\Builders\CardViewDTOBuilders\CurrentReadingResponse;

use App\UserInterface\DTO\Response\CurrentCardReadingDTO\UserViewSensorTypeCardDataResponseDTOInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;

class SensorTypeCardViewGraphReadingDTOBuilder implements SensorTypeCardViewDTOBuilder
{
    /**
     * @throws NotImplementedException
     */
    public function buildSensorTypeCardViewDTO(array $cardData): ?UserViewSensorTypeCardDataResponseDTOInterface
    {
        throw new NotImplementedException('SensorTypeCardViewGraphReadingDTOBuilder:makeDTO');
    }

}
