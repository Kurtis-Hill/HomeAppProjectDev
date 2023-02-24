<?php

namespace App\Devices\Repository\ORM;

use App\Devices\DeviceServices\GetDevices\GetDevicesForUserInterface;
use App\Devices\Entity\Devices;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Devices>
 *
 * @method Devices|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devices|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devices[]    findAll()
 * @method Devices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
        return $this->find($id);
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

    public function findAllUsersDevicesByGroupId(array $groupNameIDs, int $hydration = AbstractQuery::HYDRATE_ARRAY): array
    {
        $qb = $this->createQueryBuilder('dv');
        $qb->select('dv')
            ->leftJoin(Room::class, 'r', Join::WITH, 'dv.roomID = r.roomID')
            ->leftJoin(GroupNames::class, 'gn', Join::WITH, 'dv.groupNameID = gn.groupNameID');
        $qb->where(
            $qb->expr()->in('dv.groupNameID', ':groupNameID')
        )
            ->orderBy('dv.deviceName', 'ASC')
            ->setParameters(['groupNameID' => $groupNameIDs]);

        return $qb->getQuery()->getResult($hydration);
    }

    /**
     * @throws ORMException
     */
    public function findAllDevicesByGroupNamePaginated(
        array $groupNameIDs,
        int $limit = GetDevicesForUserInterface::MAX_DEVICE_RETURN_SIZE,
        int $offset = 0,
        int $hydration = AbstractQuery::HYDRATE_OBJECT,
    ): array {
        $qb = $this->createQueryBuilder('dv');
        $qb->select('dv')
            ->innerJoin(GroupNames::class, 'gn', Join::WITH, 'dv.groupNameID = gn.groupNameID')
            ->innerJoin(Room::class, 'r', Join::WITH, 'dv.roomID = r.roomID')
            ->innerJoin(User::class, 'u', Join::WITH, 'dv.createdBy = u.userID');
        $qb->where(
            $qb->expr()->in('dv.groupNameID', ':groupNameID')
        )
            ->orderBy('dv.deviceName', 'ASC')
            ->setParameters(['groupNameID' => $groupNameIDs])
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult($hydration);
    }

    public function remove(Devices $device): void
    {
        $this->getEntityManager()->remove($device);
    }
}
