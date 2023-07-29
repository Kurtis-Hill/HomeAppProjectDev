<?php

namespace App\Devices\Builders\DeviceUpdate;

use App\Devices\DTO\Response\DeviceUpdateResponseDTO;
use App\Devices\Entity\Devices;

class DeviceUpdateDTOBuilder
{
    public static function buildSensorSuccessResponseDTO(Devices $updatedDevice): DeviceUpdateResponseDTO
    {
        return new DeviceUpdateResponseDTO(
            $updatedDevice->getDeviceName(),
            $updatedDevice->getRoomObject()->getRoom(),
            $updatedDevice->getRoomObject()->getRoomID(),
            $updatedDevice->getGroupNameObject()->getGroupName(),
            $updatedDevice->getGroupNameObject()->getGroupNameID(),
        );
    }
}
