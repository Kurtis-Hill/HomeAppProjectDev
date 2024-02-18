<?php

namespace App\User\Repository\ORM;

use App\User\Entity\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface UserRepositoryInterface
{
    /**
     * @throws ORMException|NonUniqueResultException
     */
    public function findOneById(int $id): ?User;

    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(User $user): void;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function remove(User $user): void;
}
