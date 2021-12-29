<?php

namespace App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder;

use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeJoinQueryDTO;

interface CardReadingTypeQueryDTOBuilderInterface
{
    public function buildReadingTypeJoinQueryDTO(): CardSensorTypeJoinQueryDTO;
}
