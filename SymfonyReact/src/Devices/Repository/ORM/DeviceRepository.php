<?php

namespace App\Devices\Repository\ORM;

use App\Devices\Entity\Devices;
use App\User\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DeviceRepository extends ServiceEntityRepository implements DeviceRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devices::class);
    }

    public function persist(Devices $device): void
    {
        $this->getEntityManager()->persist($device);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findOneById(int $id): ?Devices
    {
        return $this->findOneBy(['deviceNameID' => $id]);
    }

    public function findDuplicateDeviceNewDeviceCheck(string $deviceName, int $roomId): ?Devices
    {
        $qb = $this->createQueryBuilder('devices');
        $expr = $qb->expr();

        $qb->select('devices')
            ->innerJoin(Room::class, 'room')
            ->where(
                $expr->eq('devices.deviceName', ':deviceName'),
                $expr->eq('room.roomID', ':roomID')
            )
            ->setParameters(
                [
                    'deviceName' => $deviceName,
                    'roomID' => $roomId
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }
}
