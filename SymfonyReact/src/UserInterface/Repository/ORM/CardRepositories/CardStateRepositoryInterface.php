<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\UserInterface\Entity\Card\Cardstate;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\ArrayShape;

interface CardStateRepositoryInterface
{
    public function findOneById(int $id);

    /**
     * @throws ORMException
     */
    public function persist(Cardstate $cardState): void;

    /**
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
