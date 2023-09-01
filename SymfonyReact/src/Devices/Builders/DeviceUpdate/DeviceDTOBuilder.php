<?php

namespace App\Devices\Builders\DeviceUpdate;

use App\Devices\DTO\Internal\NewDeviceDTO;
use App\Devices\DTO\Internal\UpdateDeviceDTO;
use App\Devices\DTO\Request\DeviceUpdateRequestDTO;
use App\Devices\Entity\Devices;
use App\User\Entity\Group;
use App\User\Entity\Room;
use App\User\Entity\User;

class DeviceDTOBuilder
{
    public static function buildUpdateDeviceInternalDTO(
        DeviceUpdateRequestDTO $deviceUpdateRequestDTO,
        Devices $device,
        ?Room $room,
        ?Group $groupName,
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
        Group $groupNameObject,
        Room $roomObject,
        string $deviceName,
        string $devicePassword,
    ): NewDeviceDTO {
        $device = new Devices();

        return new NewDeviceDTO(
            $user,
            $groupNameObject,
            $roomObject,
            $deviceName,
            $devicePassword,
            $device,
        );
    }
}
