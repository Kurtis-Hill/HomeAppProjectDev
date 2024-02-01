<?php

namespace App\Sensors\Builders\Trigger;

use App\Common\Repository\OperatorRepository;
use App\Common\Repository\TriggerTypeRepository;
use App\Sensors\DTO\Internal\Trigger\CreateNewTriggerDTO;
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

    public function buildCreateNewTriggerDTO(
        int $operatorID,
        int $triggerTypeID,
        float $valueThatTriggers,
        array $days,
        float $startTime,
        float $endTime,
        User $createdBy,
        ?int $baseReadingTypeThatTriggersID,
        ?int $baseReadingTypeThatIsTriggeredID,
    ): CreateNewTriggerDTO {
        $operator = $this->operatorRepository->find($operatorID);
        $triggerType = $this->triggerTypeRepository->find($triggerTypeID);
        $baseReadingTypeThatTriggers = $baseReadingTypeThatTriggersID !== null ? $this->baseSensorReadingTypeRepository->find($baseReadingTypeThatTriggersID) : null;
        $baseReadingTypeThatIsTriggered = $baseReadingTypeThatIsTriggeredID !== null ? $this->baseSensorReadingTypeRepository->find($baseReadingTypeThatIsTriggeredID) : null;

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

        return new CreateNewTriggerDTO(
            $operator,
            $triggerType,
            $valueThatTriggers,
            $days,
            $monday,
            $tuesday,
            $wednesday,
            $thursday,
            $friday,
            $saturday,
            $sunday,
            $createdBy,
            $baseReadingTypeThatTriggers,
            $baseReadingTypeThatIsTriggered,
        );
    }
}
