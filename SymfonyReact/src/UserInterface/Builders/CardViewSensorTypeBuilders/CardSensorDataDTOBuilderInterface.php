<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;
use JetBrains\PhpStorm\ArrayShape;

interface CardSensorDataDTOBuilderInterface
{
    #[ArrayShape([StandardCardViewDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array;

//    #[ArrayShape([])]
//    public function formatSensorTypeObjects(SensorTypeInterface $sensorTypeObject): array;
}
