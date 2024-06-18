<?php
declare(strict_types=1);

namespace App\DTOs\Device\Internal;

use App\DTOs\Device\Request\DeviceUpdateRequestDTO;
use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\Room;
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
