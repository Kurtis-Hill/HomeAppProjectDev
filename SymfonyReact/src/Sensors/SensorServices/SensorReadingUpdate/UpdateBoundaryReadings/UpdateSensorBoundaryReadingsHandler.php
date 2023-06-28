<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBoundaryReadingBuilderInterface;
use App\Sensors\DTO\Internal\BoundaryReadings\UpdateBoolReadingTypeBoundaryReadingsDTO;
use App\Sensors\DTO\Internal\BoundaryReadings\UpdateBoundaryReadingDTOInterface;
use App\Sensors\DTO\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\BoolSensorUpdateBoundaryDataDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Sensors\Factories\SensorReadingType\SensorReadingUpdateFactory;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\SensorReadingTypesValidator\SensorReadingTypesValidatorInterface;
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

        if (!$readingTypeUpdateBuilder instanceof ReadingTypeUpdateBoundaryReadingBuilderInterface) {
            throw new SensorTypeNotFoundException(sprintf(SensorTypeNotFoundException::SENSOR_TYPE_NOT_RECOGNISED, $updateBoundaryDataDTO->getReadingType()));
        }
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
        if ($sensorReadingTypeObject instanceof BoolReadingSensorInterface) {
            $this->updateBoolSensorBoundaryReading(
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
     * @throws ReadingTypeNotSupportedException
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
