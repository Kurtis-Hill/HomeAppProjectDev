<?php
declare(strict_types=1);

namespace App\Authentication\Repository\ORM;

use App\Authentication\Entity\GroupMapping;
use App\User\Entity\Group;
use App\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupMapping>
 *
 * @method GroupMapping|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupMapping|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupMapping[]    findAll()
 * @method GroupMapping[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupMappingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupMapping::class);
    }

    public function getAllGroupMappingEntitiesForUser(User $user): array
    {
        $qb = $this->createQueryBuilder('gmt');

        $qb->select('gmt')
            ->innerJoin(Group::class, 'gn', Join::WITH, 'gmt.groupID = gn.groupID')
            ->innerJoin(User::class, 'u', Join::WITH, 'gmt.user = u.userID')
            ->where(
                $qb->expr()->eq('gmt.user', ':user')
            )
            ->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws ORMException
     */
    public function persist(GroupMapping $groupNameMapping): void
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

    public function remove(GroupMapping $groupNameMapping): void
    {
        $this->getEntityManager()->remove($groupNameMapping);
        $this->getEntityManager()->flush();
    }
}
