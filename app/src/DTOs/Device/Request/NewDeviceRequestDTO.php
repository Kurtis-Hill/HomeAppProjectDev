<?php
declare(strict_types=1);

namespace App\DTOs\Device\Request;

use App\Entity\Device\Devices;
use App\CustomValidators\NoSpecialCharactersNameConstraint;
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
        Assert\Length(
            min: Devices::DEVICE_NAME_MIN_LENGTH,
            max: Devices::DEVICE_NAME_MAX_LENGTH,
            minMessage: 'Device name must be at least {{ limit }} characters long',
            maxMessage: 'Device name cannot be longer than {{ limit }} characters'
        ),
        NoSpecialCharactersNameConstraint,

    ]
    private string $deviceName;

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
        Assert\Length(
            min: 5,
            max: 100,
            minMessage: "Device password must be at least {{ limit }} characters long",
            maxMessage: "Device password cannot be longer than {{ limit }} characters"
        )
    ]
    private string $devicePassword;

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
    private int $deviceGroup;

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
    private int $deviceRoom;

    #[
        Assert\Type(
            type: ['string', 'null'],
            message: 'Device IP value is {{ value }} and not a valid {{ type }}'
        ),
    ]
    private mixed $deviceIPAddress = null;

    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    public function getDevicePassword(): string
    {
        return $this->devicePassword;
    }

    public function getDeviceGroup(): int
    {
        return $this->deviceGroup;
    }

    public function getDeviceRoom(): int
    {
        return $this->deviceRoom;
    }

    public function setDeviceName(string $deviceName): void
    {
        $this->deviceName = $deviceName;
    }

    public function setDevicePassword(string $devicePassword): void
    {
        $this->devicePassword = $devicePassword;
    }

    public function setDeviceGroup(int $deviceGroup): void
    {
        $this->deviceGroup = $deviceGroup;
    }

    public function setDeviceRoom(int $deviceRoom): void
    {
        $this->deviceRoom = $deviceRoom;
    }

    public function getDeviceIPAddress(): ?string
    {
        return $this->deviceIPAddress;
    }

    public function setDeviceIPAddress(?string $deviceIP): void
    {
        $this->deviceIPAddress = $deviceIP;
    }
}
