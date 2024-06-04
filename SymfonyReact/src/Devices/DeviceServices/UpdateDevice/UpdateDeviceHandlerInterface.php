<?php

namespace App\Devices\DeviceServices\UpdateDevice;

use App\Devices\DTO\Internal\UpdateDeviceDTO;
use App\Devices\DTO\Request\DeviceUpdateRequestDTO;
use App\Devices\Entity\Devices;
use App\User\Entity\User;

interface UpdateDeviceHandlerInterface
{
    public function updateDevice(UpdateDeviceDTO $deviceUpdateRequestDTO): array;

    public function saveDevice(Devices $device): bool;
}
