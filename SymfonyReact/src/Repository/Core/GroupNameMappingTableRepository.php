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

        $qb->select('gn.groupNameID')
            ->innerJoin('App\Entity\Core\GroupNames', 'gn', Join::WITH, 'gmt.groupNameID = gn.groupNameID')
            ->innerJoin('App\Entity\Core\User', 'u', Join::WITH, 'gmt.userID = u.userID')
            ->where(
                $qb->expr()->eq('gmt.userID', ':userID')
            )
            ->setParameter('userID', $userID);

        $result = $qb->getQuery()->getScalarResult();

        if (!$result) throw new \Exception('Get User Groups Has Failed');

        return array_map('current', $result);
    }

    public function getUserGroupNamesAndIDs($userID): array
    {
        $qb = $this->createQueryBuilder('gmt');
        $qb->select('gn.groupName', 'gn.groupNameID')
            ->innerJoin('App\Entity\Core\GroupNames', 'gn', Join::WITH, 'gmt.groupNameID = gn.groupNameID')
            ->innerJoin('App\Entity\Core\User', 'u', Join::WITH, 'gmt.userID = u.userUD')
            ->where(
                $qb->expr()->eq('gmt.userID', ':userID')
            )
            ->setParameter('userID', $userID);
//dd($qb->getQuery()->getArrayResult());
        return $qb->getQuery()->getArrayResult();

    }
}
