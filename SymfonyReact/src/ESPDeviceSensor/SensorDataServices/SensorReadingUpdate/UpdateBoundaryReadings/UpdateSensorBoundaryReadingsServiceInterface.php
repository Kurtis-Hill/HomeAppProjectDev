<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\DTO\SensorReadingTypeObjects\SensorReadingTypeObjectsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Builders\ReadingTypeQueryDTOBuilders\ReadingTypeQueryDTOBuilderInterface;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\Exceptions\ReadingTypeBuilderFailureException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use JetBrains\PhpStorm\ArrayShape;
use TypeError;

interface UpdateSensorBoundaryReadingsServiceInterface
{

//    public function updateSensorBoundaryReading(
//        StandardReadingSensorInterface $standardReadingSensor,
//        UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO
//    ): void;

    /**
     * @throws ReadingTypeBuilderFailureException
     */
//    #[ArrayShape([JoinQueryDTO::class])]
//    public function getSensorTypeObjectJoinQueryDTO(SensorReadingTypeObjectsDTO $sensorReadingTypeObjectsDTO): array;

    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function getReadingTypeObjectJoinQueryDTO(string $sensorName): JoinQueryDTO;

    #[ArrayShape([Temperature::class])]
    public function findSensorAndReadingTypesToUpdateBoundaryReadings(JoinQueryDTO $readingTypeJoinQueryDTO, array $readingTypeObjectsJoinDTOs, int $deviceID, string $sensorName): array;

//    #[ArrayShape(([UpdateSensorBoundaryReadingsDTO::class]))]
//    public function createSensorUpdateBoundaryReadingsDTOs(SensorTypeInterface $sensorTypeObject, array $updateData): array;

//    public function setNewBoundaryReadings(SensorTypeInterface $sensorType, array $updateSensorBoundaryReadingsDTOs): void;

//    /**
//     * @throws ReadingTypeBuilderFailureException
//     */
//    public function getReadingTypeQueryDTOBuilder(UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): ReadingTypeQueryDTOBuilderInterface;

    /**
     * @throws TypeError
     * @throws ReadingTypeBuilderFailureException
     */
    public function createUpdateSensorBoundaryReadingDTO(array $updateData): UpdateSensorBoundaryReadingsDTO;

    public function processBoundaryReadingDTOs(array $updateSensorBoundaryReadingsDTOs, array $readingTypeObjects, string $sensorTypeName): array;
}
