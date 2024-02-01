<?php

namespace App\Sensors\DTO\Internal\Trigger;

use App\Common\Entity\Operator;
use App\Common\Entity\TriggerType;
use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\SensorTrigger;
use App\User\Entity\User;

readonly class CreateNewTriggerDTO
{
    private SensorTrigger $sensorTrigger;

    public function __construct(
        private Operator $operator,
        private TriggerType $triggerType,
        private float $valueThatTriggers,
        private array $days,
        private bool $monday,
        private bool $tuesday,
        private bool $wednesday,
        private bool $thursday,
        private bool $friday,
        private bool $saturday,
        private bool $sunday,
        private int $startTime,
        private int $endTime,
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

    public function getDays(): array
    {
        return $this->days;
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

    public function getSensorTrigger(): SensorTrigger
    {
        return $this->sensorTrigger;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function getEndTime(): int
    {
        return $this->endTime;
    }
}
