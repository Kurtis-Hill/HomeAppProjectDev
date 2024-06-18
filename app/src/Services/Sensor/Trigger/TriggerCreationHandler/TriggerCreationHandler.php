<?php

namespace App\Services\Sensor\Trigger\TriggerCreationHandler;

use App\DTOs\Sensor\Internal\Trigger\CreateNewTriggerDTO;
use App\Repository\Sensor\SensorTriggerRepository;
use App\Traits\ValidatorProcessorTrait;
use DateTimeImmutable;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class TriggerCreationHandler implements TriggerCreationHandlerInterface
{
    use ValidatorProcessorTrait;

    public function __construct(
        private ValidatorInterface $validator,
        private SensorTriggerRepository $sensorTriggerRepository,
    ) {
    }

    public function createTrigger(CreateNewTriggerDTO $createNewTriggerDTO): array
    {
        $newSensorType = $createNewTriggerDTO->getNewSensorTrigger();

        $now = new DateTimeImmutable('now');
        $newSensorType->setTriggerType($createNewTriggerDTO->getTriggerType());
        $newSensorType->setOperator($createNewTriggerDTO->getOperator());
        $newSensorType->setValueThatTriggers((string)$createNewTriggerDTO->getValueThatTriggers());
        $newSensorType->setMonday($createNewTriggerDTO->getMonday());
        $newSensorType->setTuesday($createNewTriggerDTO->getTuesday());
        $newSensorType->setWednesday($createNewTriggerDTO->getWednesday());
        $newSensorType->setThursday($createNewTriggerDTO->getThursday());
        $newSensorType->setFriday($createNewTriggerDTO->getFriday());
        $newSensorType->setSaturday($createNewTriggerDTO->getSaturday());
        $newSensorType->setSunday($createNewTriggerDTO->getSunday());
        $newSensorType->setStartTime($createNewTriggerDTO->getStartTime());
        $newSensorType->setEndTime($createNewTriggerDTO->getEndTime());
        $newSensorType->setCreatedBy($createNewTriggerDTO->getCreatedBy());
        $newSensorType->setBaseReadingTypeThatTriggers($createNewTriggerDTO->getBaseReadingTypeThatTriggers());
        $newSensorType->setBaseReadingTypeToTrigger($createNewTriggerDTO->getBaseReadingTypeThatIsTriggered());
        $newSensorType->setOverride(false);
        $newSensorType->setCreatedAt(clone $now);
        $newSensorType->setUpdatedAt(clone $now);

        $validationErrors = $this->validator->validate($newSensorType);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->getValidationErrorAsArray($validationErrors);
        }

        $this->sensorTriggerRepository->persist($newSensorType);
        $this->sensorTriggerRepository->flush();

        return [];
    }
}
