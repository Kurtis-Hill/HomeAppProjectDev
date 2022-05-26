<?php

namespace App\Sensors\Entity\ReadingTypes;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractReadingType
{
    protected const HIGHER_LOWER_THAN_LOWER = 'High reading for %s cannot be lower than low reading';

    abstract public function getSensorID(): int;

    abstract public function getReadingType(): string;

    abstract public function getCurrentReading(): int|float;

    abstract public function getHighReading(): int|float;

    abstract public function getLowReading(): int|float;

    public function isReadingOutOfBounds(): bool
    {
        return $this->getCurrentReading() >= $this->getHighReading()
            || $this->getCurrentReading() <= $this->getLowReading();
    }

    public function getMeasurementDifferenceHighReading(): int|float
    {
        return $this->getHighReading() - $this->getCurrentReading();
    }

    public function getMeasurementDifferenceLowReading(): int|float
    {
        return $this->getLowReading() - $this->getCurrentReading();
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->getHighReading() < $this->getLowReading()) {
            $context
                ->buildViolation(sprintf(self::HIGHER_LOWER_THAN_LOWER, $this->getReadingType()))
                ->addViolation();
        }
    }
}
