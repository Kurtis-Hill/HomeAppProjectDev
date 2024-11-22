<?php
declare(strict_types=1);

namespace App\Services\UserInterface\Cards\CardPreparation;

use App\Controller\UserInterface\Card\GetCardViewController;
use App\DTOs\UserInterface\Internal\CardDataFiltersDTO\CardViewUriFilterDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\Entity\User\User;
use App\Entity\UserInterface\Card\CardState;
use App\Repository\UserInterface\ORM\CardRepositories\CardViewRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;

class CurrentReadingCardViewPreparationHandler
{
    private CardViewRepositoryInterface $cardViewRepository;

    public function __construct(CardViewRepositoryInterface $cardViewRepository)
    {
        $this->cardViewRepository = $cardViewRepository;
    }

    /**
     * @throws ORMException
     */
    public function prepareCardsForUser(
        User $user,
        CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO,
        CardViewUriFilterDTO $cardViewTypeFilterDTO,
        string $view = null,
    ): array {
        $cardViewTwo = match ($view) {
            GetCardViewController::ROOM_VIEW => CardState::ROOM_ONLY,
            GetCardViewController::DEVICE_VIEW => CardState::DEVICE_ONLY,
            default => CardState::INDEX_ONLY
        };

        $cardResultUnFlattened = $this->cardViewRepository->findAllCardSensorDataForUser(
            $user,
            $cardViewTwo,
            $cardDataPostFilterDTO,
            $cardViewTypeFilterDTO
        );

        $flattenedResult = [];
        foreach ($cardResultUnFlattened as $result) {
            $sensorID = $result['sensors_sensorID'];
            $filteredResult = array_filter($result, static fn($value) => $value !== null, ARRAY_FILTER_USE_BOTH);
            if (isset($flattenedResult[$sensorID])) {
                $flattenedResult[$sensorID] = array_merge($flattenedResult[$sensorID], $filteredResult);
            } else {
                $flattenedResult[$sensorID] = $filteredResult;
            }
        }

        return $flattenedResult;
    }
}
