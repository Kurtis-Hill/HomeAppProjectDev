<?php

namespace App\Builders\UserInterface\CardViewDTOBuilders\CurrentReadingResponse;

use App\DTOs\UserInterface\Response\CurrentCardReadingDTO\UserViewSensorTypeCardDataResponseDTOInterface;
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
