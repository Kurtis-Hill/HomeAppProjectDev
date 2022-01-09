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
    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function getReadingTypeObjectJoinQueryDTO(string $sensorName): JoinQueryDTO;

    #[ArrayShape([Temperature::class])]
    public function findSensorAndReadingTypesToUpdateBoundaryReadings(JoinQueryDTO $readingTypeJoinQueryDTO, array $readingTypeObjectsJoinDTOs, int $deviceID, string $sensorName): array;

    /**
     * @throws TypeError
     * @throws ReadingTypeBuilderFailureException
     */
    public function createUpdateSensorBoundaryReadingDTO(array $updateData): UpdateSensorBoundaryReadingsDTO;

    #[ArrayShape(["errors"])]
    public function processBoundaryReadingDTOs(array $updateSensorBoundaryReadingsDTOs, array $readingTypeObjects, string $sensorTypeName): array;

    /**
     * @throws ReadingTypeBuilderFailureException
     */
    public function createReadingTypeQueryDTO(UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): JoinQueryDTO;
}
