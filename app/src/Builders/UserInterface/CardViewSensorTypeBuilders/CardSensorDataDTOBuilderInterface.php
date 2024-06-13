<?php

namespace App\Builders\UserInterface\CardViewSensorTypeBuilders;

use App\DTOs\UserInterface\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use JetBrains\PhpStorm\ArrayShape;

interface CardSensorDataDTOBuilderInterface
{
    #[ArrayShape([StandardCardViewReadingResponseDTO::class|BoolCardViewReadingResponseDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array;

    public function formatSensorTypeObjectsByReadingType(SensorTypeInterface $cardDTOData, Sensor $sensor): array;
}
