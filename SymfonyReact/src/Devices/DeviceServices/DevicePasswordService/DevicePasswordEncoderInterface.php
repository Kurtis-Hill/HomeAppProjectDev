<?php

namespace App\Devices\DeviceServices\DevicePasswordService;

use App\Devices\Entity\Devices;

interface DevicePasswordEncoderInterface
{
    public function encodeDevicePassword(Devices $newDevice): void;
}
