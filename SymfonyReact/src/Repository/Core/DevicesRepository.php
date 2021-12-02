<?php


namespace App\Repository\Core;


use App\Devices\Entity\Devices;
use App\Entity\Core\GroupNames;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;
use App\User\Entity;

class DevicesRepository extends EntityRepository
{
    /**
     * @param $groupNameID
     * @return array
     */
    public function getAllUsersDevicesByGroupId($groupNameID): array
    {
        $qb = $this->createQueryBuilder('dv');
        $qb->select('dv.deviceNameID', 'dv.deviceName', 'gn.groupNameID', 'r.roomID')
            ->leftJoin(Room::class, 'r', Join::WITH, 'dv.roomID = r.roomID')
            ->leftJoin(GroupNames::class, 'gn', Join::WITH, 'dv.groupNameID = gn.groupNameID'
            );
        $qb->where(
            $qb->expr()->in('dv.groupNameID', ':groupNameID')
        )
        ->setParameters(['groupNameID' => $groupNameID]);

        return $qb->getQuery()->getArrayResult();
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

    /**
     * @param $deviceDetails
     * @return mixed|null
     */
    public function findDeviceByIdAndGroupNameIds($deviceDetails): ?Devices
    {
        $qb = $this->createQueryBuilder('devices');
        $expr = $qb->expr();

        $qb->select('devices')
            ->innerJoin(Room::class, 'room')
            ->where(
                $expr->eq('devices.deviceNameID', ':deviceNameID'),
                $expr->in('devices.groupNameID', ':groupNameID'),
            )
            ->setParameters(
                [
                    'deviceNameID' => $deviceDetails['deviceNameID'],
                    'groupNameID' => $deviceDetails['groupNameIDs'],
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }
}
