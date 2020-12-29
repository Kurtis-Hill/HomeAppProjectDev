<?php


namespace App\Repository\Core;


use App\Entity\Core\GroupNames;
use App\Entity\Core\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use function Doctrine\ORM\QueryBuilder;
use App\Entity\Core\Room;

class DevicesRepository extends EntityRepository
{
    /**
     * @param $groupNameID
     * @return array
     */
    public function getAllUsersDevices($groupNameID): array
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
     * @param $deviceDetails
     * @return int|mixed|string
     */
    public function findDeviceInUsersGroup($deviceDetails)
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

        return $qb->getQuery()->getResult();
    }


}
