<?php

namespace App\Builders\UserInterface\UserData;

use App\Builders\User\GroupName\GroupResponseDTOBuilder;
use App\Builders\User\RoomDTOBuilder\RoomResponseDTOBuilder;
use App\DTOs\UserInterface\Response\UserData\UserDataResponseDTO;

class UserDataDTOBuilder
{
    public static function buildUserDataDTOBuilder(
        array $userRooms,
        array $userGroups,
    ): UserDataResponseDTO {
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

        return new UserDataResponseDTO(
            $userRoomDTOs ?? [],
                $userGroupDTOs ?? [],
        );
    }
}
