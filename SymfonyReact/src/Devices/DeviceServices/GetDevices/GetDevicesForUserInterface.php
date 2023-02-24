<?php

namespace App\Devices\DeviceServices\GetDevices;

use App\Devices\Entity\Devices;
use App\User\Entity\User;
use JetBrains\PhpStorm\ArrayShape;

interface GetDevicesForUserInterface
{
    public const MAX_DEVICE_RETURN_SIZE = 100;

    #[ArrayShape([Devices::class])]
    public function getDevicesForUser(
        User $user,
        $limit = GetDevicesForUserInterface::MAX_DEVICE_RETURN_SIZE,
        $offset = 0,
    ): array;
}
