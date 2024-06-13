<?php

namespace App\DTOs\Sensor\Request\SensorUpdateDTO;

use App\Entity\Sensor\Sensor;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateSensorDetailsRequestDTO
{
    #[
        Assert\Type(
            type: ['string', "null"],
            message: "sensor name must be of type {{ type }} you provided {{ value }}"
        )
    ]
    private mixed $sensorName = null;

    #[
        Assert\Type(
            type: ['int', "null"],
            message: "device must be of type {{ type }} you provided {{ value }}"
        )
    ]
    private mixed $deviceID = null;

    #[
        Assert\Range(
            notInRangeMessage: 'pinNumber must be greater than {{ min }}',
            minMessage: 'pinNumber must be greater than {{ value }}',
            invalidMessage: 'pinNumber must be an int you have provided {{ value }}',
            min: 0,
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

    public function setSensorName(mixed $sensorName): void
    {
        $this->sensorName = $sensorName;
    }

    public function setDeviceID(mixed $deviceID): void
    {
        $this->deviceID = $deviceID;
    }

    public function getSensorName(): mixed
    {
        return $this->sensorName;
    }

    public function getDeviceID(): mixed
    {
        return $this->deviceID;
    }

    public function setPinNumber(mixed $pinNumber): void
    {
        $this->pinNumber = $pinNumber;
    }

    public function getPinNumber(): mixed
    {
        return $this->pinNumber;
    }

    public function setReadingInterval(mixed $readingInterval): void
    {
        $this->readingInterval = $readingInterval;
    }

    public function getReadingInterval(): mixed
    {
        return $this->readingInterval;
    }
}
