<?php
declare(strict_types=1);

namespace App\Devices\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class DeviceUpdateRequestDTO
{
    #[
        Assert\Type(
            type: ['string', "null"],
            message: "deviceName must be of type {{ type }} you provided {{ value }}"
        )
    ]
    private mixed $deviceName = null;

    #[
        Assert\Type(
            type: ['string', "null"],
            message: "password must be of type {{ type }} you provided {{ value }}"
        )
    ]
    private mixed $password = null;

    #[
        Assert\Type(
            type: ['integer', "null"],
            message: "deviceGroup must be of type {{ type }} you provided {{ value }}"
        )
    ]
    private mixed $deviceGroup = null;

    #[
        Assert\Type(
            type: ['integer', "null"],
            message: "deviceRoom must be of type {{ type }} you provided {{ value }}"
        )
    ]
    private mixed $deviceRoom = null;

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
