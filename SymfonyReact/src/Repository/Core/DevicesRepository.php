<?php


namespace App\Repository\Core;


use App\Entity\Core\Groupname;
use App\Entity\Core\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use function Doctrine\ORM\QueryBuilder;

class DevicesRepository extends EntityRepository
{
    /**
     * @param $groupNameID
     */
    public function returnAllUsersDevices($groupNameID)
    {
        $qb = $this->createQueryBuilder('dv');
        $qb->select('dv.devicename');
        $qb->where(
            $qb->expr()->in('dv.groupnameid', ':groupNameID')
        )
        ->setParameters(['groupNameID' => $groupNameID]);

        $result = $qb->getQuery()->getArrayResult();
//dd($result);
        return $result;
    }
}