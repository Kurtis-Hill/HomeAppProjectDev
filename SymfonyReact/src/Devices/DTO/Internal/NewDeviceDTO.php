<?php

namespace App\Devices\DTO\Internal;

use App\Devices\Entity\Devices;
use App\User\Entity\Group;
use App\User\Entity\Room;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Security\Core\User\UserInterface;

#[Immutable]
readonly class NewDeviceDTO
{
    public function __construct(
        private UserInterface $createdBy,
        private Group $groupID,
        private Room $roomId,
        private ?string $deviceName,
        private string $devicePassword,
        private Devices $devices,
    ) {
    }

    public function getDeviceName(): ?string
    {
        return $this->deviceName;
    }

    public function getDevicePassword(): string
    {
        return $this->devicePassword;
    }

    public function getGroupNameObject(): Group
    {
        return $this->groupID;
    }

    public function getRoomObject(): Room
    {
        return $this->roomId;
    }

    public function getCreatedByUserObject(): UserInterface
    {
        return $this->createdBy;
    }

    public function getNewDevice(): Devices
    {
        return $this->devices;
    }
}
