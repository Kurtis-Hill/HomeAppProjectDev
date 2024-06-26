<?php
declare(strict_types=1);

namespace App\Builders\Sensor\Request\GetSensorQueryDTOBuilder;

use App\DTOs\Sensor\Internal\Sensor\GetSensorQueryDTO;

class GetSensorQueryDTOBuilder
{
    public static function buildGetSensorQueryDTO(
        int $limit = 50,
        int $offset = 0,
        int $page = 1,
        ?array $deviceIDs = null,
        ?array $deviceNames = null,
        ?array $groupIDs = null,
        ?array $cardViewIDs = null
    ): GetSensorQueryDTO {
        return new GetSensorQueryDTO(
            $limit,
            $offset,
            $page,
            $deviceIDs,
            $deviceNames,
            $groupIDs,
            $cardViewIDs
        );
    }
}
