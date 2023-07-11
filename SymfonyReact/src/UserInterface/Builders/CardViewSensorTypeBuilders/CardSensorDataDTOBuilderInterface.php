<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\DTO\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

interface CardSensorDataDTOBuilderInterface
{
    #[ArrayShape([StandardCardViewReadingResponseDTO::class|BoolCardViewReadingResponseDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array;
}
