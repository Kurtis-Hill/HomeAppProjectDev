<?php

namespace App\Builders\UserInterface\UserData;

use App\Builders\User\GroupName\GroupResponseDTOBuilder;
use App\Builders\User\RoomDTOBuilder\RoomResponseDTOBuilder;

class UserDataDTOBuilder
{
    public static function buildUserDataDTOBuilder(
        array $userRooms,
        array $userGroups,
    ): \App\DTOs\UserInterface\Response\UserData\UserDataResponseDTO {
        foreach ($userRooms as $userRoom) {
            $userRoomDTOs[] = RoomResponseDTOBuilder::buildRoomResponseDTO(
                $userRoom
            );
        }

        foreach ($userGroups as $userGroup) {
            $userGroupDTOs[] = GroupResponseDTOBuilder::buildGroupNameResponseDTO(
                $userGroup
            );
        }

        return new \App\DTOs\UserInterface\Response\UserData\UserDataResponseDTO(
            $userRoomDTOs ?? [],
                $userGroupDTOs ?? [],
        );
    }
}
