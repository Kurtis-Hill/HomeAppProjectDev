<?php

namespace App\DTOs\Sensor\Request;

use App\Entity\Sensor\Sensor;
use Symfony\Component\Validator\Constraints as Assert;

class AddNewSensorRequestDTO
{
    #[
        Assert\Type(type: 'integer', message: 'sensorTypeID must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "sensorTypeID cannot be null"
        ),
    ]
    private int $sensorTypeID;

    #[
        Assert\Type(type: 'integer', message: 'deviceNameID must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "deviceID name cannot be null"
        ),
    ]
    private int $deviceID;

    #[
        Assert\Type(type: 'string', message: 'sensorName must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "sensorName name cannot be null"
        ),
    ]
    private string $sensorName;

    #[
        Assert\Range(
            notInRangeMessage: 'pinNumber must be greater than {{ min }}',
            minMessage: 'pinNumber must be greater than {{ value }}',
            invalidMessage: 'pinNumber must be an int you have provided {{ value }}',
            min: 0,
        ),
        Assert\NotNull(
            message: "pinNumber name cannot be null"
        ),
    ]
    private int $pinNumber;

    #[
        Assert\NotNull(
            message: "readingInterval cannot be null"
        ),
        Assert\Range(
            notInRangeMessage: "readingInterval must be greater than {{ min }}",
            invalidMessage: "readingInterval must be a number",
            min: Sensor::MIN_READING_INTERVAL
        ),
    ]
    private int $readingInterval;

    public function getSensorTypeID(): int
    {
        return $this->sensorTypeID;
    }

    public function setSensorTypeID(int $sensorTypeID): void
    {
        $this->sensorTypeID = $sensorTypeID;
    }

    public function getDeviceID(): int
    {
        return $this->deviceID;
    }

    public function setDeviceID(int $deviceID): void
    {
        $this->deviceID = $deviceID;
    }

    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    public function setSensorName(string $sensorName): void
    {
        $this->sensorName = $sensorName;
    }

    public function getPinNumber(): int
    {
        return $this->pinNumber;
    }

    public function setPinNumber(int $pinNumber): void
    {
        $this->pinNumber = $pinNumber;
    }

    public function getReadingInterval(): int
    {
        return $this->readingInterval;
    }

    public function setReadingInterval(int $readingInterval): void
    {
        $this->readingInterval = $readingInterval;
    }
}
