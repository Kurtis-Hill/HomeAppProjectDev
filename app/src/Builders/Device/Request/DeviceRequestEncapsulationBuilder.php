<?php
declare(strict_types=1);

namespace App\Builders\Device\Request;

use App\DTOs\Device\Request\DeviceRequest\DeviceRequestDTOInterface;
use App\DTOs\Device\Request\DeviceRequest\DeviceRequestEncapsulationDTO;
use App\Entity\Device\Devices;
use App\Exceptions\Device\DeviceIPNotSetException;

class DeviceRequestEncapsulationBuilder
{
    private const FULL_SENSOR_URL = '%s://%s/%s';

    /**
     * @throws DeviceIPNotSetException
     */
    public static function buildDeviceRequestEncapsulation(
        Devices $device,
        DeviceRequestDTOInterface $deviceRequestDTO,
        string $endpoint,
        string $httpProtocol = 'http',
    ): DeviceRequestEncapsulationDTO {
        $deviceLocalIP = $device->getIpAddress();
        if ($deviceLocalIP === null) {
            throw new DeviceIPNotSetException('Device IP address is not set');
        }

        $fullSensorURL = sprintf(
            self::FULL_SENSOR_URL,
            $httpProtocol,
            $deviceLocalIP,
            $endpoint
        );

        return new DeviceRequestEncapsulationDTO(
            $fullSensorURL,
            $deviceRequestDTO,
        );
    }
}
