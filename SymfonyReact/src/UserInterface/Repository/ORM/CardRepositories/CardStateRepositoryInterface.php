<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\UserInterface\Entity\Card\Cardstate;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @method Cardstate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cardstate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cardstate[]    findAll()
 * @method Cardstate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface CardStateRepositoryInterface
{
    public function findOneById(int $id);

    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(Cardstate $cardState): void;

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

    #[ArrayShape([Cardstate::class])]
    public function getAllStateAsObjects(): array;

    public function findOneByState(string $state): ?Cardstate;
}
