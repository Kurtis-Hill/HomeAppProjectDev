<?php


namespace App\Repository\Core;


use App\Entity\Core\GroupNames;
use App\Entity\Core\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use function Doctrine\ORM\QueryBuilder;

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
            ->leftJoin('App\Entity\Core\Room', 'r', Join::WITH, 'dv.roomID = r.roomID')
            ->leftJoin('App\Entity\Core\GroupNames', 'gn', Join::WITH, 'dv.groupNameID = gn.groupNameID'
            );
        $qb->where(
            $qb->expr()->in('dv.groupNameID', ':groupNameID')
        )
        ->setParameters(['groupNameID' => $groupNameID]);

        return $qb->getQuery()->getArrayResult();
    }


}
