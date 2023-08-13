<?php

namespace App\Devices\Factories;

use App\Devices\Builders\DeviceRequestsArgumentBuilders\DeviceRequestArgumentBuilderInterface;
use App\Devices\Exceptions\DeviceRequestArgumentBuilderTypeNotFoundException;
use App\Sensors\Builders\RequestSensorCurrentReadingUpdateArgumentBuilder\RequestSensorCurrentReadingUpdateArgumentBuilder;

readonly class DeviceSensorRequestArgumentBuilderFactory
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
