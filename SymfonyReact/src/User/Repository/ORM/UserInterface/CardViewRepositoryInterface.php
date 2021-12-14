<?php

namespace App\User\Repository\ORM\UserInterface;

use App\User\Entity\UserInterface\Card\CardView;
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
}
