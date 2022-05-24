<?php

namespace App\Devices\Builders\DeviceUpdate;

use App\Devices\DTO\Response\DeviceResponseDTO;
use App\Devices\Entity\Devices;

class DeviceUpdateResponseDTOBuilder
{
    public static function buildUpdateDeviceDTO(Devices $device, bool $showPassword = false): DeviceResponseDTO
    {
        return new DeviceResponseDTO(
            $device->getDeviceNameID(),
            $device->getDeviceName(),
            $device->getGroupNameObject()->getGroupNameID(),
            $device->getRoomObject()->getRoomID(),
            $device->getCreatedBy()->getUsername(),
            $showPassword === false ? null : $device->getDeviceSecret(),
            $device->getIpAddress(),
            $device->getExternalIpAddress(),
            $device->getRoles(),
        );
    }
}
