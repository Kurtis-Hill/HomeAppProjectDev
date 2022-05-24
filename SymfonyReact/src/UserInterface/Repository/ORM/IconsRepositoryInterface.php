<?php

namespace App\UserInterface\Repository\ORM;

use App\UserInterface\Entity\Icons;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

interface IconsRepositoryInterface
{
    public function findOneById(int $id);

    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(Icons $cardColour): void;

    /**
     * @throws OptimisticLockException
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
    #[ArrayShape(['iconID' => 'int', 'iconName' => 'string', 'description' => 'string'])]
    public function getAllIconsAsArray(): array;

    #[ArrayShape([Icons::class])]
    public function getAllIconObjects(): array;
}
