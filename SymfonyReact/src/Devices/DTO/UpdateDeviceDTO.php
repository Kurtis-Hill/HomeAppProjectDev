<?php

namespace App\Devices\DTO;

use App\Devices\DTO\Request\DeviceUpdateRequestDTO;
use App\Devices\Entity\Devices;
use App\User\Entity\Room;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UpdateDeviceDTO
{
    private DeviceUpdateRequestDTO $deviceUpdateRequestDTO;

    private Devices $devices;

    private ?Room $proposedUpdatedRoom;

    public function __construct(
        DeviceUpdateRequestDTO $updateDeviceDTO,
        Devices $devices,
        ?Room $room,
    ) {
        $this->deviceUpdateRequestDTO = $updateDeviceDTO;
        $this->devices = $devices;
        $this->proposedUpdatedRoom = $room;
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
        return $this->proposedUpdatedRoom;
    }
}
