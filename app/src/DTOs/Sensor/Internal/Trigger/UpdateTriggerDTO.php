<?php

namespace App\DTOs\Sensor\Internal\Trigger;

use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class UpdateTriggerDTO
{
    public function __construct(
        private ?int $operator = null,
        private ?int $triggerType = null,
        private ?BaseSensorReadingType $baseReadingTypeThatTriggers = null,
        private ?BaseSensorReadingType $baseReadingTypeThatIsTriggered = null,
        private ?array $days = null,
        private bool|float|int|null $valueThatTriggers = null,
        private int|null $startTime = null,
        private int|null $endTime = null,
        private ?bool $override = null
    ) {
    }

    public function getOperator(): ?int
    {
        return $this->operator;
    }


    public function getTriggerType(): ?int
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


    public function getDays(): ?array
    {
        return $this->days;
    }

    public function getValueThatTriggers(): float|bool|int|null
    {
        return $this->valueThatTriggers;
    }


    public function getStartTime(): int|string|null
    {
        return $this->startTime;
    }

    public function getEndTime(): int|string|null
    {
        return $this->endTime;
    }

    public function getOverride(): ?bool
    {
        return $this->override;
    }

}
