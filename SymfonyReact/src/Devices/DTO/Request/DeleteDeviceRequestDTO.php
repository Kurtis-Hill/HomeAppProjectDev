<?php

namespace App\Devices\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteDeviceRequestDTO implements DeviceRequestDTOInterface
{
    private mixed $deviceNameID;

    #[
        Assert\NotNull(
            message: 'Device ID needs to be included in the request'
        ),
        Assert\Type(
            type: 'integer',
            message: 'Device ID must be an integer'
        )
    ]
    public function getDeviceNameID(): mixed
    {
        return $this->deviceNameID;
    }

    public function setDeviceNameID(mixed $deviceNameID): void
    {
        $this->deviceNameID = $deviceNameID;
    }
}
