<?php

namespace App\Devices\DeviceServices\GetDevices;

use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepository;
use App\User\Entity\User;
use App\User\Services\GroupNameServices\GetGroupNamesHandler;
use JetBrains\PhpStorm\ArrayShape;

class GetDevicesForUserHandler implements GetDevicesForUserInterface
{
    private DeviceRepository $deviceRepository;

    private GetGroupNamesHandler $getGroupNamesHandler;

    public function __construct(DeviceRepository $deviceRepository, GetGroupNamesHandler $getGroupNamesHandler)
    {
        $this->deviceRepository = $deviceRepository;
        $this->getGroupNamesHandler = $getGroupNamesHandler;
    }

    #[ArrayShape([Devices::class])]
    public function getDevicesForUser(
        User $user,
        $limit = GetDevicesForUserInterface::MAX_DEVICE_RETURN_SIZE,
        $offset = 0,
    ): array {
        $groupNamesForUser = $this->getGroupNamesHandler->getGroupNameDataForUser($user);

        return $this->deviceRepository->findAllDevicesByGroupNamePaginated(
            $groupNamesForUser,
            $limit,
            $offset,
        );
    }
}
