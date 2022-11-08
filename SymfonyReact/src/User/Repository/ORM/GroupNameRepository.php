<?php

namespace App\User\Repository\ORM;

use App\User\Entity\GroupNames;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
}
