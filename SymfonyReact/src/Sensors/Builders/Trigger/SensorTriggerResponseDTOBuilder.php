<?php

namespace App\Sensors\Builders\Trigger;

use App\Sensors\DTO\Response\SensorResponse\SensorTriggerResponseDTO;

class SensorTriggerResponseDTOBuilder
{
    public static function buildSensorTriggerResponseDTO(): SensorTriggerResponseDTO
    {
        return new SensorTriggerResponseDTO();
    }
}
