<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\UserInterface\DTO\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

interface CardSensorDataDTOBuilderInterface
{
    #[ArrayShape([StandardCardViewReadingResponseDTO::class|BoolCardViewReadingResponseDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array;

    public function formatSensorTypeObjectsByReadingType(SensorTypeInterface $cardDTOData, Sensor $sensor): array;
}
