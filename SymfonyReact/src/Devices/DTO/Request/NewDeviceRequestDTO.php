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
            message: 'Device name value is {{ value }} and not a valid {{ type }}'
        ),
    ]
    private mixed $deviceName = null;

    #[
        Assert\NotNull(
            message: "Device password cannot be null"
        ),
        Assert\NotBlank(
            message: 'Device password is a required field'
        ),
        Assert\Type(
            type: 'string',
            message: 'Device password value is {{ value }} and not a valid {{ type }}'
        ),
    ]
    private mixed $devicePassword = null;

    #[
        Assert\NotNull(
            message: "Device group cannot be null"
        ),
        Assert\NotBlank(
            message: 'Device group is a required field'
        ),
        Assert\Type(
            type: ['integer'],
            message: 'Device group value is {{ value }} and not a valid {{ type }}'
        ),
    ]
    private mixed $deviceGroup = null;

    #[
        Assert\NotNull(
            message: "Device room cannot be null"
        ),
        Assert\NotBlank(
            message: 'Device room is a required field'
        ),
        Assert\Type(
            type: ['integer'],
            message: 'Device room value is {{ value }} and not a valid {{ type }}'
        ),
    ]
    private mixed $deviceRoom = null;

    public function getDeviceName(): mixed
    {
        return $this->deviceName;
    }

    public function getDevicePassword(): mixed
    {
        return $this->devicePassword;
    }

    public function getDeviceGroup(): mixed
    {
        return $this->deviceGroup;
    }

    public function getDeviceRoom(): mixed
    {
        return $this->deviceRoom;
    }

    public function setDeviceName(mixed $deviceName): void
    {
        $this->deviceName = $deviceName;
    }

    public function setDevicePassword(mixed $devicePassword): void
    {
        $this->devicePassword = $devicePassword;
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
