<?php

namespace App\UserInterface\DTO\Internal\CardDataFiltersDTO;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CardDataPreFilterDTO
{
    private array $sensorTypesToFilter;

    private array $readingTypesToFilter;

    public function __construct(
        array $sensorTypesToFilter,
        array $readingTypesToFilter
    ) {
        $this->sensorTypesToFilter = $sensorTypesToFilter;
        $this->readingTypesToFilter = $readingTypesToFilter;
    }

    public function getSensorTypesToFilter(): array
    {
        return $this->sensorTypesToFilter;
    }

    public function getReadingTypesToFilter(): array
    {
        return $this->readingTypesToFilter;
    }
}
