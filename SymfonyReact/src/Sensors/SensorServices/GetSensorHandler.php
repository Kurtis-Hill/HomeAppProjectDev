<?php

namespace App\Sensors\SensorServices;

use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\DTO\Internal\Sensor\GetSensorQueryDTO;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Services\GroupNameServices\GetGroupNamesHandler;
use JetBrains\PhpStorm\ArrayShape;

class GetSensorHandler
{
    private DeviceRepositoryInterface $deviceRepository;

    private GetGroupNamesHandler $getGroupNamesHandler;

    public function __construct(
        DeviceRepositoryInterface $deviceRepository,
        GetGroupNamesHandler $getGroupNamesHandler,
    ) {
        $this->deviceRepository = $deviceRepository;
        $this->getGroupNamesHandler = $getGroupNamesHandler;
    }

    #[ArrayShape(['errors'])]
    public function validateUserIsAllowedToGetSensors(GetSensorQueryDTO $getSensorQueryDTO, User $user): array
    {
        $allGroupsUserIsApartOf = $this->getGroupNamesHandler->getGroupNamesForUser($user);
        $groupNameIDs = array_map(static function($groupName) {
            /** @var GroupNames $groupName */
            return $groupName->getGroupNameID();
        }, $allGroupsUserIsApartOf);

        $allDevicesUserHasAccessTo = $this->deviceRepository->findAllDevicesByGroupNameIDs($groupNameIDs);
        $usersDeviceIDs = [];

        $userDeviceNames = [];
        /** @var Devices $device */
        foreach ($allDevicesUserHasAccessTo as $device) {
            $usersDeviceIDs[] = $device->getDeviceID();
            $userDeviceNames[] = $device->getDeviceName();
        }

        $requestedGroupsNameIds = $getSensorQueryDTO->getGroupIDs();
        $requestedDeviceNames = $getSensorQueryDTO->getDeviceNames();
        $requestedDeviceIDs = $getSensorQueryDTO->getDeviceIDs();

        $errors = [];

        if (!empty($requestedGroupsNameIds) && !in_array($requestedGroupsNameIds, $allGroupsUserIsApartOf, true)) {
            $groupNamesIDsUserHasAccessTo = array_filter($requestedGroupsNameIds, static function($groupName) use ($groupNameIDs, &$errors) {
                if (in_array($groupName, $groupNameIDs, false)) {
                    return true;
                }
                $errors[] = 'User does not have access to requested group: ' . $groupName;

                return false;
            });
            $getSensorQueryDTO->setGroupIDs($groupNamesIDsUserHasAccessTo);
        }

        if (!empty($requestedDeviceNames) && !in_array($requestedDeviceNames, $userDeviceNames, true)) {
            $deviceNamesUserHasAccessTo = array_filter($requestedDeviceNames, static function($deviceName) use ($userDeviceNames, &$errors) {
                if (in_array($deviceName, $userDeviceNames, false)) {
                    return true;
                }
                $errors[] = 'User does not have access to requested device: ' . $deviceName;

                return false;
            });
            $getSensorQueryDTO->setDeviceNames($deviceNamesUserHasAccessTo);
        }

        if (!empty($requestedDeviceIDs) && !in_array($requestedDeviceIDs, $usersDeviceIDs, true)) {
            $deviceIDsUserHasAccessTo = array_filter($requestedDeviceIDs, static function($deviceID) use ($usersDeviceIDs, &$errors) {
                if (in_array($deviceID, $usersDeviceIDs, false)) {
                    return true;
                }
                $errors[] = 'User does not have access to requested device: ' . $deviceID;

                return false;
            });
            $getSensorQueryDTO->setDeviceIDs($deviceIDsUserHasAccessTo);
        }

        return $errors;
    }
}
