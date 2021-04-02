<?php


namespace App\Repository\Core;


use App\Entity\Core\GroupNames;
use App\Entity\Core\User;
use App\Entity\Devices\Devices;
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
    public function findDeviceInUsersGroup($deviceDetails): ?Devices
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
//dd($qb->getQuery()->getSingleResult());
        return $qb->getQuery()->getResult()[0] ?? null;
    }

    public function findUsersDeviceAPIRequest(string $deviceName, string $deviceSecret): ?Devices
    {
        //dd($deviceName, $deviceSecret);
        $qb = $this->createQueryBuilder('devices');
        $expr = $qb->expr();

        $qb->select('devices')
            ->where(
                $expr->eq('devices.secret', ':deviceSecret'),
                $expr->eq('devices.deviceName', ':deviceName'),
            )
            ->setParameters([
                    'deviceSecret' => $deviceSecret,
                    'deviceName' => $deviceName
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findUsersDeviceAPIRequestCheckUser(string $deviceName, string $deviceSecret): ?Devices
    {
        //dd($deviceName, $deviceSecret);
        $qb = $this->createQueryBuilder('devices');
        $expr = $qb->expr();

        $qb->select('devices')
            ->where(
                $expr->eq('devices.secret', ':deviceSecret'),
                $expr->eq('devices.deviceName', ':deviceName'),
                $expr->in('devices.groupNameID', ':groupNameIds')
            )
            ->setParameters([
                    'deviceSecret' => $deviceSecret,
                    'deviceName' => $deviceName,
                ]
            );

        //dd($groupNameIds, $qb->getQuery()->getResult());

        return $qb->getQuery()->getOneOrNullResult();
    }

}
