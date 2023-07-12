<?php

namespace App\Devices\Builders\DeviceRequestsArgumentBuilders;

interface DeviceRequestArgumentBuilderInterface
{
    public function buildDeviceRequestArguments(array $deviceRequestArguments): array;
}
