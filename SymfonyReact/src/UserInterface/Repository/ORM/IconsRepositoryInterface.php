<?php

namespace App\UserInterface\Repository\ORM;

use App\UserInterface\Entity\Icons;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\ArrayShape;

interface IconsRepositoryInterface
{
    public function findOneById(int $id);

    /**
     * @throws ORMException
     */
    public function persist(Icons $cardColour): void;

    /**
     * @throws ORMException
     */
    public function flush(): void;

    /**
     * @throws NonUniqueResultException
     */
    public function countAllIcons(): int;

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getFirstIcon(): Icons;

    /**
     * @throws ORMException
     */
    #[ArrayShape([Icons::class])]
    public function getAllIcons(): array;
}
