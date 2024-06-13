<?php

namespace App\Repository\Device\ORM;

use App\Entity\Device\Devices;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @method Devices|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devices|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devices[]    findAll()
 * @method Devices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
    public function findAllUsersDevicesByGroupId(array $groupIDs, int $hydration = AbstractQuery::HYDRATE_ARRAY): array;

    #[ArrayShape([Devices::class])]
    public function findAllDevicesByGroupIDs(
        array $groupIDs,
        int $hydration = AbstractQuery::HYDRATE_OBJECT,
    ): array;

    /**
     * @throws ORMException
     */
    public function remove(Devices $device): void;

    #[ArrayShape([1,2,3])]
    public function findAllDevicePinsInUse(int $deviceID): array;
}
