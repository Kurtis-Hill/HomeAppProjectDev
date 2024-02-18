<?php

namespace App\Devices\DeviceServices\DeleteDevice;

use App\Devices\Entity\Devices;

interface DeleteDeviceServiceInterface
{
    public function deleteDevice(Devices $devices): bool;
}
