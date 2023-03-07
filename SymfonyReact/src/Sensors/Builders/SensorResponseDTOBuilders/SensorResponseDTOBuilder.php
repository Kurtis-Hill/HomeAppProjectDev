<?php

namespace App\Sensors\Builders\SensorResponseDTOBuilders;

use App\Devices\Builders\DeviceUpdate\DeviceResponseDTOBuilder;
use App\Sensors\Builders\SensorTypeDTOBuilders\SensorTypeResponseDTOBuilder;
use App\Sensors\DTO\Response\SensorResponse\SensorFullResponseDTO;
use App\Sensors\DTO\Response\SensorResponse\SensorPartialResponseDTO;
use App\Sensors\Entity\Sensor;
use App\User\Builders\User\UserResponseBuilder;

class SensorResponseDTOBuilder
{
    public static function buildSensorResponseDTO(Sensor $sensor): SensorPartialResponseDTO
    {
        return new SensorPartialResponseDTO(
            $sensor->getSensorID(),
            $sensor->getSensorName(),
            $sensor->getSensorTypeObject()->getSensorType(),
            $sensor->getDevice()->getDeviceName(),
            $sensor->getCreatedBy()->getUsername(),
        );
    }

    public static function buildFullResponseDTO(Sensor $sensor): SensorFullResponseDTO
    {
        return new SensorFullResponseDTO(
            $sensor->getSensorID(),
            UserResponseBuilder::buildFullUserResponseDTO($sensor->getCreatedBy()),
            $sensor->getSensorName(),
            DeviceResponseDTOBuilder::buildDeviceIDResponseDTO($sensor->getDevice()),
            SensorTypeResponseDTOBuilder::buildFullSensorTypeResponseDTO($sensor->getSensorTypeObject()),
        );
    }
}
