<?php

namespace App\Sensors\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\Common\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\Sensors\DTO\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\Sensors\DTO\Request\UpdateSensorReadingBoundaryRequestDTO;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;
use App\Sensors\Factories\ORMFactories\SensorReadingType\SensorReadingTypeRepositoryFactoryInterface;
use App\Sensors\Factories\ORMFactories\SensorReadingType\SensorReadingUpdateFactory;
use App\Sensors\Factories\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;
use App\Sensors\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\Sensors\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorServiceInterface;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateSensorBoundaryReadingsService implements UpdateSensorBoundaryReadingsServiceInterface
{
    use ValidatorProcessorTrait;

    private ValidatorInterface $validator;

    private SensorRepositoryInterface $sensorRepository;

    private SensorReadingTypesValidatorServiceInterface $sensorReadingTypesValidatorService;

    private SensorReadingUpdateFactory $sensorReadingUpdateFactory;

    private SensorReadingTypeRepositoryFactoryInterface $sensorReadingUpdateRepositoryFactory;

    public function __construct(
        SensorReadingTypeRepositoryFactoryInterface $sensorReadingUpdateRepositoryFactory,
        SensorReadingTypesValidatorServiceInterface $sensorReadingTypesValidatorService,
        SensorReadingUpdateFactory $sensorReadingUpdateFactory,
    ) {
        $this->sensorReadingUpdateRepositoryFactory = $sensorReadingUpdateRepositoryFactory;
        $this->sensorReadingTypesValidatorService = $sensorReadingTypesValidatorService;
        $this->sensorReadingUpdateFactory = $sensorReadingUpdateFactory;
    }

    public function getSensorReadingTypeObject(int $sensorID, string $readingType): AllSensorReadingTypeInterface
    {
        $repository = $this->sensorReadingUpdateRepositoryFactory->getSensorReadingTypeRepository($readingType);
        $sensorReadingTypeObject = $repository->getOneBySensorNameID($sensorID);

        if ($sensorReadingTypeObject === null) {
            throw new SensorReadingTypeObjectNotFoundException(SensorReadingTypeRepositoryFactoryException::READING_TYPE_NOT_FOUND);
        }
        return $repository->getOneBySensorNameID($sensorID);
    }

    #[ArrayShape(["errors"])]
    public function processBoundaryDataDTO(
        SensorUpdateBoundaryDataDTOInterface $updateBoundaryDataDTO,
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
        string $sensorType
    ): array {
        $readingTypeUpdateBuilder = $this->sensorReadingUpdateFactory->getReadingTypeUpdateBuilder($updateBoundaryDataDTO->getReadingType());

        $updateSensorBoundaryReadingsDTO = $readingTypeUpdateBuilder->buildUpdateSensorBoundaryReadingsDTO(
            $updateBoundaryDataDTO,
            $sensorReadingTypeObject
        );

        if ($sensorReadingTypeObject instanceof StandardReadingSensorInterface) {
            $this->updateStandardSensorBoundaryReading(
                $sensorReadingTypeObject,
                $updateSensorBoundaryReadingsDTO
            );
        }
        $validationError = $this->sensorReadingTypesValidatorService->validateSensorReadingTypeObject(
            $sensorReadingTypeObject,
            $sensorType
        );

        if (!empty($validationError)) {
            $this->resetEntityBackToOriginalState(
                $sensorReadingTypeObject,
                $updateSensorBoundaryReadingsDTO
            );
        }

        return $validationError;
    }

    private function updateStandardSensorBoundaryReading(
        StandardReadingSensorInterface $standardReadingSensor,
        UpdateStandardReadingTypeBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO
    ): void {
        if ($updateSensorBoundaryReadingsDTO->getHighReading() !== null) {
            $standardReadingSensor->setHighReading($updateSensorBoundaryReadingsDTO->getHighReading());
        }
        if ($updateSensorBoundaryReadingsDTO->getLowReading() !== null) {
            $standardReadingSensor->setLowReading($updateSensorBoundaryReadingsDTO->getLowReading());
        }
        if ($updateSensorBoundaryReadingsDTO->getConstRecord() !== null) {
            $standardReadingSensor->setConstRecord($updateSensorBoundaryReadingsDTO->getConstRecord());
        }
    }

    /**
     * @throws ReadingTypeNotSupportedException
     */
    private function resetEntityBackToOriginalState(
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
        UpdateStandardReadingTypeBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO
    ): void {
        if ($sensorReadingTypeObject instanceof StandardReadingSensorInterface) {
            $sensorReadingTypeObject->setHighReading($updateSensorBoundaryReadingsDTO->getCurrentHighReading());
            $sensorReadingTypeObject->setLowReading($updateSensorBoundaryReadingsDTO->getCurrentLowReading());
        } else {
            throw new ReadingTypeNotSupportedException(
                sprintf(
                    ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR,
                    $sensorReadingTypeObject->getReadingType(),
                    $updateSensorBoundaryReadingsDTO->getReadingType(),
                )
            );
        }
    }

//    /**
//     * @throws ReadingTypeNotSupportedException
//     */
//    #[ArrayShape(["errors"])]
//    public function processBoundaryReadingDTOs(
//        AllSensorReadingTypeInterface $sensorReadingTypeObject,
//        UpdateStandardReadingTypeBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO,
//        string $sensorTypeName
//    ): array {
//        if ($sensorReadingTypeObject instanceof StandardReadingSensorInterface) {
//            $this->updateStandardSensorBoundaryReading(
//                $sensorReadingTypeObject,
//                $updateSensorBoundaryReadingsDTO
//            );
//        }
//        $validationError = $this->sensorReadingTypesValidatorService->validateSensorReadingTypeObject(
//            $sensorReadingTypeObject,
//            $sensorTypeName
//        );
//
//        if (!empty($validationError)) {
//            $this->resetEntityBackToOriginalState(
//                $sensorReadingTypeObject,
//                $updateSensorBoundaryReadingsDTO
//            );
//        }
//
//        return $validationError;
//    }
//
//    public function getReadingTypeObjectJoinQueryDTO(string $sensorName): JoinQueryDTO
//    {
//        return $this->sensorTypeQueryFactory->getSensorTypeQueryDTOBuilder($sensorName)->buildSensorTypeQueryJoinDTO();
//    }
//
//    #[ArrayShape(
//        [
//            Dht::class|Bmp::class|Dallas::class|Soil::class,
//            Temperature::class,
//            Humidity::class,
//            Latitude::class,
//            Analog::class
//        ]
//    )]
//    public function findSensorTypeAndReadingTypes(
//        JoinQueryDTO $readingTypeJoinQueryDTO,
//        array $readingTypeObjectsJoinDTOs,
//        int $deviceID,
//        string $sensorName
//    ): array
//    {
//        return $this->sensorRepository->getSensorTypeAndReadingTypeObjectsForSensor(
//            $deviceID,
//            $sensorName,
//            $readingTypeJoinQueryDTO,
//            $readingTypeObjectsJoinDTOs,
//        );
//    }
//
//    public function createReadingTypeQueryDTO(UpdateStandardReadingTypeBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): JoinQueryDTO
//    {
//        $sensorTypeQueryDTOBuilder = $this->readingTypeQueryFactory->getReadingTypeQueryDTOBuilder($updateSensorBoundaryReadingsDTO->getReadingType());
//
//        return $sensorTypeQueryDTOBuilder->buildReadingTypeJoinQueryDTO();
//    }

}
