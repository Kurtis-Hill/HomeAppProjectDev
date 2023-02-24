<?php

namespace App\User\Repository\ORM;

use App\Authentication\Entity\GroupNameMapping;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupNames>
 *
 * @method GroupNames|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupNames|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupNames[]    findAll()
 * @method GroupNames[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupNameRepository extends ServiceEntityRepository implements GroupNameRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupNames::class);
    }

    public function findOneById(int $id): ?GroupNames
    {
        return $this->find($id);
    }

    public function findOneByName(string $name): ?GroupNames
    {
        return $this->findOneBy(['groupName' => $name]);
    }

    public function persist(GroupNames $groupNames): void
    {
        $this->getEntityManager()->persist($groupNames);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throw UniqueConstraintViolationException
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(GroupNames $groupNames): void
    {
        $this->getEntityManager()->remove($groupNames);
        $this->getEntityManager()->flush();
    }

    public function findGroupsUserIsNotApartOf(User $user = null, array $groups = []): array
    {
        $qb = $this->createQueryBuilder('gn');

        $expr = $qb->expr();
        $qb->select('gn')
            ->leftJoin(GroupNameMapping::class, 'gnm', Join::WITH, 'gnm.groupName = gn.groupNameID')
            ->leftJoin(User::class, 'u', Join::WITH, 'gnm.user = u.userID')
            ->where(
                $expr->orX(
                $expr->notIn('gn.groupNameID', ':groups'),
                    $expr->notIn('u.groupNameID', ':userGroups')
                )
            )
            ->setParameter('groups', $user?->getAssociatedGroupNameIds())
            ->setParameter('userGroups', $user?->getAssociatedGroupNameIds());

        if (!empty($groups)) {
            $qb->andWhere($expr->notIn('gn.groupNameID', ':groups'));
            $qb->setParameter('groups', $groups);
        }
        return $qb->getQuery()->getResult();
    }

    public function findGroupsUserIsApartOf(array $groups, User $user): array
    {
        $qb = $this->createQueryBuilder('gn');

        $expr = $qb->expr();
        $qb->select('gn')
            ->leftJoin(GroupNameMapping::class, 'gnm', Join::WITH, 'gnm.groupName = gn.groupNameID')
            ->leftJoin(User::class, 'u', Join::WITH, 'gnm.user = u.userID')
            ->where(
                $expr->orX(
                    $expr->in('gn.groupNameID', ':groups'),
                    $expr->in('u.groupNameID', ':userGroups')
                )
            )
            ->setParameter('groups', $user?->getAssociatedGroupNameIds())
            ->setParameter('userGroups', $user?->getAssociatedGroupNameIds());

        if (!empty($groups)) {
            $qb->andWhere($expr->in('gn.groupNameID', ':groups'));
            $qb->setParameter('groups', $groups);
        }
        return $qb->getQuery()->getResult();
    }
}
