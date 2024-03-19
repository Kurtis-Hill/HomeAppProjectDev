<?php

namespace App\Sensors\Builders\Internal\Trigger;

use App\Common\Entity\Operator;
use App\Common\Entity\TriggerType;
use App\Common\Repository\OperatorRepository;
use App\Common\Repository\TriggerTypeRepository;
use App\Sensors\DTO\Internal\Trigger\CreateNewTriggerDTO;
use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Exceptions\BaseReadingTypeNotFoundException;
use App\Sensors\Exceptions\OperatorNotFoundException;
use App\Sensors\Exceptions\TriggerTypeNotFoundException;
use App\Sensors\Repository\ReadingType\ORM\BaseSensorReadingTypeRepository;
use App\Sensors\SensorServices\Trigger\TriggerHelpers\TriggerDateTimeConvertor;
use App\User\Entity\User;

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
     * @throws BaseReadingTypeNotFoundException
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
    ): CreateNewTriggerDTO {
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
            match (TriggerDateTimeConvertor::prepareDaysForComparison($day)) {
                "monday" => $monday = true,
                "tuesday" => $tuesday = true,
                "wednesday" => $wednesday = true,
                "thursday" => $thursday = true,
                "friday" => $friday = true,
                "saturday" => $saturday = true,
                "sunday" => $sunday = true,
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
