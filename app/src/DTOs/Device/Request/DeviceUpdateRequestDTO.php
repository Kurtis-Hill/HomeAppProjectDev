<?php
declare(strict_types=1);

namespace App\DTOs\Device\Request;

use App\Entity\Device\Devices;
use Symfony\Component\Validator\Constraints as Assert;

class DeviceUpdateRequestDTO
{
    #[
        Assert\Type(
            type: ['string', "null"],
            message: "deviceName must be of type {{ type }} you provided {{ value }}"
        ),
        Assert\Length(
            min: Devices::DEVICE_NAME_MIN_LENGTH,
            max: Devices::DEVICE_NAME_MAX_LENGTH,
            minMessage: "Device name must be at least {{ limit }} characters long",
            maxMessage: "Device name cannot be longer than {{ limit }} characters"
        )
    ]
    private ?string $deviceName = null;

    #[
        Assert\Type(
            type: ['string', "null"],
            message: "password must be of type {{ type }} you provided {{ value }}"
        ),
        Assert\Length(
            min: 5,
            max: 100,
            minMessage: "Device password must be at least {{ limit }} characters long",
            maxMessage: "Device password cannot be longer than {{ limit }} characters"
        )
    ]
    private ?string $password = null;

    #[
        Assert\Type(
            type: ['integer', "null"],
            message: "deviceGroup must be of type {{ type }} you provided {{ value }}"
        )
    ]
    private ?int $deviceGroup = null;

    #[
        Assert\Type(
            type: ['integer', "null"],
            message: "deviceRoom must be of type {{ type }} you provided {{ value }}"
        )
    ]
    private ?int $deviceRoom = null;

    public function getDeviceName(): ?string
    {
        return $this->deviceName;
    }

    public function setDeviceName(?string $deviceName): void
    {
        $this->deviceName = $deviceName;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getDeviceGroup(): ?int
    {
        return $this->deviceGroup;
    }

    public function setDeviceGroup(?int $deviceGroup): void
    {
        $this->deviceGroup = $deviceGroup;
    }

    public function getDeviceRoom(): ?int
    {
        return $this->deviceRoom;
    }

    public function setDeviceRoom(?int $deviceRoom): void
    {
        $this->deviceRoom = $deviceRoom;
    }
}
