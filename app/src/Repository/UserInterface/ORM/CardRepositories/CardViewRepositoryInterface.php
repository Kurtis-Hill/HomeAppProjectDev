<?php

namespace App\Repository\UserInterface\ORM\CardRepositories;

use App\DTOs\UserInterface\Internal\CardDataFiltersDTO\CardViewUriFilterDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\Entity\User\User;
use App\Entity\UserInterface\Card\CardView;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;

/**
 * @method CardView|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardView|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardView[]    findAll()
 * @method CardView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
    public function findAllCardSensorDataForUser(
        User $user,
        string $cardViewTwo,
        CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO,
        CardViewUriFilterDTO $cardViewTypeFilterDTO = null,
        int $hydrationMode = AbstractQuery::HYDRATE_SCALAR,
    ): array;
}
