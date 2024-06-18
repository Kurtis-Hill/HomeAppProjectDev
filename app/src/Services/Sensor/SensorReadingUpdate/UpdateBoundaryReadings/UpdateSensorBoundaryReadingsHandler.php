<?php

namespace App\Services\Sensor\SensorReadingUpdate\UpdateBoundaryReadings;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeUpdateBuilders\ReadingTypeUpdateBoundaryReadingBuilderInterface;
use App\DTOs\Sensor\Internal\BoundaryReadings\UpdateBoolReadingTypeBoundaryReadingsDTO;
use App\DTOs\Sensor\Internal\BoundaryReadings\UpdateBoundaryReadingDTOInterface;
use App\DTOs\Sensor\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\DTOs\Sensor\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotSupportedException;
use App\Exceptions\Sensor\SensorReadingTypeObjectNotFoundException;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Exceptions\Sensor\SensorTypeNotFoundException;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Factories\Sensor\SensorReadingType\SensorReadingUpdateFactory;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\Sensor\SensorReadingTypesValidator\SensorReadingTypesValidatorInterface;
use App\Traits\ValidatorProcessorTrait;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateSensorBoundaryReadingsHandler implements UpdateSensorBoundaryReadingsHandlerInterface
{
    use ValidatorProcessorTrait;

    private ValidatorInterface $validator;

    private SensorRepositoryInterface $sensorRepository;

    private SensorReadingTypesValidatorInterface $sensorReadingTypesValidatorService;

    private SensorReadingUpdateFactory $sensorReadingUpdateFactory;

    private SensorReadingTypeRepositoryFactory $sensorReadingUpdateRepositoryFactory;

    public function __construct(
        SensorReadingTypeRepositoryFactory $sensorReadingUpdateRepositoryFactory,
        SensorReadingTypesValidatorInterface $sensorReadingTypesValidatorService,
        SensorReadingUpdateFactory $sensorReadingUpdateFactory,
    ) {
        $this->sensorReadingUpdateRepositoryFactory = $sensorReadingUpdateRepositoryFactory;
        $this->sensorReadingTypesValidatorService = $sensorReadingTypesValidatorService;
        $this->sensorReadingUpdateFactory = $sensorReadingUpdateFactory;
    }

    public function getSensorReadingTypeObject(int $sensorID, string $readingType): AllSensorReadingTypeInterface
    {
        $repository = $this->sensorReadingUpdateRepositoryFactory->getSensorReadingTypeRepository($readingType);
        $sensorReadingTypeObject = $repository->findOneBySensorNameID($sensorID);

        if ($sensorReadingTypeObject === null) {
            throw new SensorReadingTypeObjectNotFoundException(SensorReadingTypeRepositoryFactoryException::READING_TYPE_NOT_FOUND);
        }

        return $repository->findOneBySensorNameID($sensorID);
    }


    #[ArrayShape(["errors"])]
    public function processBoundaryDataDTO(
        SensorUpdateBoundaryDataDTOInterface $updateBoundaryDataDTO,
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
        string $sensorType
    ): array {
        $readingTypeUpdateBuilder = $this->sensorReadingUpdateFactory->getReadingTypeUpdateBuilder($updateBoundaryDataDTO->getReadingType());

        if (!$readingTypeUpdateBuilder instanceof ReadingTypeUpdateBoundaryReadingBuilderInterface) {
            throw new SensorTypeNotFoundException(sprintf(SensorTypeNotFoundException::SENSOR_TYPE_NOT_RECOGNISED, $updateBoundaryDataDTO->getReadingType()));
        }
        $updateSensorBoundaryReadingsDTO = $readingTypeUpdateBuilder->buildUpdateSensorBoundaryReadingsDTO(
            $updateBoundaryDataDTO,
            $sensorReadingTypeObject
        );

        if ($sensorReadingTypeObject instanceof StandardReadingSensorInterface && $updateSensorBoundaryReadingsDTO instanceof UpdateStandardReadingTypeBoundaryReadingsDTO) {
            $this->updateStandardSensorBoundaryReading(
                $sensorReadingTypeObject,
                $updateSensorBoundaryReadingsDTO
            );
        }
        if ($sensorReadingTypeObject instanceof BoolReadingSensorInterface && $updateSensorBoundaryReadingsDTO instanceof UpdateBoolReadingTypeBoundaryReadingsDTO) {
            $this->updateBoolSensorBoundaryReading(
                $sensorReadingTypeObject,
                $updateSensorBoundaryReadingsDTO
            );
        }
        $validationError = $this->sensorReadingTypesValidatorService->validateSensorReadingTypeObject(
            $sensorReadingTypeObject,
            $sensorType,
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
        if ($updateSensorBoundaryReadingsDTO->getNewConstRecord() !== null) {
            $standardReadingSensor->setConstRecord($updateSensorBoundaryReadingsDTO->getNewConstRecord());
        }
    }

    private function updateBoolSensorBoundaryReading(
        BoolReadingSensorInterface $boolReadingSensor,
        UpdateBoolReadingTypeBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO,
    ): void {
        if ($updateSensorBoundaryReadingsDTO->getNewExpectedReading() !== null) {
            $boolReadingSensor->setConstRecord($updateSensorBoundaryReadingsDTO->getNewConstRecord());
        }
        if ($updateSensorBoundaryReadingsDTO->getNewConstRecord() !== null) {
            $boolReadingSensor->setExpectedReading($updateSensorBoundaryReadingsDTO->getNewExpectedReading());
        }
    }

    /**
     * @throws \App\Exceptions\Sensor\ReadingTypeNotSupportedException
     */
    private function resetEntityBackToOriginalState(
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
        UpdateBoundaryReadingDTOInterface $updateSensorBoundaryReadingsDTO
    ): void {
        if ($sensorReadingTypeObject instanceof StandardReadingSensorInterface && $updateSensorBoundaryReadingsDTO instanceof UpdateStandardReadingTypeBoundaryReadingsDTO) {
            $sensorReadingTypeObject->setHighReading($updateSensorBoundaryReadingsDTO->getCurrentHighReading());
            $sensorReadingTypeObject->setLowReading($updateSensorBoundaryReadingsDTO->getCurrentLowReading());
            $sensorReadingTypeObject->setConstRecord($updateSensorBoundaryReadingsDTO->getCurrentConstRecord());
        } elseif ($sensorReadingTypeObject instanceof BoolReadingSensorInterface && $updateSensorBoundaryReadingsDTO instanceof UpdateBoolReadingTypeBoundaryReadingsDTO) {
            $sensorReadingTypeObject->setExpectedReading($updateSensorBoundaryReadingsDTO->getCurrentExpectedReading());
            $sensorReadingTypeObject->setConstRecord($updateSensorBoundaryReadingsDTO->getCurrentConstRecord());
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
}
