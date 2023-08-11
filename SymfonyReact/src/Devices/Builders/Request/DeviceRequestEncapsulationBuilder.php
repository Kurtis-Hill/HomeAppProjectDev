<?php

namespace App\Devices\Builders\Request;

use App\Devices\DTO\Request\DeviceRequest\DeviceRequestDTOInterface;
use App\Devices\DTO\Request\DeviceRequest\DeviceRequestEncapsulationDTO;

class DeviceRequestEncapsulationBuilder
{
    private const FULL_SENSOR_URL = '%s://%s/%s';

    public static function buildDeviceRequestEncapsulation(
        string $ipAddress,
        DeviceRequestDTOInterface $deviceRequestDTO,
        string $endpoint,
        string $httpProtocol = 'http',
    ): DeviceRequestEncapsulationDTO {
        $fullSensorURL = sprintf(
            self::FULL_SENSOR_URL,
            $httpProtocol,
            $ipAddress,
            $endpoint
        );

        return new DeviceRequestEncapsulationDTO(
            $fullSensorURL,
            $deviceRequestDTO,
        );
    }
}
