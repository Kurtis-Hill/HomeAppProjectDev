<?php

namespace App\Services\Device\GetDevices;

use App\DTOs\Device\Internal\GetDeviceDTO;
use App\Entity\Device\Devices;
use App\Entity\User\User;
use JetBrains\PhpStorm\ArrayShape;

interface DevicesForUserInterface
{
    public const MAX_DEVICE_RETURN_SIZE = 100;

    #[ArrayShape([Devices::class])]
    public function getDevicesForUser(
        User $user,
        GetDeviceDTO $getDeviceDTO,
    ): array;
}
