<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\ESPDeviceSensor\DTO\SensorReadingTypeObjects\SensorReadingTypeObjectsDTO;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use JetBrains\PhpStorm\ArrayShape;

interface UpdateSensorBoundaryReadingsServiceInterface
{
    /**
     * @throws \App\UserInterface\Exceptions\ReadingTypeBuilderFailureException
     */
    #[ArrayShape([JoinQueryDTO::class])]
    public function getSensorTypeObjectToUpdateQueryDTO(SensorReadingTypeObjectsDTO $sensorReadingTypeObjectsDTO): array;
}
