<?php

namespace App\UserInterface\Builders\CardViewDTOBuilders;

use App\UserInterface\DTO\Response\CurrentCardReadingDTO\UserViewSensorTypeCardDataResponseDTOInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;

class SensorTypeCardViewGraphReadingDTOBuilder implements SensorTypeCardViewDTOBuilder
{
    public function buildSensorTypeCardViewDTO(array $cardData): ?UserViewSensorTypeCardDataResponseDTOInterface
    {
        throw new NotImplementedException('SensorTypeCardViewGraphReadingDTOBuilder:makeDTO');
    }

}
