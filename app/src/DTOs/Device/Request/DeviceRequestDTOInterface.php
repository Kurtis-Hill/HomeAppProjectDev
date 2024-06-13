<?php

namespace App\DTOs\Device\Request;

interface DeviceRequestDTOInterface
{
    public function getDeviceName(): mixed;

    public function getDeviceGroup(): mixed;

    public function getDeviceRoom(): mixed;

    public function setDeviceName(mixed $deviceName): void;

    public function setDeviceGroup(mixed $deviceGroup): void;

    public function setDeviceRoom(mixed $deviceRoom): void;
}
