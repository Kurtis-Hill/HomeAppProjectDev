<?php
declare(strict_types=1);

namespace App\DTOs\Device\Internal;

use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\Room;
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
        private ?string $deviceIP,
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

    public function getDeviceIP(): ?string
    {
        return $this->deviceIP;
    }
}
