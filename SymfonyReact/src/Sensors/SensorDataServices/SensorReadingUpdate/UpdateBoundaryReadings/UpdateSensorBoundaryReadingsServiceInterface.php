<?php

namespace App\Sensors\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\Sensors\DTO\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\Sensors\DTO\Request\UpdateSensorReadingBoundaryRequestDTO;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\Exceptions\ReadingTypeBuilderFailureException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use Doctrine\ORM\NonUniqueResultException;
use JetBrains\PhpStorm\ArrayShape;

interface UpdateSensorBoundaryReadingsServiceInterface
{
//    /**
//     * @throws SensorTypeBuilderFailureException
//     */
//    public function getReadingTypeObjectJoinQueryDTO(string $sensorName): JoinQueryDTO;
//
//    #[ArrayShape([Temperature::class | Humidity::class | Analog::class | Latitude::class])]
//    public function findSensorTypeAndReadingTypes(JoinQueryDTO $readingTypeJoinQueryDTO, array $readingTypeObjectsJoinDTOs, int $deviceID, string $sensorName): array;

    /**
     * @throws SensorReadingUpdateFactoryException
     * @throws ReadingTypeNotExpectedException
     */
    #[ArrayShape(["errors"])]
    public function processBoundaryDataDTO(
        SensorUpdateBoundaryDataDTOInterface $updateBoundaryDataDTO,
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
        string $sensorType,
    ): array;

//    #[ArrayShape(["errors"])]
//    public function processBoundaryReadingDTOs(
//        AllSensorReadingTypeInterface $sensorReadingTypeObject,
//        UpdateStandardReadingTypeBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO,
//        string $sensorTypeName
//    ): array;

//    /**
//     * @throws ReadingTypeBuilderFailureException
//     */
//    public function createReadingTypeQueryDTO(UpdateStandardReadingTypeBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): JoinQueryDTO;

    /**
     * @throws NonUniqueResultException
     * @throws SensorReadingTypeRepositoryFactoryException
     * @throws SensorReadingTypeObjectNotFoundException
     */
    public function getSensorReadingTypeObject(int $sensorID, string $readingType): AllSensorReadingTypeInterface;
}
