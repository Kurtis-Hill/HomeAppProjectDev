<?php

namespace App\Builders\Sensor\Internal\Trigger;

use App\DTOs\Sensor\Internal\Trigger\CreateNewTriggerDTO;
use App\Entity\Common\Operator;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\TriggerType;
use App\Entity\User\User;
use App\Exceptions\Sensor\BaseReadingTypeNotFoundException;
use App\Exceptions\Sensor\OperatorNotFoundException;
use App\Exceptions\Sensor\TriggerTypeNotFoundException;
use App\Repository\Common\ORM\OperatorRepository;
use App\Repository\Sensor\ReadingType\ORM\BaseSensorReadingTypeRepository;
use App\Repository\Sensor\TriggerTypeRepository;
use App\Services\Sensor\Trigger\TriggerHelpers\TriggerDateTimeConvertor;
use InvalidArgumentException;

readonly class CreateNewTriggerDTOBuilder
{
    public function __construct(
        private OperatorRepository $operatorRepository,
        private TriggerTypeRepository $triggerTypeRepository,
        private BaseSensorReadingTypeRepository $baseSensorReadingTypeRepository,
    ) {
    }

    /**
     * @throws OperatorNotFoundException
     * @throws TriggerTypeNotFoundException
     * @throws \App\Exceptions\Sensor\BaseReadingTypeNotFoundException
     * @throws InvalidArgumentException
     */
    public function buildCreateNewTriggerDTOFromValues(
        int $operatorID,
        int $triggerTypeID,
        float $valueThatTriggers,
        array $days,
        User $createdBy,
        ?float $startTime,
        ?float $endTime,
        ?int $baseReadingTypeThatTriggersID,
        ?int $baseReadingTypeThatIsTriggeredID,
    ): \App\DTOs\Sensor\Internal\Trigger\CreateNewTriggerDTO {
        $operator = $this->operatorRepository->find($operatorID);
        if ($operator === null) {
            throw new OperatorNotFoundException(
                sprintf(
                    OperatorNotFoundException::MESSAGE,
                    $operatorID
                )
            );
        }

        $triggerType = $this->triggerTypeRepository->find($triggerTypeID);
        if ($triggerType === null) {
            throw new TriggerTypeNotFoundException(
                sprintf(
                    TriggerTypeNotFoundException::MESSAGE,
                    $triggerTypeID
                )
            );
        }

        if ($baseReadingTypeThatTriggersID !== null) {
            $baseReadingTypeThatTriggers = $this->baseSensorReadingTypeRepository->find($baseReadingTypeThatTriggersID);
            if ($baseReadingTypeThatTriggers === null) {
                throw new BaseReadingTypeNotFoundException(
                    sprintf(
                        BaseReadingTypeNotFoundException::MESSAGE,
                        $baseReadingTypeThatTriggersID
                    )
                );
            }
        }

        if ($baseReadingTypeThatIsTriggeredID !== null) {
            $baseReadingTypeThatIsTriggered = $this->baseSensorReadingTypeRepository->find($baseReadingTypeThatIsTriggeredID);
            if ($baseReadingTypeThatIsTriggered === null) {
                throw new BaseReadingTypeNotFoundException(
                    sprintf(
                        BaseReadingTypeNotFoundException::MESSAGE,
                        $baseReadingTypeThatIsTriggeredID
                    )
                );
            }
        }

        $monday = false;
        $tuesday = false;
        $wednesday = false;
        $thursday = false;
        $friday = false;
        $saturday = false;
        $sunday = false;

        foreach ($days as $day) {
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

        return self::buildCreateNewTriggerDTO(
            $operator,
            $triggerType,
            $valueThatTriggers,
            $monday,
            $tuesday,
            $wednesday,
            $thursday,
            $friday,
            $saturday,
            $sunday,
            $startTime,
            $endTime,
            $createdBy,
            $baseReadingTypeThatTriggers ?? null,
            $baseReadingTypeThatIsTriggered ?? null,
        );
    }

    public static function buildCreateNewTriggerDTO(
        Operator $operator,
        TriggerType $triggerType,
        float $valueThatTriggers,
        bool $monday,
        bool $tuesday,
        bool $wednesday,
        bool $thursday,
        bool $friday,
        bool $saturday,
        bool $sunday,
        ?float $startTime,
        ?float $endTime,
        User $createdBy,
        ?BaseSensorReadingType $baseReadingTypeThatTriggers,
        ?BaseSensorReadingType $baseReadingTypeThatIsTriggered,
    ): CreateNewTriggerDTO {
        return new CreateNewTriggerDTO(
            $operator,
            $triggerType,
            $valueThatTriggers,
            $monday,
            $tuesday,
            $wednesday,
            $thursday,
            $friday,
            $saturday,
            $sunday,
            $startTime,
            $endTime,
            $createdBy,
            $baseReadingTypeThatTriggers ?? null,
            $baseReadingTypeThatIsTriggered ?? null,
        );
    }
}
