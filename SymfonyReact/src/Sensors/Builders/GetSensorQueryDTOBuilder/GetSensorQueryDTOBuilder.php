<?php
declare(strict_types=1);

namespace App\Sensors\Builders\GetSensorQueryDTOBuilder;

use App\Sensors\DTO\Internal\Sensor\GetSensorQueryDTO;

class GetSensorQueryDTOBuilder
{
    public static function buildGetSensorQueryDTO(
        int $limit = null,
        int $offset = null,
        int $page = null,
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
