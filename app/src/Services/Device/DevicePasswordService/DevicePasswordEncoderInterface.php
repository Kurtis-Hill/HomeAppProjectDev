<?php

namespace App\Services\Device\DevicePasswordService;

use App\Entity\Device\Devices;

interface DevicePasswordEncoderInterface
{
    public function encodeDevicePassword(Devices $newDevice): void;
}
