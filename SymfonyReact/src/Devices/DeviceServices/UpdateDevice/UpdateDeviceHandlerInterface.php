<?php

namespace App\Devices\DeviceServices\UpdateDevice;

use App\Devices\DTO\Internal\UpdateDeviceDTO;
use App\Devices\Entity\Devices;

interface UpdateDeviceHandlerInterface
{
    public function updateDevice(UpdateDeviceDTO $deviceUpdateRequestDTO): array;

    public function saveDevice(Devices $device): bool;
}
