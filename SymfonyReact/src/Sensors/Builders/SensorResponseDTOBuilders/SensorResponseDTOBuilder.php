<?php

namespace App\Sensors\Builders\SensorResponseDTOBuilders;

use App\Devices\Builders\DeviceResponse\DeviceResponseDTOBuilder;
use App\Sensors\Builders\SensorTypeDTOBuilders\SensorTypeResponseDTOBuilder;
use App\Sensors\DTO\Response\SensorResponse\SensorDetailedResponseDTO;
use App\Sensors\DTO\Response\SensorResponse\SensorFullResponseDTO;
use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use App\Sensors\Entity\Sensor;
use App\User\Builders\User\UserResponseBuilder;

class SensorResponseDTOBuilder
{
    public static function buildResponseDTO(Sensor $sensor): SensorResponseDTO
    {
        return new SensorResponseDTO(
            $sensor->getSensorID(),
            $sensor->getSensorName(),
            $sensor->getSensorTypeObject()->getSensorType(),
            $sensor->getDevice()->getDeviceName(),
            $sensor->getCreatedBy()->getUsername(),
        );
    }

    public static function buildDetailedResponseDTO(Sensor $sensor): SensorDetailedResponseDTO
    {
        return new SensorDetailedResponseDTO(
            $sensor->getSensorID(),
            UserResponseBuilder::buildFullUserResponseDTO($sensor->getCreatedBy()),
            $sensor->getSensorName(),
            DeviceResponseDTOBuilder::buildDeviceResponseDTO($sensor->getDevice()),
            SensorTypeResponseDTOBuilder::buildFullSensorTypeResponseDTO($sensor->getSensorTypeObject()),
        );
    }

    public static function buildFullResponseDTO(Sensor $sensor, array $sensorTypeResponseDTOs = []): SensorFullResponseDTO
    {
        return new SensorFullResponseDTO(
            self::buildResponseDTO($sensor),
            $sensorTypeResponseDTOs
        );
    }
}
