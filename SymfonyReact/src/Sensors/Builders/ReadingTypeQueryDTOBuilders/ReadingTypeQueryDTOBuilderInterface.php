<?php

namespace App\Sensors\Builders\ReadingTypeQueryDTOBuilders;

use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;

interface ReadingTypeQueryDTOBuilderInterface
{
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO;
}
