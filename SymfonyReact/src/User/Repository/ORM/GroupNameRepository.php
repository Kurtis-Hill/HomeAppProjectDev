<?php

namespace App\User\Repository\ORM;

use App\Entity\Core\GroupNames;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GroupNameRepository extends ServiceEntityRepository implements GroupNameRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, 'GroupName');
    }

    public function findOneById(int $id): ?GroupNames
    {
        return $this->findOneBy(['groupNameID' => $id]);
    }
}
