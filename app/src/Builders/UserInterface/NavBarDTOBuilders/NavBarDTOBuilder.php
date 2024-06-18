<?php

namespace App\Builders\UserInterface\NavBarDTOBuilders;

use App\DTOs\UserInterface\Response\NavBar\NavBarResponseDTO;

class NavBarDTOBuilder
{
    public function buildNavBarResponseDTO(
        string $header,
        string $icon,
        string $itemName,
        array $listLinksToBeMade = [],
        array $errors = [],
    ): NavBarResponseDTO {
//        if (!empty($listLinksToBeMade)) {
//            $listLinksToBeMade = array_map(static function (array $listLink) use ($urlBase) {
//                return sprintf('%s?%s', $urlBase, http_build_query($listLink));
//            }, $listLinksToBeMade);
//        }

        return new NavBarResponseDTO(
            $header,
            $icon,
            $itemName,
            $listLinksToBeMade,
            $errors,
        );
//        foreach ($listLinksToBeMade as $link) {
//
//        }
    }
//    public static function buildNavBarResponseDTO(
//        array $userRooms,
//        array $userDevices,
//        array $groupNames,
//    ): NavBarResponseDTO {
//        try {
//            foreach ($userRooms as $room) {
//                $roomDTOs[] = RoomResponseDTOBuilder::buildRoomResponseDTO($room);
//            }
//        } catch (TypeError) {
//            $errors[] = sprintf(APIErrorMessages::FAILED_TO_PREPARE_OBJECT_RESPONSE, 'room');
//        }
//
//        try {
//            foreach ($userDevices as $device) {
//                $deviceDTOs[] = DeviceUpdateResponseDTOBuilder::buildDeviceIDResponseDTO($device);
//            }
//        } catch (TypeError) {
//            $errors[] = sprintf(APIErrorMessages::FAILED_TO_PREPARE_OBJECT_RESPONSE, 'device');
//        }
//
//        try {
//            foreach ($groupNames as $groupName) {
//                $groupNameDTOs[] = GroupNameResponseDTOBuilder::buildGroupNameResponseDTO($groupName);
//            }
//        } catch (TypeError) {
//            $errors[] = sprintf(APIErrorMessages::FAILED_TO_PREPARE_OBJECT_RESPONSE, 'group name');
//        }
//
//        return new NavBarResponseDTO(
//            $roomDTOs ?? ['No Rooms Available'],
//            $deviceDTOs ?? ['No Devices Available'],
//            $groupNameDTOs ?? ['No Groupnames Available'],
//            $errors ?? []
//        );
//    }
}
