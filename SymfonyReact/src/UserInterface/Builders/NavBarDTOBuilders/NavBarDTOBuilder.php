<?php

namespace App\UserInterface\Builders\NavBarDTOBuilders;

use App\Common\API\APIErrorMessages;
use App\Devices\Builders\DeviceUpdate\DeviceUpdateResponseDTOBuilder;
use App\User\Builders\GroupName\GroupNameResponseDTOBuilder;
use App\User\Builders\RoomDTOBuilder\RoomResponseDTOBuilder;
use App\UserInterface\DTO\Response\NavBar\NavBarResponseDTO;
use TypeError;

class NavBarDTOBuilder
{
    public static function buildNavBarResponseDTO(
        array $userRooms,
        array $userDevices,
        array $groupNames,
        array $errors,
    ): NavBarResponseDTO {
        try {
            foreach ($userRooms as $room) {
                $roomDTOs[] = RoomResponseDTOBuilder::buildRoomResponseDTO($room);
            }
        } catch (TypeError) {
            $errors[] = sprintf(APIErrorMessages::FAILED_TO_PREPARE_OBJECT_RESPONSE, 'room');
        }

        try {
            foreach ($userDevices as $device) {
                $deviceDTOs[] = DeviceUpdateResponseDTOBuilder::buildDeviceIDResponseDTO($device);
            }
        } catch (TypeError) {
            $errors[] = sprintf(APIErrorMessages::FAILED_TO_PREPARE_OBJECT_RESPONSE, 'device');
        }

        try {
            foreach ($groupNames as $groupName) {
                $groupNameDTOs[] = GroupNameResponseDTOBuilder::buildGroupNameResponseDTO($groupName);
            }
        } catch (TypeError) {
            $errors[] = sprintf(APIErrorMessages::FAILED_TO_PREPARE_OBJECT_RESPONSE, 'group name');
        }

        return new NavBarResponseDTO(
            $roomDTOs ?? ['No Rooms Available'],
            $deviceDTOs ?? ['No Devices Available'],
            $groupNameDTOs ?? ['No Groupnames Available'],
            $errors
        );
    }
}
