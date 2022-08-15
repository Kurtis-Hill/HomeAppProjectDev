<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\UserInterface\Entity\Card\CardColour;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @method CardColour|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardColour|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardColour[]    findAll()
 * @method CardColour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
    #[ArrayShape(['colourID' => "int", 'colour' => "string", 'shade' => "string"])]
    public function getAllColoursAsArray(): array;

    #[ArrayShape([CardColour::class])]
    public function getAllColourObjects(): array;
}
