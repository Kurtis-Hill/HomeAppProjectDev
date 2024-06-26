<?php
declare(strict_types=1);

namespace App\Events\Device;

use App\DTOs\Device\Internal\DeviceSettingsUpdateDTO;
use Symfony\Contracts\EventDispatcher\Event;

class DeviceUpdateEvent extends Event
{
    public const NAME = 'device.update';

    public function __construct(
        protected DeviceSettingsUpdateDTO $deviceUpdateEventDTO
    ) {
    }

    public function getDeviceUpdateEventDTO(): DeviceSettingsUpdateDTO
    {
        return $this->deviceUpdateEventDTO;
    }
}
