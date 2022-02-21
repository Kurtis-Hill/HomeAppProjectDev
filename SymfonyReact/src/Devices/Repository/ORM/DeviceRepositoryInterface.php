<?php

namespace App\Devices\Repository\ORM;

use App\Devices\Entity\Devices;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;

interface DeviceRepositoryInterface
{
    public function persist(Devices $device): void;

    /**
     * @throws ORMException
     */
    public function flush(): void;

    /**
     * @throws ORMException
     */
    public function findDuplicateDeviceNewDeviceCheck(string $deviceName, int $roomId): ?Devices;

    /**
     * @throws ORMException | NonUniqueResultException
     */
    public function findOneById(int $id): ?Devices;

    /**
     * @throws ORMException
     */
    public function getAllUsersDevicesByGroupId($groupNameID): array;

    /**
     * @throws ORMException
     */
    public function remove(Devices $device): void;
}
