<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\DTO\CardViewDTO\FormattedSensorDataDTO;

interface CardDTOBuilderInterface
{
    public function formatSensorData(array $sensorData): array;
}
