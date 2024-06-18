<?php

namespace App\Services\Device\UpdateDevice;

use App\DTOs\Device\Internal\UpdateDeviceDTO;
use App\Entity\Device\Devices;

interface UpdateDeviceHandlerInterface
{
    public function updateDevice(UpdateDeviceDTO $deviceUpdateRequestDTO): array;

    public function saveDevice(Devices $device): bool;
}
