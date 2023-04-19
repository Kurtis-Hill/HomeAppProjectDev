<?php

namespace App\User\Repository\ORM;

use App\User\Entity\Group;
use App\User\Entity\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Exception\ORMException;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface GroupRepositoryInterface
{
    public function findOneById(int $id): ?Group;

    /**
     * @throws ORMException
     */
    public function findOneByName(string $name): ?Group;

    /**
     * @throws ORMException
     */
    public function persist(Group $groupNames): void;

    /**
     * @throws ORMException
     * @throws NonUniqueResultException
     */
    public function flush(): void;

    /**
     * @throws ORMException
     */
    public function remove(Group $groupNames): void;

    /**
     * @throws ORMException
     */
    public function findGroupsUserIsNotApartOf(User $user = null, array $groups = []): array;

    /**
     * @throws ORMException
     */
    public function findGroupsUserIsApartOf(User $user, array $groups = []): array;
}
