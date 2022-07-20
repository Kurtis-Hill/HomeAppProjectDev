<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\ErrorLogs;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Exceptions\ReadingTypeObjectBuilderException;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;
use App\Sensors\Factories\ORMFactories\SensorReadingType\SensorReadingUpdateFactory;
use App\Sensors\Factories\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;
use App\Sensors\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\ConstantlyRecord\SensorConstantlyRecordHandlerInterface;
use App\Sensors\SensorServices\OutOfBounds\SensorOutOfBoundsHandlerInterface;
use App\Sensors\SensorServices\SensorReadingTypesValidator\SensorReadingTypesValidatorInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class UpdateCurrentSensorReadingsHandler implements UpdateCurrentSensorReadingInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorReadingUpdateFactory $readingUpdateFactory;

    private SensorReadingTypesValidatorInterface $readingTypesValidator;

    private SensorOutOfBoundsHandlerInterface $outOfBoundsSensorService;

    private SensorConstantlyRecordHandlerInterface $constantlyRecordService;

    private ReadingTypeQueryFactory $readingTypeQueryBuilderFactory;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorReadingUpdateFactory $readingUpdateFactory,
        SensorReadingTypesValidatorInterface $readingTypesValidator,
        SensorOutOfBoundsHandlerInterface $outOfBoundsSensorService,
        SensorConstantlyRecordHandlerInterface $constantlyRecordService,
        ReadingTypeQueryFactory $readingTypeQueryBuilderFactory,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->readingUpdateFactory = $readingUpdateFactory;
        $this->readingTypesValidator = $readingTypesValidator;
        $this->outOfBoundsSensorService = $outOfBoundsSensorService;
        $this->constantlyRecordService = $constantlyRecordService;
        $this->readingTypeQueryBuilderFactory = $readingTypeQueryBuilderFactory;
    }

    public function handleUpdateSensorCurrentReading(
        UpdateSensorCurrentReadingMessageDTO $updateSensorCurrentReadingConsumerDTO,
        Devices $device,
    ): bool {
        foreach ($updateSensorCurrentReadingConsumerDTO->getCurrentReadings() as $currentReadingUpdateDTO) {
            $readingTypeQueryDTOBuilder = $this->readingTypeQueryBuilderFactory->getReadingTypeQueryDTOBuilder($currentReadingUpdateDTO->getReadingType());
            $sensorReadingTypeQueryDTOs[] = $readingTypeQueryDTOBuilder->buildReadingTypeJoinQueryDTO();
        }

        if (empty($sensorReadingTypeQueryDTOs)) {
            return true;
        }
        $sensorReadingObjects = $this->sensorRepository->getSensorTypeAndReadingTypeObjectsForSensor(
            $updateSensorCurrentReadingConsumerDTO->getDeviceID(),
            $updateSensorCurrentReadingConsumerDTO->getSensorName(),
            null,
            $sensorReadingTypeQueryDTOs,
        );

        if (empty($sensorReadingObjects)) {
            throw new SensorReadingTypeObjectNotFoundException(SensorReadingTypeObjectNotFoundException::SENSOR_READING_TYPE_OBJECT_NOT_FOUND_EXCEPTION);
        }
        foreach ($sensorReadingObjects as $sensorReadingObject) {
            foreach ($updateSensorCurrentReadingConsumerDTO->getCurrentReadings() as $currentReadingDTO) {
                if ($currentReadingDTO->getReadingType() !== $sensorReadingObject->getReadingType()) {
                    continue;
                }
                try {
                    if (!$sensorReadingObject instanceof AllSensorReadingTypeInterface) {
                        throw new ReadingTypeNotExpectedException(
                            ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_UPDATE_APP_MESSAGE,
                        );
                    }
                    $sensorReadingUpdateBuilder = $this->readingUpdateFactory->getReadingTypeUpdateBuilder(
                        $sensorReadingObject->getReadingType()
                    );

                    $updateReadingTypeCurrentReadingDTO = $sensorReadingUpdateBuilder->buildReadingTypeCurrentReadingUpdateDTO(
                        $sensorReadingObject,
                        $currentReadingDTO,
                    );

                    $updateReadingTypeCurrentReadingDTO->getSensorReadingObject()->setCurrentReading(
                        $updateReadingTypeCurrentReadingDTO->getNewCurrentReading()
                    );
                    $validationErrors = $this->readingTypesValidator->validateSensorReadingTypeObject(
                        $sensorReadingObject,
                        $updateSensorCurrentReadingConsumerDTO->getSensorType()
                    );

                    $sensorReadingObject->setUpdatedAt();
                    if (!empty($validationErrors)) {
                        $sensorReadingObject->setCurrentReading($updateReadingTypeCurrentReadingDTO->getCurrentReading());
                    }
                    if ($sensorReadingObject instanceof StandardReadingSensorInterface) {
                        $this->outOfBoundsSensorService->processOutOfBounds($sensorReadingObject);
                        $this->constantlyRecordService->processConstRecord($sensorReadingObject);
                    }
                } catch (
                    ReadingTypeNotExpectedException
                    | SensorReadingUpdateFactoryException
                    | ReadingTypeObjectBuilderException $e
                ) {
                    error_log($e, 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
                    continue;
                }
            }
        }
        try {
            $this->sensorRepository->flush();
        } catch (ORMException|OptimisticLockException $e) {
            error_log($e, 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            return false;
        }

        return true;
    }
}