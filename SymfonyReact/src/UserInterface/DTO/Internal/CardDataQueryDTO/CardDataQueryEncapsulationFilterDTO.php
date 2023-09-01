<?php

namespace App\UserInterface\DTO\Internal\CardDataQueryDTO;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CardDataQueryEncapsulationFilterDTO
{
    #[ArrayShape([JoinQueryDTO::class])]
    private array $sensorTypesToQuery;

    #[ArrayShape([SensorTypeNotJoinQueryDTO::class])]
    private array $sensorTypesToExclude;

    #[ArrayShape([JoinQueryDTO::class])]
    private array $readingTypesToQuery;

    public function __construct(array $sensorTypesToQuery, array $sensorTypesToExclude, array $readingTypesToQuery)
    {
        $this->sensorTypesToQuery = $sensorTypesToQuery;
        $this->sensorTypesToExclude = $sensorTypesToExclude;
        $this->readingTypesToQuery = $readingTypesToQuery;
    }

    #[ArrayShape([JoinQueryDTO::class])]
    public function getSensorTypesToQuery(): array
    {
        return $this->sensorTypesToQuery;
    }


    #[ArrayShape([SensorTypeNotJoinQueryDTO::class])]
    public function getSensorTypesToExclude(): array
    {
        return $this->sensorTypesToExclude;
    }

    #[ArrayShape([JoinQueryDTO::class])]
    public function getReadingTypesToQuery(): array
    {
        return $this->readingTypesToQuery;
    }
}
