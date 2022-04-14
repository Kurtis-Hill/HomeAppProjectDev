<?php

namespace App\Devices\DeviceServices\DeleteDevice;

use App\Devices\Entity\Devices;

interface DeleteDeviceBuilderInterface
{
    public function deleteDevice(Devices $devices): bool;
}
