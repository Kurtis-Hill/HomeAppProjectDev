<?php

namespace App\Devices\Repository\ORM;

use App\Devices\Entity\Devices;

interface DeviceRepositoryInterface
{
    public function persist(Devices $device): void;

    public function flush(): void;

    public function findDuplicateDeviceNewDeviceCheck(string $deviceName, int $roomId): ?Devices;

    public function findOneById(int $id): ?Devices;
}
