<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\UserInterface\Entity\Card\Cardstate;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

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
    #[ArrayShape([Cardstate::class])]
    public function getAllStates(): array;

    public function findOneByState(string $state): ?Cardstate;
}
