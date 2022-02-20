<?php

namespace App\Devices\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class NewDeviceRequestDTO
{
    #[
        Assert\NotNull(
            message: "Device name cannot be null"
        ),
        Assert\NotBlank(
            message: 'Device name is a required field'
        ),
        Assert\Type(
            type: 'string',
            message: 'Device name value is {{ value }} is not a valid {{ type }}'
        ),
    ]
    private $deviceName;

    #[
        Assert\NotNull(
            message: "Device group cannot be null"
        ),
        Assert\NotBlank(
            message: 'Device group is a required field'
        ),
        Assert\Type(
            type: 'integer',
            message: 'Device group value is {{ value }} is not a valid {{ type }}'
        ),
    ]
    private $deviceGroup;

    #[
        Assert\NotNull(
            message: "Device room cannot be null"
        ),
        Assert\NotBlank(
            message: 'Device room is a required field'
        ),
        Assert\Type(
            type: 'integer',
            message: 'Device room value is {{ value }} is not a valid {{ type }}'
        ),
    ]
    private $deviceRoom;

    public function getDeviceName()
    {
        return $this->deviceName;
    }

    public function getDeviceGroup()
    {
        return $this->deviceGroup;
    }

    public function getDeviceRoom()
    {
        return $this->deviceRoom;
    }

    public function setDeviceName(mixed $deviceName): void
    {
        $this->deviceName = $deviceName;
    }

    public function setDeviceGroup(mixed $deviceGroup): void
    {
        $this->deviceGroup = $deviceGroup;
    }

    public function setDeviceRoom(mixed $deviceRoom): void
    {
        $this->deviceRoom = $deviceRoom;
    }
}
