<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Entity\Devices;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\ConstantlyRecord\SensorConstantlyRecordHandlerInterface;
use App\Sensors\SensorServices\OutOfBounds\SensorOutOfBoundsHandlerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class UpdateCurrentSensorReadingsHandlerVersionTwo implements UpdateCurrentSensorReadingInterface
{
    use ValidatorProcessorTrait;

    public function __construct(
        private ValidatorInterface $validator,
        private SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory,
        private SensorOutOfBoundsHandlerInterface $outOfBoundsSensorService,
        private SensorConstantlyRecordHandlerInterface $constantlyRecordService,
        private SensorRepositoryInterface $sensorRepository,
    ) {}

    /**
     * @throws SensorNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[ArrayShape(["errors"])]
    public function handleUpdateSensorCurrentReading(
        UpdateSensorCurrentReadingMessageDTO $updateSensorCurrentReadingConsumerDTO,
        Devices $device,
    ): array {
        $validationErrors = [];
        foreach ($updateSensorCurrentReadingConsumerDTO->getCurrentReadings() as $currentReadingUpdateRequestDTO) {
            $currentValidationErrors = $this->validator->validate(value: $currentReadingUpdateRequestDTO, groups: [$updateSensorCurrentReadingConsumerDTO->getSensorType()]);
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
                    ...$this->getValidationErrorAsArray($validationErrors),
                    ...$validationErrors
                ];
                continue;
            }

            $this->constantlyRecordService->processConstRecord($readingTypeObject);
            if ($readingTypeObject instanceof StandardReadingSensorInterface) {
                $this->outOfBoundsSensorService->processOutOfBounds($readingTypeObject);
            }
        }

        $this->sensorRepository->flush();

        return $validationErrors;
    }
}
