<?php

namespace App\Devices\Builders\DeviceUpdate;

use App\Devices\DTO\Response\DeviceFullDetailsResponseDTO;
use App\Devices\DTO\Response\DeviceResponseDTO;
use App\Devices\Entity\Devices;
use App\User\Builders\GroupName\GroupNameResponseDTOBuilder;
use App\User\Builders\RoomDTOBuilder\RoomResponseDTOBuilder;

class DeviceUpdateResponseDTOBuilder
{
    public static function buildDeviceIDResponseDTO(Devices $device, bool $showPassword = false): DeviceResponseDTO
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

    public static function buildDeletedDeviceResponseDTO(Devices $device, bool $showPassword = false): DeviceResponseDTO
    {
        return new DeviceResponseDTO(
            null,
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

    public static function buildDeviceFullDetailsResponseDTO(
        Devices $device,
        bool $showPassword = false
    ): DeviceFullDetailsResponseDTO {
        return new DeviceFullDetailsResponseDTO(
            $device->getDeviceNameID(),
            $device->getDeviceName(),
            $showPassword === false ? null : $device->getDeviceSecret(),
            GroupNameResponseDTOBuilder::buildGroupNameResponseDTO($device->getGroupNameObject()),
            RoomResponseDTOBuilder::buildRoomResponseDTO($device->getRoomObject()),
            $device->getIpAddress(),
            $device->getExternalIpAddress(),
            $device->getRoles(),
        );
    }
}
