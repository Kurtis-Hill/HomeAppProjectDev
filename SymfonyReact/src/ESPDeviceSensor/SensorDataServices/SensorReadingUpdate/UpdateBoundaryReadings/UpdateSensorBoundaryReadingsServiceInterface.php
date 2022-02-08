<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders\SensorUpdateBuilderInterface;
use App\ESPDeviceSensor\DTO\Sensor\UpdateStandardSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Exceptions\ReadingTypeObjectBuilderException;
use App\ESPDeviceSensor\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\ESPDeviceSensor\Exceptions\SensorReadingUpdateFactoryException;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\Exceptions\ReadingTypeBuilderFailureException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use Doctrine\ORM\NonUniqueResultException;
use JetBrains\PhpStorm\ArrayShape;
use TypeError;

interface UpdateSensorBoundaryReadingsServiceInterface
{
    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function getReadingTypeObjectJoinQueryDTO(string $sensorName): JoinQueryDTO;

    #[ArrayShape([Temperature::class | Humidity::class | Analog::class | Latitude::class])]
    public function findSensorTypeAndReadingTypes(JoinQueryDTO $readingTypeJoinQueryDTO, array $readingTypeObjectsJoinDTOs, int $deviceID, string $sensorName): array;

    /**
     * @throws SensorReadingUpdateFactoryException
     */
    public function getUpdateBoundaryReadingBuilder(string $sensorType): SensorUpdateBuilderInterface;

    #[ArrayShape(["errors"])]
    public function processBoundaryReadingDTOs(
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
        UpdateStandardSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO,
        string $sensorTypeName
    ): array;

    /**
     * @throws ReadingTypeBuilderFailureException
     */
    public function createReadingTypeQueryDTO(UpdateStandardSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): JoinQueryDTO;

    /**
     * @throws NonUniqueResultException
     * @throws SensorReadingTypeRepositoryFactoryException
     */
    public function getSensorReadingTypeObject(int $sensorID, string $readingType): ?AllSensorReadingTypeInterface;
}
