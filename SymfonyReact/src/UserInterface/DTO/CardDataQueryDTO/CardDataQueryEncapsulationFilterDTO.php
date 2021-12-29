<?php

namespace App\UserInterface\DTO\CardDataQueryDTO;

use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CardDataQueryEncapsulationFilterDTO
{
    private array $sensorTypesToQuery;

    private array $sensorTypesToExclude;

    private array $readingTypesToQuery;

    public function __construct(array $sensorTypesToQuery, array $sensorTypesToExclude, array $readingTypesToQuery)
    {
        $this->sensorTypesToQuery = $sensorTypesToQuery;
        $this->sensorTypesToExclude = $sensorTypesToExclude;
        $this->readingTypesToQuery = $readingTypesToQuery;
    }

    #[ArrayShape([CardSensorTypeJoinQueryDTO::class])]
    public function getSensorTypesToQuery(): array
    {
        return $this->sensorTypesToQuery;
    }


    #[ArrayShape([CardSensorTypeNotJoinQueryDTO::class])]
    public function getSensorTypesToExclude(): array
    {
        return $this->sensorTypesToExclude;
    }

    #[ArrayShape([CardSensorTypeJoinQueryDTO::class])]
    public function getReadingTypesToQuery(): array
    {
        return $this->readingTypesToQuery;
    }
}
