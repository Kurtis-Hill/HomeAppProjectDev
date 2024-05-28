<?php

namespace App\Sensors\Builders\Internal\ReadingType\ReadingTypeQueryDTOBuilders;

use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;

interface ReadingTypeQueryDTOBuilderInterface
{
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO;
}
