<?php

namespace App\ESPDeviceSensor\Repository\ORM\Device;

use App\Entity\Core\Room;
use App\Entity\Devices\Devices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DeviceRepository extends ServiceEntityRepository implements DeviceRepositoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
        parent::__construct($registry, Devices::class);
    }

    public function persist(Devices $device): void
    {
        $this->registry->getManager()->persist($device);
    }

    public function flush(): void
    {
        $this->registry->getManager()->flush();
    }

    public function findOneById(int $id): ?Devices
    {
        return $this->findOneBy(['deviceNameID' => $id]);
    }

    /**
     * @param array $deviceDetails
     * @return Devices|null
     */
    public function findDuplicateDeviceNewDeviceCheck(array $deviceDetails): ?Devices
    {
        $qb = $this->createQueryBuilder('devices');
        $expr = $qb->expr();

        $qb->select('devices')
            ->innerJoin(Room::class, 'room')
            ->where(
                $expr->eq('devices.deviceName', ':deviceName'),
                $expr->eq('devices.groupNameID', ':groupNameID'),
                $expr->eq('room.roomID', ':roomID')
            )
            ->setParameters(
                [
                    'deviceName' => $deviceDetails['deviceName'],
                    'groupNameID' => $deviceDetails['groupNameObject'],
                    'roomID' => $deviceDetails['roomObject']
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }

}
