<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\UserInterface\Entity\Card\CardColour;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

interface CardColourRepositoryInterface
{
    public function findOneById(int $id);

    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(CardColour $cardColour): void;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void;

    /**
     * @throws NonUniqueResultException
     */
    public function getFirstColourId(): CardColour;

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countAllColours(): int;

    /**
     * @throws ORMException
     */
    #[ArrayShape([CardColour::class])]
    public function getAllColours(): array;
}