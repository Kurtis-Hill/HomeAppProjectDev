<?php

namespace App\Devices\DTO\Internal;

use App\Devices\DTO\Request\DeviceUpdateRequestDTO;
use App\Devices\Entity\Devices;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UpdateDeviceDTO
{
    private DeviceUpdateRequestDTO $deviceUpdateRequestDTO;

    private Devices $devices;

    private ?Room $proposedRoomToUpdateTo;

    private ?GroupNames $proposedGroupNameToUpdateTo;

    public function __construct(
        DeviceUpdateRequestDTO $updateDeviceDTO,
        Devices $devices,
        ?Room $room,
        ?GroupNames $groupName
    ) {
        $this->deviceUpdateRequestDTO = $updateDeviceDTO;
        $this->devices = $devices;
        $this->proposedRoomToUpdateTo = $room;
        $this->proposedGroupNameToUpdateTo = $groupName;
    }

    public function getDeviceUpdateRequestDTO(): DeviceUpdateRequestDTO
    {
        return $this->deviceUpdateRequestDTO;
    }

    public function getDeviceToUpdate(): Devices
    {
        return $this->devices;
    }

    public function getProposedUpdatedRoom(): ?Room
    {
        return $this->proposedRoomToUpdateTo;
    }

    public function getProposedGroupNameToUpdateTo(): ?GroupNames
    {
        return $this->proposedGroupNameToUpdateTo;
    }
}
