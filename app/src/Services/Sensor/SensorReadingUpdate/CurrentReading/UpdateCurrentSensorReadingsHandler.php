<?php

namespace App\Services\Sensor\SensorReadingUpdate\CurrentReading;

use App\DTOs\Sensor\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingTransportMessageDTO;
use App\DTOs\Sensor\Internal\CurrentReadingDTO\ReadingTypeUpdateCurrentReadingDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;
use App\Exceptions\Sensor\ReadingTypeNotSupportedException;
use App\Exceptions\Sensor\ReadingTypeObjectBuilderException;
use App\Exceptions\Sensor\SensorNotFoundException;
use App\Exceptions\Sensor\SensorReadingUpdateFactoryException;
use App\Factories\Sensor\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;
use App\Factories\Sensor\SensorReadingType\SensorReadingUpdateFactory;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\Sensor\ConstantlyRecord\SensorConstantlyRecordHandlerInterface;
use App\Services\Sensor\OutOfBounds\SensorOutOfBoundsHandlerInterface;
use App\Services\Sensor\SensorReadingTypesValidator\SensorReadingTypesValidatorInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;

class UpdateCurrentSensorReadingsHandler implements UpdateCurrentSensorReadingInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorReadingUpdateFactory $readingUpdateFactory;

    private SensorReadingTypesValidatorInterface $readingTypesValidator;

    private SensorOutOfBoundsHandlerInterface $outOfBoundsSensorService;

    private SensorConstantlyRecordHandlerInterface $constantlyRecordService;

    private ReadingTypeQueryFactory $readingTypeQueryBuilderFactory;

    private LoggerInterface $logger;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorReadingUpdateFactory $readingUpdateFactory,
        SensorReadingTypesValidatorInterface $readingTypesValidator,
        SensorOutOfBoundsHandlerInterface $outOfBoundsSensorService,
        SensorConstantlyRecordHandlerInterface $constantlyRecordService,
        ReadingTypeQueryFactory $readingTypeQueryBuilderFactory,
        LoggerInterface $elasticLogger,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->readingUpdateFactory = $readingUpdateFactory;
        $this->readingTypesValidator = $readingTypesValidator;
        $this->outOfBoundsSensorService = $outOfBoundsSensorService;
        $this->constantlyRecordService = $constantlyRecordService;
        $this->readingTypeQueryBuilderFactory = $readingTypeQueryBuilderFactory;
        $this->logger = $elasticLogger;
    }

    public function handleUpdateSensorCurrentReading(
        UpdateSensorCurrentReadingTransportMessageDTO $updateSensorCurrentReadingConsumerDTO,
    ): array {
        foreach ($updateSensorCurrentReadingConsumerDTO->getCurrentReadings() as $currentReadingUpdateDTO) {
            $readingTypeQueryDTOBuilder = $this->readingTypeQueryBuilderFactory->getReadingTypeQueryDTOBuilder($currentReadingUpdateDTO->getReadingType());
            $sensorReadingTypeQueryDTOs[] = $readingTypeQueryDTOBuilder->buildReadingTypeJoinQueryDTO();
        }

        if (empty($sensorReadingTypeQueryDTOs)) {
            return ['No reading types found'];
        }
        $sensorReadingObjects = $this->sensorRepository->findSensorTypeAndReadingTypeObjectsForSensor(
            $updateSensorCurrentReadingConsumerDTO->getDeviceID(),
            $updateSensorCurrentReadingConsumerDTO->getSensorName(),
            null,
            $sensorReadingTypeQueryDTOs,
        );
        if (empty($sensorReadingObjects)) {
            throw new SensorNotFoundException(sprintf(SensorNotFoundException::SENSOR_NOT_FOUND_WITH_SENSOR_NAME, $updateSensorCurrentReadingConsumerDTO->getSensorName()));
        }
        foreach ($sensorReadingObjects as $sensorReadingObject) {
            /** @var AbstractCurrentReadingUpdateRequestDTO $currentReadingDTO */
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

                    /** @var ReadingTypeUpdateCurrentReadingDTO $updateReadingTypeCurrentReadingDTO */
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
                        if ($sensorReadingObject instanceof StandardReadingSensorInterface) {
                            $this->outOfBoundsSensorService->processOutOfBounds($sensorReadingObject);
                            $this->constantlyRecordService->processConstRecord($sensorReadingObject);
                        }
                        if ($sensorReadingObject instanceof BoolReadingSensorInterface) {
                            $sensorReadingObject->setRequestedReading($updateReadingTypeCurrentReadingDTO->getCurrentReading());
                        }
                    }
                } catch (
                    ReadingTypeNotExpectedException
                    | SensorReadingUpdateFactoryException
                    | ReadingTypeObjectBuilderException $e
                ) {
                    $this->logger->error($e->getMessage());
                    continue;
                }
            }
        }
        try {
            $this->sensorRepository->flush();
        } catch (ORMException|OptimisticLockException $e) {
            $this->logger->error($e->getMessage());

            return $validationErrors ?? [];
        }

        return $validationErrors ?? [];
    }
}
