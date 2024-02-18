<?php

namespace App\Sensors\DTO\Request\SensorUpdateDTO;

use Symfony\Component\Validator\Constraints as Assert;

class SensorUpdateRequestDTO
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
}
