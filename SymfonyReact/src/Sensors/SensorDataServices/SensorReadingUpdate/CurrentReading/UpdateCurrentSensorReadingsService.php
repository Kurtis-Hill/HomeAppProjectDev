<?php

namespace App\Sensors\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\ErrorLogs;
use App\Sensors\DTO\Sensor\CurrentReadingDTO\UpdateSensorCurrentReadingConsumerMessageDTO;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Exceptions\ReadingTypeObjectBuilderException;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;
use App\Sensors\Factories\ORMFactories\SensorReadingType\SensorReadingUpdateFactory;
use App\Sensors\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\Sensors\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorDataServices\ConstantlyRecord\SensorConstantlyRecordServiceInterface;
use App\Sensors\SensorDataServices\OutOfBounds\OutOfBoundsSensorServiceInterface;
use App\Sensors\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorServiceInterface;
use Doctrine\ORM\ORMException;

class UpdateCurrentSensorReadingsService implements UpdateCurrentSensorReadingInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    private SensorReadingUpdateFactory $readingUpdateFactory;

    private SensorReadingTypesValidatorServiceInterface $readingTypesValidator;

    private OutOfBoundsSensorServiceInterface $outOfBoundsSensorService;

    private SensorConstantlyRecordServiceInterface $constantlyRecordService;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorTypeQueryFactory $readingTypeQueryFactory,
        SensorReadingUpdateFactory $readingUpdateFactory,
        SensorReadingTypesValidatorServiceInterface $readingTypesValidator,
        OutOfBoundsSensorServiceInterface $outOfBoundsSensorService,
        SensorConstantlyRecordServiceInterface $constantlyRecordService,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->sensorTypeQueryFactory = $readingTypeQueryFactory;
        $this->readingUpdateFactory = $readingUpdateFactory;
        $this->readingTypesValidator = $readingTypesValidator;
        $this->outOfBoundsSensorService = $outOfBoundsSensorService;
        $this->constantlyRecordService = $constantlyRecordService;
    }

    public function handleUpdateSensorCurrentReading(
        UpdateSensorCurrentReadingConsumerMessageDTO $updateSensorCurrentReadingConsumerDTO,
        Devices $device
    ): bool
    {
        $sensorTypeQueryDTOBuilder = $this->sensorTypeQueryFactory->getSensorTypeQueryDTOBuilder(
            $updateSensorCurrentReadingConsumerDTO->getSensorType()
        );

        $sensorReadingTypeQueryDTOs = $sensorTypeQueryDTOBuilder->buildSensorReadingTypes();

        $sensorReadingObjects = $this->sensorRepository->getSensorTypeAndReadingTypeObjectsForSensor(
            $updateSensorCurrentReadingConsumerDTO->getDeviceId(),
            $updateSensorCurrentReadingConsumerDTO->getSensorName(),
            null,
            $sensorReadingTypeQueryDTOs,
        );

        foreach ($sensorReadingObjects as $sensorReadingObject) {
            try {
                if (!$sensorReadingObject instanceof AllSensorReadingTypeInterface) {
                    throw new ReadingTypeNotExpectedException(
                            ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_UPDATE_APP_MESSAGE,
                    );
                }
                $sensorReadingUpdateBuilder = $this->readingUpdateFactory->getReadingTypeUpdateBuilder(
                    $sensorReadingObject->getReadingType()
                );

                $updateReadingTypeCurrentReadingDTO = $sensorReadingUpdateBuilder->buildCurrentReadingUpdateDTO(
                    $sensorReadingObject,
                    $updateSensorCurrentReadingConsumerDTO->getCurrentReadings()
                );

                $updateReadingTypeCurrentReadingDTO->getSensorReadingObject()->setCurrentReading(
                    $updateReadingTypeCurrentReadingDTO->getNewCurrentReading()
                );
                $validationErrors = $this->readingTypesValidator->validateSensorReadingTypeObject(
                    $sensorReadingObject,
                    $updateSensorCurrentReadingConsumerDTO->getSensorType()
                );

                if (!empty($validationErrors)) {
                    $sensorReadingObject->setCurrentReading($updateReadingTypeCurrentReadingDTO->getCurrentReading());
                }
                if ($sensorReadingObject instanceof StandardReadingSensorInterface) {
                    try {
                        $this->outOfBoundsSensorService->checkAndProcessOutOfBounds($sensorReadingObject);
                    } catch (ORMException $e) {
                        error_log($e, 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
                    }
                    try {
                        $this->constantlyRecordService->checkAndProcessConstRecord($sensorReadingObject);
                    } catch (ORMException $e) {
                        error_log($e, 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
                    }
                }
            } catch (
                ReadingTypeNotExpectedException
                | SensorReadingUpdateFactoryException
                | ReadingTypeObjectBuilderException $e) {
                continue;
            }
        }

        $this->sensorRepository->flush();

        return true;
    }
}
