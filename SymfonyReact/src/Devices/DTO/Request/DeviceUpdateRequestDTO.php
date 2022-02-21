<?php

namespace App\Devices\DTO\Request;
use Symfony\Component\Validator\Constraints as Assert;

class DeviceUpdateRequestDTO implements DeviceRequestDTOInterface
{
    #[
        Assert\NotNull(
            message: 'Device ID needs to be included in the request'
        ),
        Assert\Type(
            type: 'integer',
            message: 'Device ID must be an integer'
        )
    ]
    private mixed $deviceNameID;

    #[Assert\Type(type: ['string', "null"])]
    private mixed $deviceName;

    #[Assert\Type(type: ['string', "null"])]
    private mixed $password;

    #[Assert\Type(type: ['integer', "null"])]
    private mixed $deviceGroup;

    #[Assert\Type(type: ['integer', "null"])]
    private mixed $deviceRoom;

    public function getDeviceNameID(): mixed
    {
        return $this->deviceNameID;
    }

    public function setDeviceNameID($deviceNameID): void
    {
        $this->deviceNameID = $deviceNameID;
    }

    public function getDeviceName(): mixed
    {
        return $this->deviceName;
    }

    public function setDeviceName($deviceName): void
    {
        $this->deviceName = $deviceName;
    }

    public function getPassword(): mixed
    {
        return $this->password;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function getDeviceGroup(): mixed
    {
        return $this->deviceGroup;
    }

    public function setDeviceGroup(mixed $deviceGroup): void
    {
        $this->deviceGroup = $deviceGroup;
    }

    public function getDeviceRoom(): mixed
    {
        return $this->deviceRoom;
    }

    public function setDeviceRoom(mixed $deviceRoom): void
    {
        $this->deviceRoom = $deviceRoom;
    }
}
