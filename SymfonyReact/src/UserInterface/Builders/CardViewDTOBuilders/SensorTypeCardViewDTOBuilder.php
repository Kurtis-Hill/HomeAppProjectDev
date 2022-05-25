<?php

namespace App\UserInterface\Builders\CardViewDTOBuilders;

use App\UserInterface\DTO\Response\CurrentCardReadingDTO\UserViewSensorTypeCardDataResponseDTOInterface;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

interface SensorTypeCardViewDTOBuilder
{
    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function buildSensorTypeCardViewDTO(array $cardData): ?UserViewSensorTypeCardDataResponseDTOInterface;
}
