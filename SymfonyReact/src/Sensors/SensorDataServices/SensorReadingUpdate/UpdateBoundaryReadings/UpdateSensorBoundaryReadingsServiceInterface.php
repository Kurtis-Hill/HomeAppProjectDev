<?php

namespace App\Sensors\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\Sensors\Builders\ReadingTypeUpdateBuilders\SensorUpdateBuilderInterface;
use App\Sensors\DTO\Sensor\UpdateStandardSensorBoundaryReadingsDTO;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Exceptions\ReadingTypeObjectBuilderException;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;
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
