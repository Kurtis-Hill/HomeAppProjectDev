<?php

namespace App\Devices\Repository\ORM;

use App\Devices\Entity\Devices;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;

interface DeviceRepositoryInterface
{
    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(Devices $device): void;

    /**
     * @throws OptimisticLockException
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
    public function getAllUsersDevicesByGroupId(array $groupNameIDs, int $hydration = AbstractQuery::HYDRATE_ARRAY): array;

    /**
     * @throws ORMException
     */
    public function remove(Devices $device): void;
}
