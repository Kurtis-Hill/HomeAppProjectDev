<?php

namespace App\Services\User\User;

use App\Entity\User\User;
use App\Repository\User\ORM\UserRepositoryInterface;
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
