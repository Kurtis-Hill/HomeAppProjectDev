<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\DTO\Sensor\CurrentReadingDTO\UpdateReadingTypeCurrentReadingDTO;
use App\ESPDeviceSensor\DTO\Sensor\CurrentReadingDTO\UpdateSensorCurrentReadingConsumerMessageDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotSupportedException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeObjectBuilderException;
use App\ESPDeviceSensor\Exceptions\SensorReadingUpdateFactoryException;
use App\ESPDeviceSensor\Factories\ORMFactories\ConstRecord\ORMConstRecordFactoryInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\OufOfBounds\OutOfBoundsFactoryInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingUpdateFactory;
use App\ESPDeviceSensor\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorServiceInterface;

//@TODO make rabbit mq autoload docker config
class UpdateCurrentSensorReadingsService implements UpdateCurrentSensorReadingInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    private SensorReadingUpdateFactory $readingUpdateFactory;

    private SensorReadingTypesValidatorServiceInterface $readingTypesValidator;

    private ORMConstRecordFactoryInterface $constRecordFactory;

    private OutOfBoundsFactoryInterface $outOfBoundsFactory;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorTypeQueryFactory $readingTypeQueryFactory,
        SensorReadingUpdateFactory $readingUpdateFactory,
        SensorReadingTypesValidatorServiceInterface $readingTypesValidator,
        ORMConstRecordFactoryInterface $constRecordFactory,
        OutOfBoundsFactoryInterface $outOfBoundsFactory,

    )
    {
        $this->sensorRepository = $sensorRepository;
        $this->sensorTypeQueryFactory = $readingTypeQueryFactory;
        $this->readingUpdateFactory = $readingUpdateFactory;
        $this->readingTypesValidator = $readingTypesValidator;
        $this->constRecordFactory = $constRecordFactory;
        $this->outOfBoundsFactory = $outOfBoundsFactory;
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
                    $this->checkIfSensorIsConstantlyRecorded($updateReadingTypeCurrentReadingDTO);
                    $this->checkIfSensorIsOutOfBounds($updateReadingTypeCurrentReadingDTO);
                }
            } catch (
                ReadingTypeNotExpectedException
                | SensorReadingUpdateFactoryException
                | ReadingTypeObjectBuilderException $e) {
                continue;
            }
        }

        return true;
    }

    private function checkIfSensorIsConstantlyRecorded(UpdateReadingTypeCurrentReadingDTO $updateReadingTypeCurrentReadingDTO): void
    {
        $isConstRecord = $updateReadingTypeCurrentReadingDTO->getSensorReadingObject()->getConstRecord();

        if ($isConstRecord === true) {

        }
    }

    private function checkIfSensorIsOutOfBounds(UpdateReadingTypeCurrentReadingDTO $updateReadingTypeCurrentReadingDTO): void
    {
        $isOutOfBounds = $updateReadingTypeCurrentReadingDTO->getSensorReadingObject()->isReadingOutOfBounds();

        if ($isOutOfBounds === true) {

        }
    }

    private function updateCurrentSensorReading(AllSensorReadingTypeInterface $allSensorReadingType, array $sensorData): bool
    {
        if ($allSensorReadingType instanceof StandardReadingSensorInterface) {
            $this->updateStandardSensorReading($allSensorReadingType, $sensorData);
        }
    }

    private function updateStandardSensorReading(StandardReadingSensorInterface $standardReadingSensor, array $sensorData): bool
    {

    }
}
