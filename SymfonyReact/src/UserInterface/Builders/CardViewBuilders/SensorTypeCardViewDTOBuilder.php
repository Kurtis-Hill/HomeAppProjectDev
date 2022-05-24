<?php

namespace App\UserInterface\Builders\CardViewBuilders;

use App\UserInterface\DTO\Response\CurrentCardReadingDTO\UserViewSensorTypeCardDataInterface;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

interface SensorTypeCardViewDTOBuilder
{
    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function buildSensorTypeCardViewDTO(array $cardData): ?UserViewSensorTypeCardDataInterface;
}
