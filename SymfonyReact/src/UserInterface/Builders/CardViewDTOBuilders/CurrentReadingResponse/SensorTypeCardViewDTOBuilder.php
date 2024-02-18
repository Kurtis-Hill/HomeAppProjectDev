<?php

namespace App\UserInterface\Builders\CardViewDTOBuilders\CurrentReadingResponse;

use App\UserInterface\DTO\Response\CurrentCardReadingDTO\UserViewSensorTypeCardDataResponseDTOInterface;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

interface SensorTypeCardViewDTOBuilder
{
    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function buildTrimmedDownSensorTypeCardViewDTO(array $cardData): ?UserViewSensorTypeCardDataResponseDTOInterface;
}
