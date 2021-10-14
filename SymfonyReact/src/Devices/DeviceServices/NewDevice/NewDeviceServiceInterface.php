<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Devices\Entity\Devices;

interface NewDeviceServiceInterface
{
    public function handleNewDeviceSubmission(array $deviceData): ?Devices;
}
