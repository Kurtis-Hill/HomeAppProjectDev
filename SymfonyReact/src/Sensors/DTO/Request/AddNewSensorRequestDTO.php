<?php

namespace App\Sensors\DTO\Request;

use App\Devices\DeviceServices\GetDevices\DevicesForUserInterface;
use App\Sensors\Entity\Sensor;
use Symfony\Component\Validator\Constraints as Assert;

class AddNewSensorRequestDTO
{
    #[
        Assert\Type(type: 'integer', message: 'sensorTypeID must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "sensorTypeID cannot be null"
        ),
    ]
    private mixed $sensorTypeID = null;

    #[
        Assert\Type(type: 'integer', message: 'deviceNameID must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "deviceID name cannot be null"
        ),
    ]
    private mixed $deviceID = null;

    #[
        Assert\Type(type: 'string', message: 'sensorName must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "sensorName name cannot be null"
        ),
    ]
    private mixed $sensorName = null;

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
    private mixed $pinNumber = null;

    #[
        Assert\Range(
            notInRangeMessage: "readingInterval must be greater than {{ min }}",
            minMessage: "readingInterval must be greater than " . Sensor::MIN_READING_INTERVAL,
            invalidMessage: "readingInterval must be a number",
            min: Sensor::MIN_READING_INTERVAL,
        ),
    ]
    private mixed $readingInterval = null;

    public function getSensorTypeID(): mixed
    {
        return $this->sensorTypeID;
    }

    public function setSensorTypeID(mixed $sensorTypeID): void
    {
        $this->sensorTypeID = $sensorTypeID;
    }

    public function getDeviceID(): mixed
    {
        return $this->deviceID;
    }

    public function setDeviceID(mixed $deviceID): void
    {
        $this->deviceID = $deviceID;
    }

    public function getSensorName(): mixed
    {
        return $this->sensorName;
    }

    public function setSensorName(mixed $sensorName): void
    {
        $this->sensorName = $sensorName;
    }

    public function getPinNumber(): mixed
    {
        return $this->pinNumber;
    }

    public function setPinNumber(mixed $pinNumber): void
    {
        $this->pinNumber = $pinNumber;
    }

    public function getReadingInterval(): mixed
    {
        return $this->readingInterval;
    }

    public function setReadingInterval(mixed $readingInterval): void
    {
        $this->readingInterval = $readingInterval;
    }
}
