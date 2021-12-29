<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\User\Entity\User;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

interface CardViewRepositoryInterface
{
    /**
     * @throws ORMException
     */
    public function persist(CardView $cardView): void;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void;

    public function getAllIndexCardDataForUser(User $user, array $filters): array;
}
