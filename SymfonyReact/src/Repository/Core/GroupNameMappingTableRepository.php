<?php


namespace App\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use function Doctrine\ORM\QueryBuilder;
use mysql_xdevapi\Exception;

class GroupNameMappingTableRepository extends EntityRepository
{
    public function getGroupsForUser($userID): array
    {
        $qb = $this->createQueryBuilder('gmt');

        $qb->select('gn.groupNameID, gn.groupName')
            ->innerJoin('App\Entity\Core\GroupNames', 'gn', Join::WITH, 'gmt.groupNameID = gn.groupNameID')
            ->innerJoin('App\Entity\Core\User', 'u', Join::WITH, 'gmt.userID = u.userID')
            ->where(
                $qb->expr()->eq('gmt.userID', ':userID')
            )
            ->setParameter('userID', $userID);

        return $qb->getQuery()->getScalarResult();
    }

}
