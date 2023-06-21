<?php

namespace App\Devices\DTO\Internal;

use App\Devices\DTO\Request\DeviceUpdateRequestDTO;
use App\Devices\Entity\Devices;
use App\User\Entity\Group;
use App\User\Entity\Room;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class UpdateDeviceDTO
{
    public function __construct(
        private DeviceUpdateRequestDTO $updateDeviceDTO,
        private Devices $devices,
        private ?Room $room,
        private ?Group $groupName
    ) {
    }

    public function getDeviceUpdateRequestDTO(): DeviceUpdateRequestDTO
    {
        return $this->updateDeviceDTO;
    }

    public function getDeviceToUpdate(): Devices
    {
        return $this->devices;
    }

    public function getProposedUpdatedRoom(): ?Room
    {
        return $this->room;
    }

    public function getProposedGroupNameToUpdateTo(): ?Group
    {
        return $this->groupName;
    }
}
