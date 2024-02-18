<?php

namespace App\Sensors\DTO\Request;

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
}
