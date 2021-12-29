<?php

namespace App\UserInterface\Services\Cards\CardDataFilterService;

use App\ESPDeviceSensor\Exceptions\SensorTypeBuilderFailureException;
use App\UserInterface\DTO\CardDataFiltersDTO\CardDataPostFilterDTO;
use App\UserInterface\DTO\CardDataFiltersDTO\CardDataPreFilterDTO;
use Doctrine\ORM\ORMException;

interface CardDataFilterServiceInterface
{
    /**
     * @throws SensorTypeBuilderFailureException
     * @throws ORMException
     */
    public function filterSensorTypes(CardDataPreFilterDTO $cardFilters): CardDataPostFilterDTO;
}
