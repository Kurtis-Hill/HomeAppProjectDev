<?php

namespace App\Devices\Repository\ORM;

use App\Devices\Entity\Devices;
use Doctrine\ORM\ORMException;

interface DeviceRepositoryInterface
{
    public function persist(Devices $device): void;

    /**
     * @throws ORMException
     */
    public function flush(): void;

    public function findDuplicateDeviceNewDeviceCheck(string $deviceName, int $roomId): ?Devices;

    public function findOneById(int $id): ?Devices;
}
