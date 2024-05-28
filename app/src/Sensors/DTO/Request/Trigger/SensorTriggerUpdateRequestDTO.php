<?php

namespace App\Sensors\DTO\Request\Trigger;

use App\Sensors\Entity\SensorTrigger;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SensorTriggerUpdateRequestDTO
{
    #[
        Assert\Type(type: ['int', 'null'], message: 'operator must be an {{ type }} you have provided {{ value }}')
    ]
    private mixed $operator = null;

    #[
        Assert\Type(type: ['int', 'null'], message: 'trigger type must be an {{ type }} you have provided {{ value }}'),
    ]
    private mixed $triggerType = null;

    #[
        Assert\Type(type: ['int', 'null'], message: 'base reading type that triggers must be an {{ type }} you have provided {{ value }}')
    ]
    private mixed $baseReadingTypeThatTriggers = null;

    #[
        Assert\Type(type: ['int', 'null'], message: 'base reading type that is triggered must be an {{ type }} you have provided {{ value }}')
    ]
    private mixed $baseReadingTypeThatIsTriggered = null;

    #[
        Assert\Type(type: ['array', 'null'], message: 'days must be an {{ type }} you have provided {{ value }}'),
        Assert\All(
            constraints: new Assert\Choice(
                choices: SensorTrigger::DAYS,
                message: 'Days must be of {{ choices }}',
            ),
        )
    ]
    private mixed $days = null;

    #[
        Assert\Type(type: ['bool', 'float', 'int', 'null'], message: 'value that triggers must be an {{ type }} you have provided {{ value }}')
    ]
    private mixed $valueThatTriggers = null;

    #[Assert\Sequentially([
    new Assert\Type(type: ['string', 'null'], message: 'start time must be an {{ type }} you have provided {{ value }}'),
    //            new Assert\Length(
    //                min: 1,
    //                max: 4,
    //                exactMessage: 'Start time must be in 24 hour format',
    //                maxMessage: 'Start time must be in 24 hour format',
    //            ),
    ])
    ]
    private mixed $startTime = null;

    #[
        Assert\Sequentially(constraints: [
            new Assert\Type(type: ['string', 'null'], message: 'end time must be an {{ type }} you have provided {{ value }}'),
//            new Assert\Length(
//                min: 1,
//                max: 4,
//                exactMessage: 'End time must be in 24 hour format',
//                maxMessage: 'End Time must be in 24 hour format',
//            ),
        ])
    ]
    private mixed $endTime = null;

    #[Assert\Type(type: ['bool', 'null'], message: 'override must be an {{ type }} you have provided {{ value }}')]
    private mixed $override = null;

    public function getOperator(): mixed
    {
        return $this->operator;
    }

    public function setOperator(mixed $operator): void
    {
        $this->operator = $operator;
    }

    public function getTriggerType(): mixed
    {
        return $this->triggerType;
    }

    public function setTriggerType(mixed $triggerType): void
    {
        $this->triggerType = $triggerType;
    }

    public function getBaseReadingTypeThatTriggers(): mixed
    {
        return $this->baseReadingTypeThatTriggers;
    }

    public function setBaseReadingTypeThatTriggers(mixed $baseReadingTypeThatTriggers): void
    {
        $this->baseReadingTypeThatTriggers = $baseReadingTypeThatTriggers;
    }

    public function getBaseReadingTypeThatIsTriggered(): mixed
    {
        return $this->baseReadingTypeThatIsTriggered;
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

    public function getEndTime(): mixed
    {
        return $this->endTime;
    }

    public function setEndTime(mixed $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function getOverride(): mixed
    {
        return $this->override;
    }

    public function setOverride(mixed $override): void
    {
        $this->override = $override;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if (($this->getEndTime() && $this->getStartTime()) && $this->getEndTime() < $this->getStartTime()) {
            $context
                ->buildViolation('Start time cannot be greater than end time')
                ->addViolation();
        }
    }
}
