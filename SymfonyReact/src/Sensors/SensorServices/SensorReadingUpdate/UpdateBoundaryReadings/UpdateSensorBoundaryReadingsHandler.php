<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\DTO\Internal\BoundaryReadings\UpdateStandardReadingTypeBoundaryReadingsDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\BoolSensorUpdateBoundaryDataDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
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
        if ($updateSensorBoundaryReadingsDTO->getConstRecord() !== null) {
            $standardReadingSensor->setConstRecord($updateSensorBoundaryReadingsDTO->getConstRecord());
        }
    }

    private function updateBoolSensorBoundaryReading(
        BoolReadingSensorInterface $boolReadingSensor,
        BoolSensorUpdateBoundaryDataDTO $updateSensorBoundaryReadingsDTO,
    ): void {
        if ($updateSensorBoundaryReadingsDTO->getConstRecord() !== null) {
            $boolReadingSensor->setConstRecord($updateSensorBoundaryReadingsDTO->getConstRecord());
        }
        if ($updateSensorBoundaryReadingsDTO->getExpectedReading() !== null) {
            $boolReadingSensor->setExpectedReading($updateSensorBoundaryReadingsDTO->getExpectedReading());
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
}
