<?php

namespace App\Devices\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class DeviceUpdateRequestDTO implements DeviceRequestDTOInterface
{
    #[Assert\Type(type: ['string', "null"])]
    private mixed $deviceName;

    #[Assert\Type(type: ['string', "null"])]
    private mixed $password = null;

    #[Assert\Type(type: ['integer', "null"])]
    private mixed $deviceGroup = null;

    #[Assert\Type(type: ['integer', "null"])]
    private mixed $deviceRoom = null;

    public function getDeviceName(): mixed
    {
        return $this->deviceName = null;
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
