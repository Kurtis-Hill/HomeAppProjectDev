<?php

namespace App\User\Repository\ORM;

use App\User\Entity\GroupNames;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GroupNameRepository extends ServiceEntityRepository implements GroupNameRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupNames::class);
    }

    public function findOneById(int $id): ?GroupNames
    {
        return $this->findOneBy(['groupNameID' => $id]);
    }

    public function findOneByName(string $name): ?GroupNames
    {
        return $this->findOneBy(['groupName' => $name]);
    }
}
