<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\DTO\SensorReadingTypeObjects\SensorReadingTypeObjectsDTO;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use JetBrains\PhpStorm\ArrayShape;

interface UpdateSensorBoundaryReadingsServiceInterface
{
    /**
     * @throws \App\UserInterface\Exceptions\ReadingTypeBuilderFailureException
     */
    #[ArrayShape([JoinQueryDTO::class])]
    public function getSensorTypeObjectJoinQueryDTO(SensorReadingTypeObjectsDTO $sensorReadingTypeObjectsDTO): array;

    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function getReadingTypeObjectJoinQueryDTO(string $sensorName): JoinQueryDTO;

    public function findSensorTypeToUpdateBoundaryReadings(JoinQueryDTO $readingTypeJoinQueryDTO, array $readingTypeObjectsJoinDTOs, int $deviceID, string $sensorName): SensorTypeInterface;

    #[ArrayShape(([UpdateSensorBoundaryReadingsDTO::class]))]
    public function createSensorUpdateBoundaryReadingsDTOs(SensorTypeInterface $sensorTypeObject, array $updateData): array;

    public function setNewBoundaryReadings(SensorTypeInterface $sensorType, array $updateSensorBoundaryReadingsDTOs): void;
}
