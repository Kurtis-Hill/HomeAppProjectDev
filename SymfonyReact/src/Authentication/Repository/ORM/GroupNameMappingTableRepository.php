<?php

namespace App\Authentication\Repository\ORM;

use App\Authentication\Entity\GroupNameMapping;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class GroupNameMappingTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupNameMapping::class);
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
