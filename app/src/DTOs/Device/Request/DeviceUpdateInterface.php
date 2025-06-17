<?php

namespace App\DTOs\Device\Request;

interface DeviceUpdateInterface
{
    public function getDeviceName(): ?string;

    public function getDeviceRoom(): ?int;
}
