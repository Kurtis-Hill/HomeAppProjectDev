<?php
declare(strict_types=1);

namespace App\Builders\Sensor\Request\GetSensorQueryDTOBuilder;

use App\DTOs\Sensor\Internal\Sensor\GetSensorQueryDTO;

class GetSensorQueryDTOBuilder
{
    public static function buildGetSensorQueryDTO(
        ?array $deviceIDs = null,
        ?array $deviceNames = null,
        ?array $groupIDs = null,
        ?array $cardViewIDs = null
    ): GetSensorQueryDTO {
        return new GetSensorQueryDTO(
            deviceIDs: $deviceIDs,
            deviceNames: $deviceNames,
            groupIDs: $groupIDs,
            cardViewIDs: $cardViewIDs
        );
    }
}
