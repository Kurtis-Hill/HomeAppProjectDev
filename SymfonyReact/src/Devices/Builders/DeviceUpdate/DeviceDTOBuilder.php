<?php

namespace App\Devices\Builders\DeviceUpdate;

use App\Devices\DTO\Internal\NewDeviceDTO;
use App\Devices\DTO\Internal\UpdateDeviceDTO;
use App\Devices\DTO\Request\DeviceUpdateRequestDTO;
use App\Devices\Entity\Devices;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Entity\User;

class DeviceDTOBuilder
{
    public static function buildUpdateDeviceInternalDTO(
        DeviceUpdateRequestDTO $deviceUpdateRequestDTO,
        Devices $device,
        ?Room $room,
        ?GroupNames $groupName,
    ): UpdateDeviceDTO {
        return new UpdateDeviceDTO(
            $deviceUpdateRequestDTO,
            $device,
            $room ?? null,
            $groupName ?? null
        );
    }

    public static function buildNewDeviceDTO(
        User $user,
        GroupNames $groupNameObject,
        Room $roomObject,
        string $deviceName,
    ): NewDeviceDTO {
        return new NewDeviceDTO(
            $user,
            $groupNameObject,
            $roomObject,
            $deviceName,
        );
    }
}
