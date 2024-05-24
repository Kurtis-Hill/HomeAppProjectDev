<?php

namespace App\Sensors\Builders\Internal\Trigger;

use App\Sensors\DTO\Internal\Trigger\UpdateTriggerDTO;
use App\Sensors\DTO\Request\Trigger\SensorTriggerUpdateRequestDTO;
use App\Sensors\Repository\ReadingType\ORM\BaseSensorReadingTypeRepository;

readonly class UpdateTriggerDTOBuilder
{
    public function __construct(private BaseSensorReadingTypeRepository $baseSensorReadingTypeRepository)
    {}

    public function buildTriggerUpdateDTO(SensorTriggerUpdateRequestDTO $sensorTriggerUpdateRequestDTO): UpdateTriggerDTO
    {
        $baseReadingTypeThatTriggers = $sensorTriggerUpdateRequestDTO->getBaseReadingTypeThatTriggers() !== null
            ? $this->baseSensorReadingTypeRepository->find($sensorTriggerUpdateRequestDTO->getBaseReadingTypeThatTriggers())
            : null;

        $baseReadingTypeThatIsTriggered = $sensorTriggerUpdateRequestDTO->getBaseReadingTypeThatIsTriggered() !== null
            ? $this->baseSensorReadingTypeRepository->find($sensorTriggerUpdateRequestDTO->getBaseReadingTypeThatIsTriggered())
            : null;

        return new UpdateTriggerDTO(
            operator: $sensorTriggerUpdateRequestDTO->getOperator(),
            triggerType: $sensorTriggerUpdateRequestDTO->getTriggerType(),
            baseReadingTypeThatTriggers: $baseReadingTypeThatTriggers,
            baseReadingTypeThatIsTriggered: $baseReadingTypeThatIsTriggered,
            days: $sensorTriggerUpdateRequestDTO->getDays(),
            valueThatTriggers: $sensorTriggerUpdateRequestDTO->getValueThatTriggers(),
            startTime: $sensorTriggerUpdateRequestDTO->getStartTime(),
            endTime: $sensorTriggerUpdateRequestDTO->getEndTime(),
            override: $sensorTriggerUpdateRequestDTO->getOverride(),
        );
    }
}
