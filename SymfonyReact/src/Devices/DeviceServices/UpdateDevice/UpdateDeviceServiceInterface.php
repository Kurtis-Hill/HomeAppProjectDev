<?php

namespace App\Devices\DeviceServices\UpdateDevice;

use App\Devices\DTO\Internal\UpdateDeviceDTO;
use App\Devices\DTO\Request\DeviceRequestDTOInterface;
use App\Devices\DTO\Response\DeviceUpdateResponseDTO;
use App\Devices\Entity\Devices;

interface UpdateDeviceServiceInterface
{
    public function updateDevice(UpdateDeviceDTO $deviceUpdateRequestDTO): array;

    public function saveDevice(Devices $device): bool;
}
