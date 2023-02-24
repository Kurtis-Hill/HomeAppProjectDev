<?php

namespace App\Devices\DeviceServices\GetDevices;

use App\Devices\DTO\Internal\GetDeviceDTO;
use App\Devices\Entity\Devices;
use App\User\Entity\User;
use JetBrains\PhpStorm\ArrayShape;

interface GetDevicesForUserInterface
{
    public const MAX_DEVICE_RETURN_SIZE = 100;

    #[ArrayShape([Devices::class])]
    public function getDevicesForUser(
        User $user,
        GetDeviceDTO $getDeviceDTO,
    ): array;

    /**
     * @param Devices[] $devices
     */
    public function handleDeviceResponseDTOCreation(array $devices): array;
}
