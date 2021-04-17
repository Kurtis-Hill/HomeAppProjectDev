<?php


namespace App\Repository\Core;

use App\Entity\Core\GroupNames;
use App\Entity\Core\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use function Doctrine\ORM\QueryBuilder;

class GroupNameMappingTableRepository extends EntityRepository
{
    public function getGroupsForUser($userID): array
    {
        $qb = $this->createQueryBuilder('gmt');

        $qb->select('gn.groupNameID, gn.groupName')
            ->innerJoin(GroupNames::class, 'gn', Join::WITH, 'gmt.groupNameID = gn.groupNameID')
            ->innerJoin(User::class, 'u', Join::WITH, 'gmt.userID = u.userID')
            ->where(
                $qb->expr()->eq('gmt.userID', ':userID')
            )
            ->setParameter('userID', $userID);

        return $qb->getQuery()->getScalarResult();
    }

    public function getAllGroupMappingEntitiesForUser(User $user)
    {
        $qb = $this->createQueryBuilder('gmt');

        $qb->select('gmt')
            ->innerJoin(GroupNames::class, 'gn', Join::WITH, 'gmt.groupNameID = gn.groupNameID')
            ->innerJoin(User::class, 'u', Join::WITH, 'gmt.userID = u.userID')
            ->where(
                $qb->expr()->eq('gmt.userID', ':user')
            )
            ->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }

    public function findGroupsUserIsNotApartOf(array $groups)
    {
        $qb = $this->createQueryBuilder('gmt');

        $qb->select('gmt')
            ->where(
                $qb->expr()->notIn('gmt.groupNameID', ':groups'),
            )
            ->setParameter('groups', $groups);

        return $qb->getQuery()->getResult();
    }
}
