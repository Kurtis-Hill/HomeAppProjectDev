<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\ErrorLogs;
use App\ESPDeviceSensor\DTO\Sensor\CurrentReadingDTO\UpdateSensorCurrentReadingConsumerMessageDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotSupportedException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeObjectBuilderException;
use App\ESPDeviceSensor\Exceptions\SensorReadingUpdateFactoryException;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingUpdateFactory;
use App\ESPDeviceSensor\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\SensorDataServices\ConstantlyRecord\SensorConstantlyRecordServiceInterface;
use App\ESPDeviceSensor\SensorDataServices\OutOfBounds\OutOfBoundsSensorServiceInterface;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorServiceInterface;
use Doctrine\ORM\ORMException;

//@TODO make rabbit mq autoload docker config
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
    )
    {
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
//dd($updateSensorCurrentReadingConsumerDTO);
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
//dd($sensorReadingObject, $updateReadingTypeCurrentReadingDTO);
                $validationErrors = $this->readingTypesValidator->validateSensorReadingTypeObject(
                    $sensorReadingObject,
                    $updateSensorCurrentReadingConsumerDTO->getSensorType()
                );

//                    dd($updateReadingTypeCurrentReadingDTO, $validationErrors);
                if (!empty($validationErrors)) {
                    $sensorReadingObject->setCurrentReading($updateReadingTypeCurrentReadingDTO->getCurrentReading());
                }
//dd($sensorReadingObject);
                if ($sensorReadingObject instanceof StandardReadingSensorInterface) {
                    try {
                        $this->outOfBoundsSensorService->checkAndHandleSensorReadingOutOfBounds($sensorReadingObject);
                    } catch (ORMException $e) {
                        error_log($e, 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
                    }
                    try {
//                        dd('asd');
                        $this->constantlyRecordService->checkAndProcessConstRecord($sensorReadingObject);
                    } catch (ORMException) {
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
