<?php

namespace App\Repository\UserInterface\ORM\CardRepositories;

use App\Entity\UserInterface\Card\CardState;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @method CardState|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardState|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardState[]    findAll()
 * @method CardState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface CardStateRepositoryInterface
{
    public function findOneById(int $id);

    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(CardState $cardState): void;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void;

    /**
     * @throws ORMException
     */
    #[ArrayShape(['stateID' => 'int', 'state' => 'string'])]
    public function getAllStatesAsArray(): array;

    #[ArrayShape([CardState::class])]
    public function getAllStateAsObjects(): array;

    public function findOneByState(string $state): ?CardState;
}
