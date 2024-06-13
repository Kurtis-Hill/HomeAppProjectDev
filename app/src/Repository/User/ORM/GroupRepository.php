<?php

namespace App\Repository\User\ORM;

use App\Entity\Authentication\GroupMapping;
use App\Entity\User\Group;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 *
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository implements GroupRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function findOneById(int $id): ?Group
    {
        return $this->find($id);
    }

    public function findOneByName(string $name): ?Group
    {
        return $this->findOneBy(['groupName' => $name]);
    }

    public function persist(Group $groupNames): void
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

    public function remove(Group $groupNames): void
    {
        $this->getEntityManager()->remove($groupNames);
        $this->getEntityManager()->flush();
    }

    public function findGroupsUserIsNotApartOf(User $user = null, array $groups = []): array
    {
        $qb = $this->createQueryBuilder('gn');
        $expr = $qb->expr();

        $qb->select('gn')
            ->leftJoin(GroupMapping::class, 'gnm', Join::WITH, 'gnm.groupID = gn.groupID')
            ->leftJoin(User::class, 'u', Join::WITH, 'gnm.user = u.userID')
            ->where(
//                $expr->orX(
                $expr->notIn('gn.groupID', ':groups'),
//                    $expr->notIn('u.groupID', ':userGroups')
//                )
            )
            ->setParameter('groups', $user?->getAssociatedGroupIDs());

        if (!empty($groups)) {
            $qb->andWhere($expr->notIn('gn.groupID', ':groups'));
            $qb->setParameter('groups', $groups);
        }

        return $qb->getQuery()->getResult();
    }

    public function findGroupsUserIsApartOf(User $user, array $groups = []): array
    {
        $qb = $this->createQueryBuilder('gn');

        $expr = $qb->expr();
        $qb->select('gn')
            ->leftJoin(GroupMapping::class, 'gnm', Join::WITH, 'gnm.groupID = gn.groupID')
            ->leftJoin(User::class, 'u', Join::WITH, 'gnm.user = u.userID')
            ->where(
                $expr->orX(
                    $expr->in('gn.groupID', ':groups'),
                    $expr->in('u.groupID', ':userGroups')
                )
            )
            ->setParameter('groups', $user->getAssociatedGroupIDs())
            ->setParameter('userGroups', $user->getAssociatedGroupIDs());

        if (!empty($groups)) {
            $qb->andWhere($expr->in('gn.groupID', ':groups'));
            $qb->setParameter('groups', $groups);
        }
        return $qb->getQuery()->getResult();
    }
}
