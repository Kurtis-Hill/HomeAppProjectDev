<?php

namespace App\UserInterface\DTO\CardDataFiltersDTO;

use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeQueryDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CardDataPostFilterDTO
{
    private array $sensorTypesToQuery;

    private array $readingTypesToQuery;

    public function __construct(array $sensorTypesToQuery, array $readingTypesToQuery)
    {
        $this->sensorTypesToQuery = $sensorTypesToQuery;
        $this->readingTypesToQuery = $readingTypesToQuery;
    }

    #[ArrayShape([CardSensorTypeQueryDTO::class])]
    public function getSensorTypesToQuery(): array
    {
        return $this->sensorTypesToQuery;
    }

    public function getReadingTypesToQuery(): array
    {
        return $this->readingTypesToQuery;
    }
}
