<?php

namespace App\Builders\Sensor\Internal\Trigger;

use App\DTOs\Sensor\Internal\Trigger\UpdateTriggerDTO;
use App\DTOs\Sensor\Request\Trigger\SensorTriggerUpdateRequestDTO;
use App\Repository\Sensor\ReadingType\ORM\BaseSensorReadingTypeRepository;
use App\Services\Sensor\Trigger\TriggerHelpers\TriggerDateTimeConvertor;

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

        return new \App\DTOs\Sensor\Internal\Trigger\UpdateTriggerDTO(
            operator: $sensorTriggerUpdateRequestDTO->getOperator(),
            triggerType: $sensorTriggerUpdateRequestDTO->getTriggerType(),
            baseReadingTypeThatTriggers: $baseReadingTypeThatTriggers,
            baseReadingTypeThatIsTriggered: $baseReadingTypeThatIsTriggered,
            days: $sensorTriggerUpdateRequestDTO->getDays(),
            valueThatTriggers: $sensorTriggerUpdateRequestDTO->getValueThatTriggers(),
            startTime: $sensorTriggerUpdateRequestDTO->getStartTime() !== null ? TriggerDateTimeConvertor::prepareTimes($sensorTriggerUpdateRequestDTO->getStartTime()) : null,
            endTime: $sensorTriggerUpdateRequestDTO->getEndTime() !== null ? TriggerDateTimeConvertor::prepareTimes($sensorTriggerUpdateRequestDTO->getEndTime()) : null,
            override: $sensorTriggerUpdateRequestDTO->getOverride(),
        );
    }
}
