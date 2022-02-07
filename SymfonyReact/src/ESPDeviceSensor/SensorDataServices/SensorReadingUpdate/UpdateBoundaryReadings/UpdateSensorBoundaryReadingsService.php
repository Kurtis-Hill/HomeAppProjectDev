<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\Common\Traits\ValidatorProcessorTrait;
use App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders\SensorUpdateBuilderInterface;
use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingTypeRepositoryFactoryInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingUpdateFactory;
use App\ESPDeviceSensor\Factories\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorServiceInterface;
use App\ESPDeviceSensor\Builders\ReadingTypeQueryDTOBuilders\ReadingTypeQueryDTOBuilderInterface;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\ESPDeviceSensor\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\UserInterface\Exceptions\ReadingTypeBuilderFailureException;
use http\Exception\UnexpectedValueException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class UpdateSensorBoundaryReadingsService implements UpdateSensorBoundaryReadingsServiceInterface
{
    private ReadingTypeQueryFactory $readingTypeQueryFactory;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    private SensorRepositoryInterface $sensorRepository;

    private SensorReadingTypesValidatorServiceInterface $sensorReadingTypesValidatorService;

    private SensorReadingUpdateFactory $sensorReadingUpdateFactory;

    private SensorReadingTypeRepositoryFactoryInterface $sensorReadingUpdateRepositoryFactory;

    public function __construct(
        ReadingTypeQueryFactory $readingTypeQuery,
        SensorTypeQueryFactory $sensorTypeQuery,
        SensorRepositoryInterface $sensorRepository,
        SensorReadingTypeRepositoryFactoryInterface $sensorReadingUpdateRepositoryFactory,
        SensorReadingTypesValidatorServiceInterface $sensorReadingTypesValidatorService,
        SensorReadingUpdateFactory $sensorReadingUpdateFactory,
    ) {
        $this->readingTypeQueryFactory = $readingTypeQuery;
        $this->sensorRepository = $sensorRepository;
        $this->sensorTypeQueryFactory = $sensorTypeQuery;
        $this->sensorReadingUpdateRepositoryFactory = $sensorReadingUpdateRepositoryFactory;
        $this->sensorReadingTypesValidatorService = $sensorReadingTypesValidatorService;
        $this->sensorReadingUpdateFactory = $sensorReadingUpdateFactory;
    }

    public function getUpdateBoundaryReadingBuilder(string $sensorType): SensorUpdateBuilderInterface
    {
        return $this->sensorReadingUpdateFactory->getReadingTypeUpdateBuilder($sensorType);
    }

    public function getSensorReadingTypeObject(int $sensorID, string $readingType): ?AllSensorReadingTypeInterface
    {
        $repository = $this->sensorReadingUpdateRepositoryFactory->getSensorReadingTypeRepository($readingType);

        return $repository->getOneBySensorNameID($sensorID);
    }

    /**
     * @throws ReadingTypeBuilderFailureException
     */
    private function getReadingTypeQueryDTOBuilder(UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): ReadingTypeQueryDTOBuilderInterface
    {
        return $this->readingTypeQueryFactory->getReadingTypeQueryDTOBuilder($updateSensorBoundaryReadingsDTO->getReadingType());
    }

    private function updateSensorBoundaryReading(
        StandardReadingSensorInterface $standardReadingSensor,
        UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO
    ): void
    {
        $standardReadingSensor->setHighReading($updateSensorBoundaryReadingsDTO->getHighReading());
        $standardReadingSensor->setLowReading($updateSensorBoundaryReadingsDTO->getLowReading());
        $standardReadingSensor->setConstRecord($updateSensorBoundaryReadingsDTO->getConstRecord());
    }

    public function getReadingTypeObjectJoinQueryDTO(string $sensorName): JoinQueryDTO
    {
        return $this->sensorTypeQueryFactory->getSensorTypeQueryDTOBuilder($sensorName)->buildSensorTypeQueryJoinDTO();
    }

    #[ArrayShape([Temperature::class, Humidity::class, Latitude::class, Analog::class])]
    public function findSensorAndReadingTypesToUpdateBoundaryReadings(
        JoinQueryDTO $readingTypeJoinQueryDTO,
        array $readingTypeObjectsJoinDTOs,
        int $deviceID,
        string $sensorName
    ): array
    {
        return $this->sensorRepository->getSensorTypeAndReadingTypeObjectsForSensor(
            $readingTypeJoinQueryDTO,
            $deviceID,
            $readingTypeObjectsJoinDTOs,
            $sensorName,
        );
    }

    public function createReadingTypeQueryDTO(UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): JoinQueryDTO
    {
        $sensorTypeQueryDTOBuilder = $this->getReadingTypeQueryDTOBuilder($updateSensorBoundaryReadingsDTO);

        return $sensorTypeQueryDTOBuilder->buildReadingTypeJoinQueryDTO();
    }

    #[ArrayShape(["errors"])]
    public function processBoundaryReadingDTOs(
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
        UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO,
        string $sensorTypeName
    ): array
    {
        if ($sensorReadingTypeObject instanceof StandardReadingSensorInterface) {
            $this->updateSensorBoundaryReading(
                $sensorReadingTypeObject,
                $updateSensorBoundaryReadingsDTO
            );
        }

        $validationError = $this->sensorReadingTypesValidatorService->validateSensorReadingTypeObject(
            $sensorReadingTypeObject,
            $sensorTypeName
        );

        if (!empty($validationError)) {
            $this->resetEntityBackToOriginalStatus(
                $sensorReadingTypeObject,
                $updateSensorBoundaryReadingsDTO
            );
        }

        return $validationError;

//            try {
//                if (!$updateSensorBoundaryReadingsDTO instanceof UpdateSensorBoundaryReadingsDTO) {
//                    throw new UnexpectedValueException('You have not passed the correct DTO for this service to process request');
//                }
//                foreach ($readingTypeObjects as $sensorReadingTypeObject) {
//                    if (!$sensorReadingTypeObject instanceof StandardReadingSensorInterface || !$sensorReadingTypeObject instanceof AllSensorReadingTypeInterface) {
//                        throw new UnexpectedValueException('You have not passed the correct sensor reading type for this service to process request');
//                    }
//                    if ($sensorReadingTypeObject->getReadingType() === $updateSensorBoundaryReadingsDTO->getReadingType()) {
//                        $this->updateSensorBoundaryReading(
//                            $sensorReadingTypeObject,
//                            $updateSensorBoundaryReadingsDTO
//                        );
//                        $validationError = $this->sensorReadingTypesValidatorService->validateSensorReadingTypeObject(
//                            $sensorReadingTypeObject,
//                            $sensorTypeName
//                        );
//                        if (!empty($validationError)) {
//                            $this->resetEntityBackToOriginalStatus(
//                                $sensorReadingTypeObject,
//                                $updateSensorBoundaryReadingsDTO
//                            );
//                            foreach ($validationError as $error) {
//                                $validationErrors[] = $error;
//                            }
//                        }
//                    }
//                }
//            } catch (UnexpectedValueException $e) {
//                $validationErrors[] = $e->getMessage();
//            }

//        $this->sensorRepository->flush();

        return $validationErrors ?? [];
    }

    private function resetEntityBackToOriginalStatus(
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
        UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO
    ): void
    {
        if ($sensorReadingTypeObject instanceof StandardReadingSensorInterface) {
            $sensorReadingTypeObject->setHighReading($updateSensorBoundaryReadingsDTO->getCurrentHighReading());
            $sensorReadingTypeObject->setLowReading($updateSensorBoundaryReadingsDTO->getCurrentLowReading());
        }
    }
}
