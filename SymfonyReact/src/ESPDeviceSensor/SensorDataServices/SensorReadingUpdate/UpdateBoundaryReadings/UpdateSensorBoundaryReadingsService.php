<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\DTO\SensorReadingTypeObjects\SensorReadingTypeObjectsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingTypeFactoryInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingUpdateFactory;
use App\ESPDeviceSensor\Factories\SensorTypeObjectsBuilderFactory;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\Factories\CardQueryBuilderFactories\ReadingTypeQueryFactory;
use App\UserInterface\Factories\CardQueryBuilderFactories\SensorTypeQueryFactory;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class UpdateSensorBoundaryReadingsService implements UpdateSensorBoundaryReadingsServiceInterface
{
    private ReadingTypeQueryFactory $readingTypeQueryFactory;

    private SensorRepositoryInterface $sensorRepository;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    private SensorReadingUpdateFactory $sensorReadingUpdateFactory;

    private SensorReadingTypeFactoryInterface $sensorReadingTypeFactory;

//    private SensorReadingUpdateFactory $sensorReadingUpdateFactory;

    public function __construct(
        ReadingTypeQueryFactory $readingTypeQuery,
        SensorTypeQueryFactory $sensorTypeQuery,
        SensorRepositoryInterface $sensorRepository,
        SensorReadingUpdateFactory $sensorReadingUpdateFactory,
        SensorReadingTypeFactoryInterface $sensorReadingTypeFactory,
//        SensorReadingUpdateFactory $sensorReadingUpdateFactory,
    )
    {
        $this->readingTypeQueryFactory = $readingTypeQuery;
        $this->sensorRepository = $sensorRepository;
        $this->sensorTypeQueryFactory = $sensorTypeQuery;
        $this->sensorReadingUpdateFactory = $sensorReadingUpdateFactory;
        $this->sensorReadingTypeFactory = $sensorReadingTypeFactory;
//        $this->sensorReadingUpdateFactory = $sensorReadingUpdateFactory;
    }

    #[ArrayShape([JoinQueryDTO::class])]
    public function getSensorTypeObjectJoinQueryDTO(SensorReadingTypeObjectsDTO $sensorReadingTypeObjectsDTO): array
    {
        foreach ($sensorReadingTypeObjectsDTO->getSensorReadingTypeObjects() as $sensorName => $sensorReadingTypeObject) {
            $readingTypeQueryBuilder = $this->readingTypeQueryFactory->getReadingTypeQueryDTOBuilder($sensorName);
            $readingTypeQueryDTOs[] = $readingTypeQueryBuilder->buildReadingTypeJoinQueryDTO();
        }

        return $readingTypeQueryDTOs ?? [];
    }

    public function getReadingTypeObjectJoinQueryDTO(string $sensorName): JoinQueryDTO
    {
        return $this->sensorTypeQueryFactory->getSensorTypeQueryDTOBuilder($sensorName)->buildSensorTypeQueryJoinDTO();
    }

    #[ArrayShape([Temperature::class, Humidity::class])]
    public function findSensorAndReadingTypesToUpdateBoundaryReadings(JoinQueryDTO $readingTypeJoinQueryDTO, array $readingTypeObjectsJoinDTOs, int $deviceID, string $sensorName): array
    {
        $sensorTypeObjectArray = $this->sensorRepository->getSensorTypeAndReadingTypeObjectsForSensor(
            $readingTypeJoinQueryDTO,
            $deviceID,
            $readingTypeObjectsJoinDTOs,
            $sensorName,
        );

        return $sensorTypeObjectArray;
    }

    #[Pure]
    #[ArrayShape(([UpdateSensorBoundaryReadingsDTO::class]))]
    public function createSensorUpdateBoundaryReadingsDTOs(SensorTypeInterface $sensorTypeObject, array $updateData): array
    {
        foreach ($updateData as $sensorData) {
            $updateBoundaryReadingDTOs[] = $this->sensorReadingUpdateFactory->getReadingTypeUpdateBuilder($sensorData['sensorType'])->buildUpdateSensorBoundaryReadingsDTO($sensorData, $sensorTypeObject);
        }

        return $updateBoundaryReadingDTOs ?? [];
    }

    /**
     * @throws \App\ESPDeviceSensor\Exceptions\SensorReadingUpdateFactoryException
     * @throws \App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException
     */
    public function setNewBoundaryReadings(SensorTypeInterface $sensorType, array $updateSensorBoundaryReadingsDTOs): void
    {
//        dd('fsdf', $sensorType);
        foreach ($updateSensorBoundaryReadingsDTOs as $updateSensorBoundaryReadingsDTO) {
            $this->sensorReadingTypeFactory->getSensorReadingTypeRepository($updateSensorBoundaryReadingsDTO->getSensorType())->findOneById();
            $sensorReadingTypeBuilder = $this->sensorReadingUpdateFactory->getReadingTypeUpdateBuilder($updateSensorBoundaryReadingsDTO->getSensorType());

            $sensorReadingTypeBuilder->setNewBoundaryForReadingType($sensorType, $updateSensorBoundaryReadingsDTO);
        }
    }
}
