<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\DTO\SensorReadingTypeObjects\SensorReadingTypeObjectsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingTypeFactoryInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingUpdateFactory;
use App\ESPDeviceSensor\Factories\SensorTypeObjectsBuilderFactory;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorService;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorServiceInterface;
use App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder\ReadingTypeQueryDTOBuilderInterface;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\Factories\CardQueryBuilderFactories\ReadingTypeQueryFactory;
use App\UserInterface\Factories\CardQueryBuilderFactories\SensorTypeQueryFactory;
use http\Exception\UnexpectedValueException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class UpdateSensorBoundaryReadingsService implements UpdateSensorBoundaryReadingsServiceInterface
{
    private ReadingTypeQueryFactory $readingTypeQueryFactory;

    private SensorRepositoryInterface $sensorRepository;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;#

    private SensorReadingTypesValidatorServiceInterface $sensorReadingTypesValidatorService;

//    private SensorReadingUpdateFactory $sensorReadingUpdateFactory;
//
//    private SensorReadingTypeFactoryInterface $sensorReadingTypeFactory;

//    private SensorReadingUpdateFactory $sensorReadingUpdateFactory;

    public function __construct(
        ReadingTypeQueryFactory $readingTypeQuery,
        SensorTypeQueryFactory $sensorTypeQuery,
        SensorRepositoryInterface $sensorRepository,
        SensorReadingTypesValidatorServiceInterface $sensorReadingTypesValidatorService,
        SensorReadingUpdateFactory $sensorReadingUpdateFactory,
        SensorReadingTypeFactoryInterface $sensorReadingTypeFactory,
//        SensorReadingUpdateFactory $sensorReadingUpdateFactory,
    )
    {
        $this->readingTypeQueryFactory = $readingTypeQuery;
        $this->sensorRepository = $sensorRepository;
        $this->sensorTypeQueryFactory = $sensorTypeQuery;
        $this->sensorReadingTypesValidatorService = $sensorReadingTypesValidatorService;
//        $this->sensorReadingUpdateFactory = $sensorReadingUpdateFactory;
//        $this->sensorReadingTypeFactory = $sensorReadingTypeFactory;
//        $this->sensorReadingUpdateFactory = $sensorReadingUpdateFactory;
    }

    private function getReadingTypeQueryDTOBuilder(UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): ReadingTypeQueryDTOBuilderInterface
    {
        return $this->readingTypeQueryFactory->getReadingTypeQueryDTOBuilder($updateSensorBoundaryReadingsDTO->getSensorType());
    }

    private function updateSensorBoundaryReading(
        StandardReadingSensorInterface $standardReadingSensor,
        UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO
    ): void {
        $standardReadingSensor->setHighReading($updateSensorBoundaryReadingsDTO->getHighReading());
        $standardReadingSensor->setLowReading($updateSensorBoundaryReadingsDTO->getLowReading());
        $standardReadingSensor->setConstRecord($updateSensorBoundaryReadingsDTO->getConstRecord());
    }

//    #[ArrayShape([JoinQueryDTO::class])]
//    public function getSensorTypeObjectJoinQueryDTO(SensorReadingTypeObjectsDTO $sensorReadingTypeObjectsDTO): array
//    {
//        foreach ($sensorReadingTypeObjectsDTO->getSensorReadingTypeObjects() as $sensorName => $sensorReadingTypeObject) {
//            $readingTypeQueryBuilder = $this->readingTypeQueryFactory->getReadingTypeQueryDTOBuilder($sensorName);
//            $readingTypeQueryDTOs[] = $readingTypeQueryBuilder->buildReadingTypeJoinQueryDTO();
//        }
//
//        return $readingTypeQueryDTOs ?? [];
//    }

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
    public function createUpdateSensorBoundaryReadingDTO(array $updateData): UpdateSensorBoundaryReadingsDTO
    {
        return new UpdateSensorBoundaryReadingsDTO(
            $updateData['sensorType'],
            $updateData['highReading'],
            $updateData['lowReading'],
            $updateData['constRecord'],
        );
    }

    public function processBoundaryReadingDTOs(array $updateSensorBoundaryReadingsDTOs, array $readingTypeObjects, string $sensorTypeName): array
    {
        $validationErrors = [];
        foreach ($updateSensorBoundaryReadingsDTOs as $updateSensorBoundaryReadingsDTO) {
            if (!$updateSensorBoundaryReadingsDTO instanceof UpdateSensorBoundaryReadingsDTO) {
                throw new UnexpectedValueException('You have not passed the correct DTO for this service to process request');
            }
            foreach ($readingTypeObjects as $sensorReadingTypeObject) {
                if (!$sensorReadingTypeObject instanceof StandardReadingSensorInterface || !$sensorReadingTypeObject instanceof AllSensorReadingTypeInterface) {
                    throw new UnexpectedValueException('You have not passed the correct sensor reading type for this service to process request');
                }
                if ($sensorReadingTypeObject->getSensorTypeName() === $updateSensorBoundaryReadingsDTO->getSensorType()) {
                    $this->updateSensorBoundaryReading(
                        $sensorReadingTypeObject,
                        $updateSensorBoundaryReadingsDTO
                    );
                    $validationErrors[] = $this->sensorReadingTypesValidatorService->validateSensorReadingTypeObject(
                        $sensorReadingTypeObject,
                        $sensorTypeName
                    );
                }
            }
        }

        return $validationErrors ?? [];
    }

    public function createReadingTypeQueryDTO(UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): JoinQueryDTO
    {
        $sensorTypeQueryDTOBuilder = $this->getReadingTypeQueryDTOBuilder($updateSensorBoundaryReadingsDTO);

        return $sensorTypeQueryDTOBuilder->buildReadingTypeJoinQueryDTO();
    }


//    #[Pure]
//    #[ArrayShape(([UpdateSensorBoundaryReadingsDTO::class]))]
//    public function createSensorUpdateBoundaryReadingsDTOs(SensorTypeInterface $sensorTypeObject, array $updateData): array
//    {
//        foreach ($updateData as $sensorData) {
//            $updateBoundaryReadingDTOs[] = $this->sensorReadingUpdateFactory->getReadingTypeUpdateBuilder($sensorData['sensorType'])->buildUpdateSensorBoundaryReadingsDTO($sensorData, $sensorTypeObject);
//        }
//
//        return $updateBoundaryReadingDTOs ?? [];
//    }

//    /**
//     * @throws \App\ESPDeviceSensor\Exceptions\SensorReadingUpdateFactoryException
//     * @throws \App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException
//     */
//    public function setNewBoundaryReadings(SensorTypeInterface $sensorType, array $updateSensorBoundaryReadingsDTOs): void
//    {
////        dd('fsdf', $sensorType);
//        foreach ($updateSensorBoundaryReadingsDTOs as $updateSensorBoundaryReadingsDTO) {
//            $this->sensorReadingTypeFactory->getSensorReadingTypeRepository($updateSensorBoundaryReadingsDTO->getSensorType())->findOneById();
//            $sensorReadingTypeBuilder = $this->sensorReadingUpdateFactory->getReadingTypeUpdateBuilder($updateSensorBoundaryReadingsDTO->getSensorType());
//
//            $sensorReadingTypeBuilder->setNewBoundaryForReadingType($sensorType, $updateSensorBoundaryReadingsDTO);
//        }
//    }
}
