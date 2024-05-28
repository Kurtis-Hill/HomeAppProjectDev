<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\UserInterface\Entity\Card\Colour;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @method Colour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Colour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Colour[]    findAll()
 * @method Colour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface CardColourRepositoryInterface
{
    public function findOneById(int $id);

    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(Colour $cardColour): void;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void;

    /**
     * @throws NonUniqueResultException
     */
    public function getFirstColourID(): Colour;

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countAllColours(): int;

    /**
     * @throws ORMException
     */
    #[ArrayShape(['colourID' => "int", 'colour' => "string", 'shade' => "string"])]
    public function getAllColoursAsArray(): array;

    #[ArrayShape([Colour::class])]
    public function getAllColourObjects(): array;
}
