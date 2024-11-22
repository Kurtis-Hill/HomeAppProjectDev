<?php
declare(strict_types=1);

namespace App\Repository\Device\ORM;

use App\Entity\Device\Devices;
use App\Entity\Sensor\Sensor;
use App\Entity\User\Group;
use App\Entity\User\Room;
use App\Entity\User\User;
use App\Services\Device\GetDevices\DevicesForUserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

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

    public function findAllUsersDevicesByGroupId(array $groupIDs, int $hydration = AbstractQuery::HYDRATE_ARRAY): array
    {
        $qb = $this->createQueryBuilder('dv');
        $qb->select('dv')
            ->leftJoin(Room::class, 'r', Join::WITH, 'dv.roomID = r.roomID')
            ->leftJoin(Group::class, 'gn', Join::WITH, 'dv.groupID = gn.groupID');
        $qb->where(
            $qb->expr()->in('dv.groupID', ':groupID')
        )
            ->orderBy('dv.deviceName', 'ASC')
            ->setParameters(['groupID' => $groupIDs]);

        return $qb->getQuery()->getResult($hydration);
    }

    /**
     * @throws ORMException
     */
    public function findAllDevicesByGroupNamePaginated(
        array $groupIDs,
        int $limit = DevicesForUserInterface::MAX_DEVICE_RETURN_SIZE,
        int $offset = 0,
        int $hydration = AbstractQuery::HYDRATE_OBJECT,
    ): array {
        $qb = $this->createQueryBuilder('dv');
        $qb->select('dv')
            ->innerJoin(Group::class, 'gn', Join::WITH, 'dv.groupID = gn.groupID')
            ->innerJoin(Room::class, 'r', Join::WITH, 'dv.roomID = r.roomID')
            ->innerJoin(User::class, 'u', Join::WITH, 'dv.createdBy = u.userID');
        $qb->where(
            $qb->expr()->in('dv.groupID', ':groupID')
        )
            ->orderBy('dv.deviceName', 'ASC')
            ->setParameters(['groupID' => $groupIDs])
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult($hydration);
    }

    #[ArrayShape([Devices::class])]
    public function findAllDevicesByGroupIDs(
        array $groupIDs,
        int $hydration = AbstractQuery::HYDRATE_OBJECT,
    ): array {
        $qb = $this->createQueryBuilder('dv');
        $expr = $qb->expr();

        $qb->select('dv');
        $qb->where(
            $expr->in('dv.groupID', ':groupID')
        )
            ->orderBy('dv.deviceName', 'ASC')
            ->setParameters(['groupID' => $groupIDs]);

        return $qb->getQuery()->getResult($hydration);
    }

    #[ArrayShape([1,2,3])]
    public function findAllDevicePinsInUse(int $deviceID): array
    {
        $qb = $this->createQueryBuilder('dv');
        $expr = $qb->expr();

        $qb->select('s.pinNumber')
            ->innerJoin(Sensor::class, 's', Join::WITH, 'dv.deviceID = s.deviceID')
            ->where(
                $expr->eq('dv.deviceID', ':deviceID')
            )
            ->setParameters(['deviceID' => $deviceID]);

        return $qb->getQuery()->getSingleColumnResult();
    }

    public function remove(Devices $device): void
    {
        $this->getEntityManager()->remove($device);
    }
}
