<?php


namespace App\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use function Doctrine\ORM\QueryBuilder;

class GroupNameMappingTableRepository extends EntityRepository
{
    public function getGroupsForUser($userID)
    {
       //dd($userID);
        $qb = $this->createQueryBuilder('gmt');

        $qb->select('gn.groupnameid')
            ->innerJoin('App\Entity\Core\GroupName', 'gn', Join::WITH, 'gmt.groupnameid = gn.groupnameid')
            ->innerJoin('App\Entity\Core\User', 'u', Join::WITH, 'gmt.userID = u.userid')
            ->where(
                $qb->expr()->eq('gmt.userID', ':userID')
            )
            ->setParameter('userID', $userID);

        $result = $qb->getQuery()->getScalarResult();
        $groupNameIDs = array_map('current', $result);
       //dd($groupNameIDs);
        return $groupNameIDs;
    }
}