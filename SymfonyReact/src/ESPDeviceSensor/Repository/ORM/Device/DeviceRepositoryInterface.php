<?php

namespace App\ESPDeviceSensor\Repository\ORM\Device;


use App\Devices\Entity\Devices;

interface DeviceRepositoryInterface
{
    public function persist(Devices $device): void;

    public function flush(): void;

    public function findDuplicateDeviceNewDeviceCheck(array $deviceDetails): ?Devices;

    public function findOneById(int $id): ?Devices;
}
