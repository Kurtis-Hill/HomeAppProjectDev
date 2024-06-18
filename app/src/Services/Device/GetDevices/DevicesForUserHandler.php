<?php
declare(strict_types=1);

namespace App\Services\Device\GetDevices;

use App\DTOs\Device\Internal\GetDeviceDTO;
use App\Entity\Device\Devices;
use App\Entity\User\User;
use App\Repository\Device\ORM\DeviceRepository;
use App\Services\User\GroupServices\UserGroupsFinder;
use JetBrains\PhpStorm\ArrayShape;

class DevicesForUserHandler implements DevicesForUserInterface
{
    private DeviceRepository $deviceRepository;

    private UserGroupsFinder $getGroupNamesHandler;

    public function __construct(
        DeviceRepository $deviceRepository,
        UserGroupsFinder $getGroupNamesHandler
    ) {
        $this->deviceRepository = $deviceRepository;
        $this->getGroupNamesHandler = $getGroupNamesHandler;
    }

    #[ArrayShape([Devices::class])]
    public function getDevicesForUser(
        User $user,
        GetDeviceDTO $getDeviceDTO,
    ): array {
        $groupNamesForUser = $this->getGroupNamesHandler->getUsersGroups($user);

        return $this->deviceRepository->findAllDevicesByGroupNamePaginated(
            $groupNamesForUser,
            $getDeviceDTO->getLimit(),
            $getDeviceDTO->getOffset(),
        );
    }
}
