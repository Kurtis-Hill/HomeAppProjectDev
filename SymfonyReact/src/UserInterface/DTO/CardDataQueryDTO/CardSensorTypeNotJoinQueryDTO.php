<?php

namespace App\UserInterface\DTO\CardDataQueryDTO;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CardSensorTypeNotJoinQueryDTO
{
    private string $alias;

    private int $sensorTypeId;

    public function __construct(string $alias, int $sensorTypeId)
    {
        $this->alias = $alias;
        $this->sensorTypeId = $sensorTypeId;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getSensorTypeID(): int
    {
        return $this->sensorTypeId;
    }
}