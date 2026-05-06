<?php

namespace App\Services\Sensor\SensorReadingUpdate\CurrentReading;

use App\DTOs\Sensor\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingTransportMessageDTO;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Exceptions\Sensor\SensorNotFoundException;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\Sensor\ConstantlyRecord\SensorConstantlyRecordHandlerInterface;
use App\Services\Sensor\OutOfBounds\SensorOutOfBoundsHandlerInterface;
use App\Services\Sensor\Trigger\SensorTriggerProcessor\ReadingTriggerHandlerInterface;
use App\Traits\ValidatorProcessorTrait;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

readonly class UpdateCurrentSensorReadingsHandlerVersionTwo implements UpdateCurrentSensorReadingInterface
{
    use ValidatorProcessorTrait;

    public function __construct(
        private ValidatorInterface $validator,
        private SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory,
        private SensorOutOfBoundsHandlerInterface $outOfBoundsSensorService,
        private SensorConstantlyRecordHandlerInterface $constantlyRecordService,
        private ReadingTriggerHandlerInterface $triggerHandler,
        private SensorRepositoryInterface $sensorRepository,
        private LoggerInterface $elasticLogger,
    ) {}

    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     * @throws SensorNotFoundException
     */
    #[ArrayShape(["errors"])]
    public function handleUpdateSensorCurrentReading(
        UpdateSensorCurrentReadingTransportMessageDTO $updateSensorCurrentReadingConsumerDTO,
    ): array {
        $validationErrors = [];
        foreach ($updateSensorCurrentReadingConsumerDTO->getCurrentReadings() as $currentReadingUpdateRequestDTO) {
            $currentValidationErrors = $this->validator->validate(
                value: $currentReadingUpdateRequestDTO,
                groups: [$updateSensorCurrentReadingConsumerDTO->getSensorType()]
            );
            if ($this->checkIfErrorsArePresent($currentValidationErrors)) {
                $validationErrors = [
                    ...$this->getValidationErrorAsArray($currentValidationErrors),
                    ...$validationErrors
                ];
                continue;
            }

            $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository(
                $currentReadingUpdateRequestDTO->getReadingType()
            );


            $readingTypeObject = $readingTypeRepository->findOneBySensorName($updateSensorCurrentReadingConsumerDTO->getSensorName());
            if ($readingTypeObject === null) {
                throw new SensorNotFoundException(
                    sprintf(
                        'Sensor with name %s not found',
                        $updateSensorCurrentReadingConsumerDTO->getSensorName()
                    )
                );
            }
            $readingTypeObject->setCurrentReading($currentReadingUpdateRequestDTO->getCurrentReading());
            $readingTypeObject->setUpdatedAt();

            $currentValidationErrors = $this->validator->validate(
                value: $readingTypeObject,
                groups: [$updateSensorCurrentReadingConsumerDTO->getSensorType()]
            );

            if ($this->checkIfErrorsArePresent($currentValidationErrors)) {
                $readingTypeRepository->refresh($readingTypeObject);
                $validationErrors = [
                    ...$this->getValidationErrorAsArray($currentValidationErrors),
                    ...$validationErrors
                ];
                continue;
            }

            try {
                $this->constantlyRecordService->processConstRecord($readingTypeObject);
            } catch (Throwable $th) {
                $this->elasticLogger->error(
                    'Error processing constantly record',
                    [
                        'exception' => $th,
                        'sensorName' => $readingTypeObject->getSensor()->getSensorName(),
                        'readingType' => $readingTypeObject->getReadingType(),
                        'currentReading' => $readingTypeObject->getCurrentReading(),
                    ]
                );
            }
            if ($readingTypeObject instanceof StandardReadingSensorInterface) {
                try {
                    $this->outOfBoundsSensorService->processOutOfBounds($readingTypeObject);
                } catch (Throwable $th) {

                    $this->elasticLogger->error(
                        'Error processing out of bounds',
                        [
                            'exception' => $th,
                            'sensorName' => $readingTypeObject->getSensor()->getSensorName(),
                            'readingType' => $readingTypeObject->getReadingType(),
                            'currentReading' => $readingTypeObject->getCurrentReading(),
                        ]
                    );
                }
            }
            if ($readingTypeObject instanceof BoolReadingSensorInterface) {
                $readingTypeObject->setRequestedReading($currentReadingUpdateRequestDTO->getCurrentReading());
            }

            $this->triggerHandler->handleTrigger($readingTypeObject);
        }

        try {
            $this->sensorRepository->flush();
        } catch (Exception $exception) {
            $this->elasticLogger->error(
                'Error flushing sensor repository',
                [
                    'exception' => $exception,
                    'sensorName' => $updateSensorCurrentReadingConsumerDTO->getSensorName(),
                ]
            );
        }

        return $validationErrors;
    }
}
