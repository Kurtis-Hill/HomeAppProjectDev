<?php

namespace App\User\Services\User;

use App\User\Entity\User;
use App\User\Repository\ORM\UserRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class DeleteUserHandler
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function deleteUser(User $user): void
    {
        $this->userRepository->remove($user);
    }
}
