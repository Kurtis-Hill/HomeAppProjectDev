<?php

namespace App\Services\Sensor\Trigger\UpdateTrigger;

use App\DTOs\Sensor\Internal\Trigger\UpdateTriggerDTO;
use App\Entity\Sensor\SensorTrigger;
use App\Repository\Common\ORM\OperatorRepository;
use App\Repository\Sensor\ReadingType\ORM\BaseSensorReadingTypeRepository;
use App\Repository\Sensor\TriggerTypeRepository;
use App\Services\API\APIErrorMessages;
use App\Services\Sensor\Trigger\TriggerHelpers\TriggerDateTimeConvertor;
use App\Traits\ValidatorProcessorTrait;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateTriggerHandler
{
    use ValidatorProcessorTrait;

    private TriggerTypeRepository $triggerTypeRepository;

    private OperatorRepository $operatorRepository;

    private BaseSensorReadingTypeRepository $baseSensorReadingTypeRepository;

    private ValidatorInterface $validator;

    public function __construct(
        TriggerTypeRepository $triggerTypeRepository,
        OperatorRepository $operatorRepository,
        BaseSensorReadingTypeRepository $baseSensorReadingTypeRepository,
        ValidatorInterface $validator,
    ) {
        $this->triggerTypeRepository = $triggerTypeRepository;
        $this->operatorRepository = $operatorRepository;
        $this->baseSensorReadingTypeRepository = $baseSensorReadingTypeRepository;
        $this->validator = $validator;
    }

    /**
     * @throws InvalidArgumentException
     */
    #[ArrayShape(['validationErrors'])]
    public function handleUpdateOfTrigger(SensorTrigger $sensorTriggerToUpdate, UpdateTriggerDTO $updateTriggerDTO): array
    {
        $validationErrors = [];
        if ($updateTriggerDTO->getTriggerType() !== null) {
            $newTriggerType = $this->triggerTypeRepository->find($updateTriggerDTO->getTriggerType());
            if (!$newTriggerType) {
                $validationErrors[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Trigger type');
            } else {
                $sensorTriggerToUpdate->setTriggerType($newTriggerType);
            }
        }
        if ($updateTriggerDTO->getValueThatTriggers() !== null) {
            $sensorTriggerToUpdate->setValueThatTriggers((string)$updateTriggerDTO->getValueThatTriggers());
        }
        if (!empty($updateTriggerDTO->getDays())) {
            foreach ($updateTriggerDTO->getDays() as $day) {
                match (TriggerDateTimeConvertor::prepareDays($day)) {
                    "monday" => $monday = true,
                    "tuesday" => $tuesday = true,
                    "wednesday" => $wednesday = true,
                    "thursday" => $thursday = true,
                    "friday" => $friday = true,
                    "saturday" => $saturday = true,
                    "sunday" => $sunday = true,
                    default => throw new InvalidArgumentException('Invalid day'),
                };
            }
            $sensorTriggerToUpdate->setMonday($monday ?? false);
            $sensorTriggerToUpdate->setTuesday($tuesday ?? false);
            $sensorTriggerToUpdate->setWednesday($wednesday ?? false);
            $sensorTriggerToUpdate->setThursday($thursday ?? false);
            $sensorTriggerToUpdate->setFriday($friday ?? false);
            $sensorTriggerToUpdate->setSaturday($saturday ?? false);
            $sensorTriggerToUpdate->setSunday($sunday ?? false);
        }

        if ($updateTriggerDTO->getOperator() !== null) {
            $newOperator = $this->operatorRepository->find($updateTriggerDTO->getOperator());
            if (!$newOperator) {
                $validationErrors[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Operator');
            } else {
                $sensorTriggerToUpdate->setOperator($newOperator);
            }
        }

        if ($updateTriggerDTO->getStartTime() !== null) {
            $sensorTriggerToUpdate->setStartTime($updateTriggerDTO->getStartTime());
        }
        if ($updateTriggerDTO->getEndTime() !== null) {
            $sensorTriggerToUpdate->setEndTime($updateTriggerDTO->getEndTime());
        }

        if ($updateTriggerDTO->getBaseReadingTypeThatIsTriggered() !== null) {
            $readingTypeThatIsTriggered = $this->baseSensorReadingTypeRepository->find($updateTriggerDTO->getBaseReadingTypeThatIsTriggered());
            if (!$readingTypeThatIsTriggered) {
                $validationErrors[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'BaseReadingType That Is Triggered');
            } else {
                $sensorTriggerToUpdate->setBaseReadingTypeToTrigger($updateTriggerDTO->getBaseReadingTypeThatIsTriggered());
            }
        }
        if ($updateTriggerDTO->getBaseReadingTypeThatTriggers() !== null) {
            $readingTypeToTrigger = $this->baseSensorReadingTypeRepository->find($updateTriggerDTO->getBaseReadingTypeThatTriggers());
            if (!$readingTypeToTrigger) {
                $validationErrors[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'BaseReadingType That Triggers');
            } else {
                $sensorTriggerToUpdate->setBaseReadingTypeThatTriggers($readingTypeToTrigger);
            }
        }

        if ($updateTriggerDTO->getOverride() !== null) {
            $sensorTriggerToUpdate->setOverride($updateTriggerDTO->getOverride());
        }

        $errors = $this->validator->validate($sensorTriggerToUpdate);
        if ($this->checkIfErrorsArePresent($errors)) {
            $validationErrors = array_merge($validationErrors, $this->getValidationErrorAsArray($errors));
        }

        return $validationErrors;
    }
}
