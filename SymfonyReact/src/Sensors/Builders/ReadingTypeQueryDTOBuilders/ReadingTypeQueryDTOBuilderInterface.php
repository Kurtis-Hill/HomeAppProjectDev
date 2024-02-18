<?php

namespace App\Sensors\Builders\ReadingTypeQueryDTOBuilders;

use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;

interface ReadingTypeQueryDTOBuilderInterface
{
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO;
}
