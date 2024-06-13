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

        $flatterResult = [];
        foreach ($cardResultUnFlattened as $result) {
            $sensorID = $result['sensors_sensorID'];
            $flatterResult[$sensorID][] = array_filter($result, static function ($value) {
                return $value !== null;
            }, ARRAY_FILTER_USE_BOTH);
        }

        $flatteredMergedResult = [];
        foreach ($flatterResult as $flattenedResultKey => $flattenedResultValue) {
            if (count($flattenedResultValue) > 1) {
                array_walk_recursive($flattenedResultValue, static function ($a, $b) use (&$flatteredMergedResult, $flattenedResultKey) {
                    $flatteredMergedResult[$flattenedResultKey][$b] = $a;
                });
                continue;
            }
            $flatteredMergedResult[$flattenedResultKey] = $flattenedResultValue[0];
        }

        return $flatteredMergedResult;
    }
}
