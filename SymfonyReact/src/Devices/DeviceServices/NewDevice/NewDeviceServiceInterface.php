<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Devices\DTO\DeviceDTO;
use App\Devices\Entity\Devices;

interface NewDeviceServiceInterface
{
    public function handleNewDeviceSubmission(DeviceDTO $deviceData): ?Devices;
}
