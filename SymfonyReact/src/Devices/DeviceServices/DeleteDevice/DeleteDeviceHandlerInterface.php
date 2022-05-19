<?php

namespace App\Devices\DeviceServices\DeleteDevice;

use App\Devices\Entity\Devices;

interface DeleteDeviceHandlerInterface
{
    public function deleteDevice(Devices $devices): bool;
}
