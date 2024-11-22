<?php
declare(strict_types=1);

namespace App\Factories\Device;

use App\Builders\Device\DeviceRequestsArgumentBuilders\DeviceRequestArgumentBuilderInterface;
use App\Builders\Sensor\Request\RequestSensorCurrentReadingUpdateArgumentBuilder\RequestSensorCurrentReadingUpdateArgumentBuilder;
use App\Exceptions\Device\DeviceRequestArgumentBuilderTypeNotFoundException;

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
