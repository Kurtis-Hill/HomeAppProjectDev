<?php

namespace App\UserInterface\Builders\CardViewBuilders;

use App\UserInterface\DTO\UserViewReadingSensorTypeCardData\UserViewSensorTypeCardDataInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;

class SensorTypeCardViewGraphReadingDTOBuilder implements SensorTypeCardViewDTOBuilder
{
    public function makeDTO(array $cardData): ?UserViewSensorTypeCardDataInterface
    {
        throw new NotImplementedException('SensorTypeCardViewGraphReadingDTOBuilder:makeDTO');
    }

}