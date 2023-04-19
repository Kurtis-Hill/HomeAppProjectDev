<?php

namespace App\Devices\DeviceServices\GetDevices;

use App\Devices\Builders\DeviceResponse\DeviceResponseDTOBuilder;
use App\Devices\DTO\Internal\GetDeviceDTO;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepository;
use App\User\Entity\User;
use App\User\Services\GroupNameServices\UserGroupsFinder;
use JetBrains\PhpStorm\ArrayShape;

class GetDevicesForUserHandler implements GetDevicesForUserInterface
{
    private DeviceRepository $deviceRepository;

    private UserGroupsFinder $getGroupNamesHandler;

    public function __construct(DeviceRepository $deviceRepository, UserGroupsFinder $getGroupNamesHandler)
    {
        $this->deviceRepository = $deviceRepository;
        $this->getGroupNamesHandler = $getGroupNamesHandler;
    }

    #[ArrayShape([Devices::class])]
    public function getDevicesForUser(
        User $user,
        GetDeviceDTO $getDeviceDTO,
    ): array {
        $groupNamesForUser = $this->getGroupNamesHandler->getGroupNamesForUser($user);

        return $this->deviceRepository->findAllDevicesByGroupNamePaginated(
            $groupNamesForUser,
            $getDeviceDTO->getLimit(),
            $getDeviceDTO->getOffset(),
        );
    }

    /**
     * @param Devices[] $devices
     */
    public function handleDeviceResponseDTOCreation(array $devices): array
    {
        $deviceResponseDTOs = [];
        foreach ($devices as $device) {
            $deviceResponseDTOs[] = DeviceResponseDTOBuilder::buildDeviceOnlyResponseDTO($device);
        }

        return $deviceResponseDTOs;
    }
}
