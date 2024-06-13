<?php

namespace App\Services\Device\DeleteDevice;

use App\Entity\Device\Devices;

interface DeleteDeviceServiceInterface
{
    public function deleteDevice(Devices $devices): bool;
}
