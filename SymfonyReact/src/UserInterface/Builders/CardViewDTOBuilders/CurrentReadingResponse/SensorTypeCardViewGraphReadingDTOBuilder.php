<?php

namespace App\UserInterface\Builders\CardViewDTOBuilders\CurrentReadingResponse;

use App\UserInterface\DTO\Response\CurrentCardReadingDTO\UserViewSensorTypeCardDataResponseDTOInterface;
use RuntimeException;

class SensorTypeCardViewGraphReadingDTOBuilder implements SensorTypeCardViewDTOBuilder
{
    /**
     * @throws RuntimeException
     */
    public function buildTrimmedDownSensorTypeCardViewDTO(array $cardData): ?UserViewSensorTypeCardDataResponseDTOInterface
    {
        throw new RuntimeException('Method not implemented');
    }

}
