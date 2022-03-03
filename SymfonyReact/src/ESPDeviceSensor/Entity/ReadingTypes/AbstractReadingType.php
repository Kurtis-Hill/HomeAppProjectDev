<?php

namespace App\ESPDeviceSensor\Entity\ReadingTypes;

abstract class AbstractReadingType
{
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
}
