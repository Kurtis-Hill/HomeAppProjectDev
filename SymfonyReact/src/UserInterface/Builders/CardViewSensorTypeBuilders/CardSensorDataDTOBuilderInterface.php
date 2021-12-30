<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\DTO\CardViewDTO\FormattedSensorDataDTO;

interface CardSensorDataDTOBuilderInterface
{
    public function formatCardSensorData(array $sensorData): array;
}
