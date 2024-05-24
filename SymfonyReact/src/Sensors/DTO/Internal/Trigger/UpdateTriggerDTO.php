<?php

namespace App\Sensors\DTO\Internal\Trigger;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
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
        private ?string $startTime = null,
        private ?string $endTime = null,
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


    public function getStartTime(): ?int
    {
        return $this->startTime;
    }

    public function getEndTime(): ?int
    {
        return $this->endTime;
    }

    public function getOverride(): ?bool
    {
        return $this->override;
    }

}
