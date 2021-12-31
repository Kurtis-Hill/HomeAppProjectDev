<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\UserInterface\DTO\CardViewDTO\FormattedSensorDataDTO;
use JetBrains\PhpStorm\ArrayShape;

interface CardSensorDataDTOBuilderInterface
{
    public function formatScalarCardSensorData(array $sensorData): array;

    #[ArrayShape([])]
    public function formatSensorTypeObjects(SensorTypeInterface $sensorTypeObject): array;

}
