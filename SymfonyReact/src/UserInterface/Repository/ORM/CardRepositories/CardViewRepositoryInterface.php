<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\User\Entity\User;
use App\UserInterface\DTO\CardDataFiltersDTO\CardViewTypeFilterDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\Entity\Card\CardView;
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

    /**
     * @throws ORMException
     */
    public function getAllCardSensorDataScalar(
        User $user,
        string $cardViewTwo,
        CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO,
        CardViewTypeFilterDTO $cardViewTypeFilterDTO = null
    ): array;

    /**
     * @throws ORMException
     */
    public function findOneById(int $cardViewID): ?CardView;

}
