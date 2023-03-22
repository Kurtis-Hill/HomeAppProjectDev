<?php

namespace App\Sensors\Builders\GetSensorQueryDTOBuilder;

use App\Sensors\DTO\Internal\Sensor\GetSensorQueryDTO;

class GetSensorQueryDTOBuilder
{
    public static function buildGetSensorQueryDTO(
        int $limit = null,
        int $offset = null,
        int $page = null,
        array $deviceIDs = [],
        array $deviceNames = [],
        array $groupIDs = []
    ): GetSensorQueryDTO {
        return new GetSensorQueryDTO(
            $limit,
            $offset,
            $page,
            $deviceIDs,
            $deviceNames,
            $groupIDs
        );
    }
}
