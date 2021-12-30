<?php

namespace App\UserInterface\Services\Cards\CardDataFilterService;

use App\UserInterface\DTO\CardDataFiltersDTO\CardDataPreFilterDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use Doctrine\ORM\ORMException;

interface CardDataFilterServiceInterface
{
    /**
     * @throws ORMException
     */
    public function filterSensorsToQuery(CardDataPreFilterDTO $cardFilters): CardDataQueryEncapsulationFilterDTO;

    public function preparePreFilterDTO(array $sensorTypesToFilter, array $readingTypesToFilter): CardDataPreFilterDTO;
}
