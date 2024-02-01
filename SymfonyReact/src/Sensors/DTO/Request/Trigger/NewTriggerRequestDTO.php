<?php

namespace App\Sensors\DTO\Request\Trigger;

use App\Sensors\Entity\SensorTrigger;
use App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading\CurrentReadingSensorDataRequestHandlerInterface;
use Symfony\Component\Validator\Constraints as Assert;

class NewTriggerRequestDTO
{
    #[
        Assert\Type(type: ['int'], message: 'operator must be an {{ type }} you have provided {{ value }}')
    ]
    private mixed $operator;

    #[
        Assert\Type(type: ['int'], message: 'trigger type must be an {{ type }} you have provided {{ value }}')
    ]
    private mixed $triggerType;

    #[
        Assert\Type(type: ['int', 'null'], message: 'reading type that triggers must be an {{ type }} you have provided {{ value }}')
    ]
    private mixed $baseReadingTypeThatTriggers;

    #[
        Assert\Type(type: ['int', 'null'], message: 'reading type that is triggered must be an {{ type }} you have provided {{ value }}')
    ]
    private mixed $baseReadingTypeThatIsTriggered;

    #[
        Assert\Type(type: ['array', 'null'], message: 'days must be an {{ type }} you have provided {{ value }}'),
        Assert\Choice(
            choices: SensorTrigger::DAYS,
            message: 'Days must be of {{ choices }}',
            groups: [CurrentReadingSensorDataRequestHandlerInterface::UPDATE_CURRENT_READING]
        ),
    ]
    private mixed $days;

    #[
        Assert\Type(type: ['bool', 'float'], message: 'value that triggers must be an {{ type }} you have provided {{ value }}')
    ]
    private mixed $valueThatTriggers;

    #[
        Assert\Type(type: ['int'], message: 'start time must be an {{ type }} you have provided {{ value }}')
    ]
    private mixed $startTime;

    #[
        Assert\Type(type: ['int'], message: 'end time must be an {{ type }} you have provided {{ value }}')
    ]
    private mixed $endTime;

    public function getOperator(): mixed
    {
        return $this->operator;
    }

    public function getTriggerType(): mixed
    {
        return $this->triggerType;
    }

    public function getBaseReadingTypeThatTriggers(): mixed
    {
        return $this->baseReadingTypeThatTriggers;
    }

    public function getBaseReadingTypeThatIsTriggered(): mixed
    {
        return $this->baseReadingTypeThatIsTriggered;
    }

    public function setOperator(mixed $operator): void
    {
        $this->operator = $operator;
    }

    public function setTriggerType(mixed $triggerType): void
    {
        $this->triggerType = $triggerType;
    }

    public function setBaseReadingTypeThatTriggers(mixed $baseReadingTypeThatTriggers): void
    {
        $this->baseReadingTypeThatTriggers = $baseReadingTypeThatTriggers;
    }

    public function setBaseReadingTypeThatIsTriggered(mixed $baseReadingTypeThatIsTriggered): void
    {
        $this->baseReadingTypeThatIsTriggered = $baseReadingTypeThatIsTriggered;
    }

    public function getDays(): mixed
    {
        return $this->days;
    }

    public function setDays(mixed $days): void
    {
        $this->days = $days;
    }

    public function getValueThatTriggers(): mixed
    {
        return $this->valueThatTriggers;
    }

    public function setValueThatTriggers(mixed $valueThatTriggers): void
    {
        $this->valueThatTriggers = $valueThatTriggers;
    }

    public function getStartTime(): mixed
    {
        return $this->startTime;
    }

    public function setStartTime(mixed $startTime): void
    {
        $this->startTime = $startTime;
    }
}
