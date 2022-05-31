<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\User\Entity\User;
use App\UserInterface\DTO\Internal\CardDataFiltersDTO\CardViewTypeFilterDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;

interface CardViewRepositoryInterface
{
    /**
     * @throws ORMInvalidArgumentException
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
    public function getAllCardSensorData(
        User $user,
        string $cardViewTwo,
        CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO,
        CardViewTypeFilterDTO $cardViewTypeFilterDTO = null,
        int $hydrationMode = AbstractQuery::HYDRATE_SCALAR,
    ): array;

    /**
     * @throws ORMException
     */
    public function findOneById(int $cardViewID): ?CardView;

}
