<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\ConstantlyRecord\SensorConstantlyRecordHandlerInterface;
use App\Sensors\SensorServices\OutOfBounds\SensorOutOfBoundsHandlerInterface;
use App\Sensors\SensorServices\TriggerChecker\SensorReadingTriggerCheckerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class UpdateCurrentSensorReadingsHandlerVersionTwo implements UpdateCurrentSensorReadingInterface
{
    use ValidatorProcessorTrait;

    public function __construct(
        private ValidatorInterface $validator,
        private SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory,
        private SensorOutOfBoundsHandlerInterface $outOfBoundsSensorService,
        private SensorConstantlyRecordHandlerInterface $constantlyRecordService,
        private SensorReadingTriggerCheckerInterface $sensorReadingTriggerChecker,
        private SensorRepositoryInterface $sensorRepository,
        private LoggerInterface $elasticLogger,
    ) {}

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws SensorNotFoundException
     */
    #[ArrayShape(["errors"])]
    public function handleUpdateSensorCurrentReading(
        UpdateSensorCurrentReadingMessageDTO $updateSensorCurrentReadingConsumerDTO,
    ): array {
        $validationErrors = [];
        $changed = [];
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
//            dd($readingTypeObject);
            $changed[] = $readingTypeObject;
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
                    ...$this->getValidationErrorAsArray($validationErrors),
                    ...$validationErrors
                ];
                continue;
            }

            try {
                $this->constantlyRecordService->processConstRecord($readingTypeObject);
            } catch (\Throwable $th) {
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
                } catch (\Throwable $th) {
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
        }

//        dd('eh',
//            $changed,
//            $updateSensorCurrentReadingConsumerDTO
//        );
        try {

        $this->sensorRepository->flush();
        } catch (\Exception $exception) {
            dd($exception);
            dd('exception');
        }

        return $validationErrors;
    }
}
