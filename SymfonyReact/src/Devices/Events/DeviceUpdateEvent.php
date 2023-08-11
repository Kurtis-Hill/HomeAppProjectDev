<?php

namespace App\Devices\Events;

use App\Devices\DTO\Request\DeviceRequest\DeviceSettingsUpdateEventDTO;
use Symfony\Contracts\EventDispatcher\Event;

class DeviceUpdateEvent extends Event
{
    public const NAME = 'device.update';

    public function __construct(
        protected DeviceSettingsUpdateEventDTO $deviceUpdateEventDTO
    ) {}

    public function getDeviceUpdateEventDTO(): DeviceSettingsUpdateEventDTO
    {
        return $this->deviceUpdateEventDTO;
    }
}
