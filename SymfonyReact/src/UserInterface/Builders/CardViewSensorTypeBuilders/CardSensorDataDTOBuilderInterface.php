<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

interface CardSensorDataDTOBuilderInterface
{
    #[ArrayShape([StandardCardViewReadingResponseDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array;
}
