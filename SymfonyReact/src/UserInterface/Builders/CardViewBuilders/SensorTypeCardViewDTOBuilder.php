<?php

namespace App\UserInterface\Builders\CardViewBuilders;

use App\UserInterface\DTO\UserViewReadingSensorTypeCardData\UserViewSensorTypeCardDataInterface;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

interface SensorTypeCardViewDTOBuilder
{
    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function makeDTO(array $cardData): ?UserViewSensorTypeCardDataInterface;
}
