<?php

namespace App\Devices\Factories;

use App\Devices\Builders\DeviceRequestsArgumentBuilders\DeviceRequestArgumentBuilderInterface;
use App\Devices\Builders\DeviceRequestsArgumentBuilders\RequestSensorCurrentReadingUpdateArgumentBuilder;
use App\Devices\Exceptions\DeviceRequestArgumentBuilderTypeNotFoundException;

readonly class DeviceRequestArgumentBuilderFactory
{
    public const UPDATE_SENSOR_CURRENT_READING = 'update-sensor';

    public function __construct(
        private RequestSensorCurrentReadingUpdateArgumentBuilder $requestSensorCurrentReadingUpdateArgumentBuilder,
    ) {}

    /**
     * @throws DeviceRequestArgumentBuilderTypeNotFoundException
     */
    public function fetchDeviceRequestArgumentBuilder(string $deviceRequestType): DeviceRequestArgumentBuilderInterface
    {
        return match ($deviceRequestType) {
            self::UPDATE_SENSOR_CURRENT_READING => $this->requestSensorCurrentReadingUpdateArgumentBuilder,
            default => throw new DeviceRequestArgumentBuilderTypeNotFoundException(),
        };
    }
}
