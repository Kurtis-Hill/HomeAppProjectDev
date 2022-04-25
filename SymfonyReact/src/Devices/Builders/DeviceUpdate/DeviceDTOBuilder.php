<?php

namespace App\Devices\Builders\DeviceUpdate;

use App\Devices\DTO\Response\DeviceDTO;
use App\Devices\Entity\Devices;

class DeviceDTOBuilder
{
    public static function buildDeviceDTO(Devices $device, bool $showPassword = false): DeviceDTO
    {
        return new DeviceDTO(
            $device->getDeviceNameID(),
            $device->getDeviceName(),
            $device->getGroupNameObject()->getGroupNameID(),
            $device->getRoomObject()->getRoomID(),
            $device->getCreatedBy()->getUsername(),
            $showPassword === false ? null : $device->getPassword(),
            $device->getIpAddress(),
            $device->getExternalIpAddress(),
        );
    }
}
