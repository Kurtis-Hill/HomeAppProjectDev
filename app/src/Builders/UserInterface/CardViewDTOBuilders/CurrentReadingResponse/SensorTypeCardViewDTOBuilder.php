<?php

namespace App\Builders\UserInterface\CardViewDTOBuilders\CurrentReadingResponse;

use App\DTOs\UserInterface\Response\CurrentCardReadingDTO\UserViewSensorTypeCardDataResponseDTOInterface;
use App\Exceptions\UserInterface\SensorTypeBuilderFailureException;

interface SensorTypeCardViewDTOBuilder
{
    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function buildTrimmedDownSensorTypeCardViewDTO(array $cardData): ?UserViewSensorTypeCardDataResponseDTOInterface;
}
