<?php

namespace App\Authentication\Repository\ORM;

use App\Authentication\Entity\GroupNameMapping;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupNameMapping>
 *
 * @method GroupNameMapping|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupNameMapping|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupNameMapping[]    findAll()
 * @method GroupNameMapping[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupNameMappingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupNameMapping::class);
    }

    public function getAllGroupMappingEntitiesForUser(User $user): array
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

    public function findGroupsUserIsNotApartOf(array $groups): array
    {
        $qb = $this->createQueryBuilder('gmt');

        $qb->select('gmt')
            ->where(
                $qb->expr()->notIn('gmt.groupNameID', ':groups'),
            )
            ->setParameter('groups', $groups);

        return $qb->getQuery()->getResult();
    }

    public function persist(GroupNameMapping $groupNameMapping): void
    {
        $this->getEntityManager()->persist($groupNameMapping);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(GroupNameMapping $groupNameMapping): void
    {
        $this->getEntityManager()->remove($groupNameMapping);
        $this->getEntityManager()->flush();
    }
}
