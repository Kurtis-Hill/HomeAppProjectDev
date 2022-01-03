<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeQueryTypeDTOBuilders;

use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;

interface ReadingTypeQueryTypeDTOBuildersInterface
{
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO;
}
