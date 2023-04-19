<?php

namespace App\Sensors\SensorServices;

use App\Common\API\APIErrorMessages;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\DTO\Internal\Sensor\GetSensorQueryDTO;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Services\GroupNameServices\UserGroupsFinder;
use JetBrains\PhpStorm\ArrayShape;

class GetSensorHandler
{
    private DeviceRepositoryInterface $deviceRepository;

    private UserGroupsFinder $getGroupNamesHandler;

    public function __construct(
        DeviceRepositoryInterface $deviceRepository,
        UserGroupsFinder $getGroupNamesHandler,
    ) {
        $this->deviceRepository = $deviceRepository;
        $this->getGroupNamesHandler = $getGroupNamesHandler;
    }

    #[ArrayShape(['errors'])]
    public function validateUserIsAllowedToGetSensors(GetSensorQueryDTO $getSensorQueryDTO, User $user): array
    {
        $usersDeviceIDs = [];
        $userDeviceNames = [];

        $requestedDeviceNames = $getSensorQueryDTO->getDeviceNames();
        $requestedDeviceIDs = $getSensorQueryDTO->getDeviceIDs();
        $groupID = $this->getGroupNamesHandler->getGroupNamesForUser($user);
        if (!empty($requestedDeviceNames) || !empty($requestedDeviceIDs)) {
            $allDevicesUserHasAccessTo = $this->deviceRepository->findAllDevicesByGroupIDs($groupID);
            /** @var Devices $device */
            foreach ($allDevicesUserHasAccessTo as $device) {
                $usersDeviceIDs[] = $device->getDeviceID();
                $userDeviceNames[] = $device->getDeviceName();
            }
        }

        $errors = [];

        $requestedGroupsNameIds = $getSensorQueryDTO->getGroupIDs();
        if (!empty($requestedGroupsNameIds)) {
            $allGroupsUserIsApartOf = $this->getGroupNamesHandler->getGroupNamesForUser($user);
            $groupID = array_map(static function($groupName) {
                /** @var GroupNames $groupName */
                return $groupName->getGroupID();
            }, $allGroupsUserIsApartOf);
            if (!in_array($requestedGroupsNameIds, $allGroupsUserIsApartOf, true)) {
                $groupNamesIDsUserHasAccessTo = array_filter($requestedGroupsNameIds, static function($groupName) use ($groupID, &$errors) {
                    if (in_array($groupName, $groupID, false)) {
                        return true;
                    }
                    $errors[] = sprintf(
                        APIErrorMessages::USER_DOES_NOT_HAVE_ACCESS_TO_REQUESTED,
                        'group',
                        $groupName
                    );

                    return false;
                });
                $getSensorQueryDTO->setGroupIDs($groupNamesIDsUserHasAccessTo);
            }
        }


        if (!empty($requestedDeviceNames) && !in_array($requestedDeviceNames, $userDeviceNames, true)) {
            $deviceNamesUserHasAccessTo = array_filter($requestedDeviceNames, static function($deviceName) use ($userDeviceNames, &$errors) {
                if (in_array($deviceName, $userDeviceNames, false)) {
                    return true;
                }
                $errors[] = sprintf(
                    APIErrorMessages::USER_DOES_NOT_HAVE_ACCESS_TO_REQUESTED,
                    'device',
                    $deviceName
                );

                return false;
            });
            $getSensorQueryDTO->setDeviceNames($deviceNamesUserHasAccessTo);
        }

        if (!empty($requestedDeviceIDs) && !in_array($requestedDeviceIDs, $usersDeviceIDs, true)) {
            $deviceIDsUserHasAccessTo = array_filter($requestedDeviceIDs, static function($deviceID) use ($usersDeviceIDs, &$errors) {
                if (in_array($deviceID, $usersDeviceIDs, false)) {
                    return true;
                }
                $errors[] = sprintf(
                    APIErrorMessages::USER_DOES_NOT_HAVE_ACCESS_TO_REQUESTED,
                    'device',
                    $deviceID
                );

                return false;
            });
            $getSensorQueryDTO->setDeviceIDs($deviceIDsUserHasAccessTo);
        }

        return $errors;
    }
}
