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

        $qb->select('gn.groupnameid')
            ->innerJoin('App\Entity\Core\GroupName', 'gn', Join::WITH, 'gmt.groupnameid = gn.groupnameid')
            ->innerJoin('App\Entity\Core\User', 'u', Join::WITH, 'gmt.userID = u.userid')
            ->where(
                $qb->expr()->eq('gmt.userID', ':userID')
            )
            ->setParameter('userID', $userID);

        $result = $qb->getQuery()->getScalarResult();

        if (!$result) {
            throw new \Exception('Get User Groups Has Failed');
        }

        $groupNameIDs = array_map('current', $result);

        return $groupNameIDs;
    }

    public function getUserGroupNamesAndIds($userID): array
    {
        $qb = $this->createQueryBuilder('gmt');
        $qb->select('gn.groupname', 'gn.groupnameid')
            ->innerJoin('App\Entity\Core\GroupName', 'gn', Join::WITH, 'gmt.groupnameid = gn.groupnameid')
            ->innerJoin('App\Entity\Core\User', 'u', Join::WITH, 'gmt.userID = u.userid')
            ->where(
                $qb->expr()->eq('gmt.userID', ':userID')
            )
            ->setParameter('userID', $userID);

        return $qb->getQuery()->getArrayResult();

    }
}