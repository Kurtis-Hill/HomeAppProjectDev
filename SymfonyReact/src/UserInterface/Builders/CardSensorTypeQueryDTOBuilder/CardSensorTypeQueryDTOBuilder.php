<?php

namespace App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder;

use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeQueryDTO;

interface CardSensorTypeQueryDTOBuilder
{
    public function buildSensorTypeQueryDTO(): CardSensorTypeQueryDTO;
}
