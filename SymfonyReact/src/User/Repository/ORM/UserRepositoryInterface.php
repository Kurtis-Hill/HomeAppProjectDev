<?php

namespace App\User\Repository\ORM;

use App\User\Entity\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;

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
}
