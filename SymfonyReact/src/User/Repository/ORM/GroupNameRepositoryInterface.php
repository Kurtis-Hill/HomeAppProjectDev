<?php

namespace App\User\Repository\ORM;

use App\User\Entity\GroupNames;
use App\User\Entity\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Exception\ORMException;

/**
 * @method GroupNames|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupNames|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupNames[]    findAll()
 * @method GroupNames[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface GroupNameRepositoryInterface
{
    public function findOneById(int $id): ?GroupNames;

    public function findOneByName(string $name): ?GroupNames;

    public function persist(GroupNames $groupNames): void;

    public function flush(): void;

    public function remove(GroupNames $groupNames): void;

    public function findGroupsUserIsNotApartOf(User $user = null, array $groups = []): array;

    public function findGroupsUserIsApartOf(User $user, array $groups = []): array;
}
