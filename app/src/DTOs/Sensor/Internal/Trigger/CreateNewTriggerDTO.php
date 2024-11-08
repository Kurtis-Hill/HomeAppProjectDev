<?php

namespace App\DTOs\Sensor\Internal\Trigger;

use App\Entity\Common\Operator;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\SensorTrigger;
use App\Entity\Sensor\TriggerType;
use App\Entity\User\User;

readonly class CreateNewTriggerDTO
{
    private SensorTrigger $sensorTrigger;

    public function __construct(
        private Operator $operator,
        private TriggerType $triggerType,
        private float $valueThatTriggers,
        private bool $monday,
        private bool $tuesday,
        private bool $wednesday,
        private bool $thursday,
        private bool $friday,
        private bool $saturday,
        private bool $sunday,
        private null|int|float $startTime,
        private null|int|float $endTime,
        private User $createdBy,
        private ?BaseSensorReadingType $baseReadingTypeThatTriggers,
        private ?BaseSensorReadingType $baseReadingTypeThatIsTriggered,
    ) {
        $this->sensorTrigger = new SensorTrigger();
    }

    public function getOperator(): Operator
    {
        return $this->operator;
    }

    public function getTriggerType(): TriggerType
    {
        return $this->triggerType;
    }

    public function getBaseReadingTypeThatTriggers(): ?BaseSensorReadingType
    {
        return $this->baseReadingTypeThatTriggers;
    }

    public function getBaseReadingTypeThatIsTriggered(): ?BaseSensorReadingType
    {
        return $this->baseReadingTypeThatIsTriggered;
    }

    public function getValueThatTriggers(): float
    {
        return $this->valueThatTriggers;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function getMonday(): bool
    {
        return $this->monday;
    }

    public function getTuesday(): bool
    {
        return $this->tuesday;
    }

    public function getWednesday(): bool
    {
        return $this->wednesday;
    }

    public function getThursday(): bool
    {
        return $this->thursday;
    }

    public function getFriday(): bool
    {
        return $this->friday;
    }

    public function getSaturday(): bool
    {
        return $this->saturday;
    }

    public function getSunday(): bool
    {
        return $this->sunday;
    }

    public function getNewSensorTrigger(): SensorTrigger
    {
        return $this->sensorTrigger;
    }

    public function getStartTime(): ?int
    {
        return $this->startTime;
    }

    public function getEndTime(): ?int
    {
        return $this->endTime;
    }
}
